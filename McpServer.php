<?php
// src/McpServer.php

class McpServer {
    private $tools = [];
    private $resources = [];
    private $prompts = [];
    private $transport;
    private $serverInfo;
    private $initialized = false;
    private static $instance = null;
    
    private function __construct() {
        $config = Config::getInstance();
        $this->transport = $config->get('mcp_transport');
        $this->serverInfo = [
            'name' => $config->get('mcp_server_name'),
            'version' => $config->get('mcp_server_version'),
            'protocol_version' => '2025-03-26'
        ];
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Register a tool
     */
    public function registerTool($name, $description, callable $handler, $params = []) {
        $this->tools[$name] = [
            'name' => $name,
            'description' => $description,
            'handler' => $handler,
            'params' => $params,
            'version' => '1.0.0'
        ];
        return $this;
    }
    
    /**
     * Register a resource
     */
    public function registerResource($uri, $name, callable $handler, $mimeType = 'text/plain') {
        $this->resources[$uri] = [
            'uri' => $uri,
            'name' => $name,
            'handler' => $handler,
            'mimeType' => $mimeType
        ];
        return $this;
    }
    
    /**
     * Register a prompt
     */
    public function registerPrompt($name, $description, callable $handler, $params = []) {
        $this->prompts[$name] = [
            'name' => $name,
            'description' => $description,
            'handler' => $handler,
            'params' => $params
        ];
        return $this;
    }
    
    /**
     * Handle MCP request (HTTP mode)
     */
    public function handleHttpRequest($request) {
        // Parse the MCP message
        $message = json_decode($request, true);
        if (!$message || !isset($message['type'])) {
            return $this->errorResponse('Invalid MCP message');
        }
        
        return $this->processMessage($message);
    }
    
    /**
     * Handle MCP request (stdio mode)
     */
    public function handleStdio() {
        // Read from stdin
        $input = file_get_contents('php://stdin');
        if (empty($input)) {
            return;
        }
        
        $message = json_decode($input, true);
        if (!$message || !isset($message['type'])) {
            $this->sendStdioResponse($this->errorResponse('Invalid MCP message'));
            return;
        }
        
        $response = $this->processMessage($message);
        $this->sendStdioResponse($response);
    }
    
    /**
     * Process an MCP message
     */
    private function processMessage($message) {
        $type = $message['type'];
        
        switch ($type) {
            case 'initialize':
                return $this->handleInitialize($message);
                
            case 'tools/list':
                return $this->handleToolsList($message);
                
            case 'tools/call':
                return $this->handleToolsCall($message);
                
            case 'resources/list':
                return $this->handleResourcesList($message);
                
            case 'resources/read':
                return $this->handleResourcesRead($message);
                
            case 'prompts/list':
                return $this->handlePromptsList($message);
                
            case 'prompts/get':
                return $this->handlePromptsGet($message);
                
            default:
                return $this->errorResponse("Unknown MCP message type: {$type}");
        }
    }
    
    /**
     * Handle initialization
     */
    private function handleInitialize($message) {
        $this->initialized = true;
        
        return [
            'type' => 'initialize_response',
            'server_info' => $this->serverInfo,
            'capabilities' => [
                'tools' => !empty($this->tools),
                'resources' => !empty($this->resources),
                'prompts' => !empty($this->prompts),
                'streaming' => false
            ]
        ];
    }
    
    /**
     * Handle tools/list
     */
    private function handleToolsList($message) {
        $tools = [];
        foreach ($this->tools as $name => $tool) {
            $tools[] = [
                'name' => $name,
                'description' => $tool['description'],
                'parameters' => $tool['params'],
                'version' => $tool['version'] ?? '1.0.0'
            ];
        }
        
        return [
            'type' => 'tools/list_response',
            'tools' => $tools
        ];
    }
    
    /**
     * Handle tools/call
     */
    private function handleToolsCall($message) {
        $toolName = $message['name'] ?? '';
        $params = $message['parameters'] ?? [];
        
        if (!isset($this->tools[$toolName])) {
            return $this->errorResponse("Tool not found: {$toolName}");
        }
        
        try {
            $handler = $this->tools[$toolName]['handler'];
            $result = call_user_func($handler, $params);
            
            return [
                'type' => 'tools/call_response',
                'result' => $result,
                'isError' => false
            ];
        } catch (Exception $e) {
            return [
                'type' => 'tools/call_response',
                'result' => ['error' => $e->getMessage()],
                'isError' => true
            ];
        }
    }
    
    /**
     * Handle resources/list
     */
    private function handleResourcesList($message) {
        $resources = [];
        foreach ($this->resources as $uri => $resource) {
            $resources[] = [
                'uri' => $uri,
                'name' => $resource['name'],
                'mimeType' => $resource['mimeType']
            ];
        }
        
        return [
            'type' => 'resources/list_response',
            'resources' => $resources
        ];
    }
    
    /**
     * Handle resources/read
     */
    private function handleResourcesRead($message) {
        $uri = $message['uri'] ?? '';
        
        if (!isset($this->resources[$uri])) {
            return $this->errorResponse("Resource not found: {$uri}");
        }
        
        try {
            $handler = $this->resources[$uri]['handler'];
            $result = call_user_func($handler);
            
            return [
                'type' => 'resources/read_response',
                'contents' => [
                    [
                        'uri' => $uri,
                        'mimeType' => $this->resources[$uri]['mimeType'],
                        'text' => $result
                    ]
                ]
            ];
        } catch (Exception $e) {
            return $this->errorResponse("Error reading resource: " . $e->getMessage());
        }
    }
    
    /**
     * Handle prompts/list
     */
    private function handlePromptsList($message) {
        $prompts = [];
        foreach ($this->prompts as $name => $prompt) {
            $prompts[] = [
                'name' => $name,
                'description' => $prompt['description'],
                'parameters' => $prompt['params']
            ];
        }
        
        return [
            'type' => 'prompts/list_response',
            'prompts' => $prompts
        ];
    }
    
    /**
     * Handle prompts/get
     */
    private function handlePromptsGet($message) {
        $name = $message['name'] ?? '';
        $params = $message['parameters'] ?? [];
        
        if (!isset($this->prompts[$name])) {
            return $this->errorResponse("Prompt not found: {$name}");
        }
        
        try {
            $handler = $this->prompts[$name]['handler'];
            $result = call_user_func($handler, $params);
            
            return [
                'type' => 'prompts/get_response',
                'prompt' => $result
            ];
        } catch (Exception $e) {
            return $this->errorResponse("Error getting prompt: " . $e->getMessage());
        }
    }
    
    /**
     * Create an error response
     */
    private function errorResponse($message, $code = -1) {
        return [
            'type' => 'error',
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ];
    }
    
    /**
     * Send response via stdio
     */
    private function sendStdioResponse($response) {
        echo json_encode($response) . "\n";
        flush();
    }
    
    /**
     * Get registered tools (for REST API)
     */
    public function getTools() {
        return array_keys($this->tools);
    }
    
    /**
     * Get registered resources
     */
    public function getResources() {
        return array_keys($this->resources);
    }
}