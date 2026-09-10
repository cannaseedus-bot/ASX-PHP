<?php
// src/JsonRpcServer.php

class JsonRpcServer {
    private $methods = [];
    private $notifications = [];
    private static $instance = null;
    
    private function __construct() {}
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Register a method with the RPC server
     */
    public function registerMethod($method, callable $handler) {
        $this->methods[$method] = $handler;
        return $this;
    }
    
    /**
     * Register a notification (no response expected)
     */
    public function registerNotification($method, callable $handler) {
        $this->notifications[$method] = $handler;
        return $this;
    }
    
    /**
     * Handle a JSON-RPC request
     */
    public function handle($request) {
        // Parse request
        if (is_string($request)) {
            $request = json_decode($request, true);
        }
        
        // Handle batch requests
        if (is_array($request) && isset($request[0]) && is_array($request[0])) {
            return $this->handleBatch($request);
        }
        
        return $this->handleSingle($request);
    }
    
    /**
     * Handle a single request
     */
    private function handleSingle($request) {
        // Validate request
        $error = $this->validateRequest($request);
        if ($error) {
            return $error;
        }
        
        $method = $request['method'];
        $params = $request['params'] ?? [];
        $id = $request['id'] ?? null;
        $isNotification = !array_key_exists('id', $request);
        
        // Check if this is a notification or method
        if ($isNotification) {
            return $this->handleNotification($method, $params);
        }
        
        // Check if method exists
        if (!isset($this->methods[$method]) && !isset($this->notifications[$method])) {
            return [
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32601,
                    'message' => 'Method not found'
                ],
                'id' => $id
            ];
        }
        
        try {
            // Execute method
            $handler = $this->methods[$method] ?? $this->notifications[$method];
            $result = call_user_func($handler, $params);
            
            // Return response for regular method calls
            if (isset($this->methods[$method])) {
                return [
                    'jsonrpc' => '2.0',
                    'result' => $result,
                    'id' => $id
                ];
            }
            
            // Notifications don't return responses
            return null;
            
        } catch (Exception $e) {
            return [
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32000,
                    'message' => $e->getMessage()
                ],
                'id' => $id
            ];
        }
    }
    
    /**
     * Handle batch requests
     */
    private function handleBatch($requests) {
        $responses = [];
        foreach ($requests as $request) {
            $response = $this->handleSingle($request);
            if ($response !== null) {
                $responses[] = $response;
            }
        }
        return $responses;
    }
    
    /**
     * Handle a notification
     */
    private function handleNotification($method, $params) {
        if (!isset($this->notifications[$method])) {
            // No handler for notification, just ignore
            return null;
        }
        
        try {
            call_user_func($this->notifications[$method], $params);
        } catch (Exception $e) {
            // Silently ignore errors for notifications
            error_log("Notification error: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Validate a JSON-RPC request
     */
    private function validateRequest($request) {
        if (!is_array($request)) {
            return [
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32700,
                    'message' => 'Parse error'
                ],
                'id' => null
            ];
        }
        
        if (!isset($request['jsonrpc']) || $request['jsonrpc'] !== '2.0') {
            return [
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32600,
                    'message' => 'Invalid Request'
                ],
                'id' => $request['id'] ?? null
            ];
        }
        
        if (!isset($request['method']) || !is_string($request['method'])) {
            return [
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32600,
                    'message' => 'Invalid Request'
                ],
                'id' => $request['id'] ?? null
            ];
        }
        
        // Check params if present
        if (isset($request['params']) && !is_array($request['params'])) {
            return [
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => -32600,
                    'message' => 'Invalid Request'
                ],
                'id' => $request['id'] ?? null
            ];
        }
        
        return null;
    }
}