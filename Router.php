<?php
// src/Router.php

class Router {
    private $routes = [];
    private $middleware = [];
    private $config;
    private $jsonRpcServer;
    private $mcpServer;
    private static $instance = null;
    
    private function __construct() {
        $this->config = Config::getInstance();
        $this->jsonRpcServer = JsonRpcServer::getInstance();
        $this->mcpServer = McpServer::getInstance();
        
        // Register default RPC methods
        $this->registerDefaultRpcMethods();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Register a route
     */
    public function add($method, $path, callable $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->normalizePath($path),
            'handler' => $handler
        ];
        return $this;
    }
    
    /**
     * Convenience methods for HTTP verbs
     */
    public function get($path, callable $handler) {
        return $this->add('GET', $path, $handler);
    }
    
    public function post($path, callable $handler) {
        return $this->add('POST', $path, $handler);
    }
    
    public function put($path, callable $handler) {
        return $this->add('PUT', $path, $handler);
    }
    
    public function delete($path, callable $handler) {
        return $this->add('DELETE', $path, $handler);
    }
    
    /**
     * Register middleware
     */
    public function middleware(callable $middleware) {
        $this->middleware[] = $middleware;
        return $this;
    }
    
    /**
     * Dispatch the request
     */
    public function dispatch($method, $uri, $input = null) {
        $path = $this->normalizePath(parse_url($uri, PHP_URL_PATH));
        
        // Run middleware
        $context = ['method' => $method, 'path' => $path, 'input' => $input];
        foreach ($this->middleware as $mw) {
            $result = call_user_func($mw, $context);
            if ($result !== null && is_array($result)) {
                return $result;
            }
        }
        
        // Check REST prefix
        $restPrefix = $this->config->get('rest_prefix');
        if (strpos($path, $restPrefix) === 0) {
            return $this->handleRest($method, $path, $input);
        }
        
        // Check RPC prefix
        $rpcPrefix = $this->config->get('rpc_prefix');
        if (strpos($path, $rpcPrefix) === 0) {
            return $this->handleRpc($input);
        }
        
        // Check MCP prefix
        $mcpPrefix = $this->config->get('mcp_prefix');
        if (strpos($path, $mcpPrefix) === 0) {
            return $this->handleMcp($input);
        }
        
        // Try route matching
        return $this->matchRoute($method, $path, $input);
    }
    
    /**
     * Handle REST API requests
     */
    private function handleRest($method, $path, $input) {
        $result = $this->matchRoute($method, $path, $input);
        if ($result !== null) {
            return $result;
        }
        
        // Default REST handling - serve resource
        return [
            'error' => 'Resource not found',
            'method' => $method,
            'path' => $path
        ];
    }
    
    /**
     * Handle JSON-RPC requests
     */
    private function handleRpc($input) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['error' => 'RPC endpoint requires POST'];
        }
        
        return $this->jsonRpcServer->handle($input);
    }
    
    /**
     * Handle MCP requests
     */
    private function handleMcp($input) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ['error' => 'MCP endpoint requires POST'];
        }
        
        return $this->mcpServer->handleHttpRequest($input);
    }
    
    /**
     * Match a route
     */
    private function matchRoute($method, $path, $input) {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            
            if ($route['path'] === $path) {
                return call_user_func($route['handler'], $input, $method, $path);
            }
        }
        return null;
    }
    
    /**
     * Register default RPC methods
     */
    private function registerDefaultRpcMethods() {
        // System info
        $this->jsonRpcServer->registerMethod('system.info', function($params) {
            return [
                'php_version' => phpversion(),
                'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time')
            ];
        });
        
        // Echo service
        $this->jsonRpcServer->registerMethod('system.echo', function($params) {
            return $params['message'] ?? $params[0] ?? 'No message';
        });
        
        // Ping
        $this->jsonRpcServer->registerMethod('system.ping', function($params) {
            return ['pong' => microtime(true)];
        });
        
        // DNS lookup via RPC
        $this->jsonRpcServer->registerMethod('dns.resolve', function($params) {
            $domain = $params['domain'] ?? $params[0] ?? null;
            if (!$domain) {
                throw new Exception('Domain required');
            }
            $type = $params['type'] ?? 'A';
            $dns = DnsResolver::getInstance();
            return $dns->resolve($domain, $type);
        });
        
        // Cache operations via RPC
        $this->jsonRpcServer->registerMethod('cache.get', function($params) {
            $key = $params['key'] ?? $params[0] ?? null;
            if (!$key) {
                throw new Exception('Cache key required');
            }
            $cache = Cache::getInstance();
            return $cache->get($key);
        });
        
        $this->jsonRpcServer->registerMethod('cache.set', function($params) {
            $key = $params['key'] ?? $params[0] ?? null;
            $value = $params['value'] ?? $params[1] ?? null;
            $ttl = $params['ttl'] ?? $params[2] ?? null;
            if (!$key || $value === null) {
                throw new Exception('Key and value required');
            }
            $cache = Cache::getInstance();
            return $cache->set($key, $value, $ttl);
        });
        
        // Register MCP tools as RPC methods
        $this->jsonRpcServer->registerMethod('mcp.tools', function($params) {
            return ['tools' => McpServer::getInstance()->getTools()];
        });
    }
    
    /**
     * Normalize path
     */
    private function normalizePath($path) {
        // Remove query string
        $path = strtok($path, '?');
        // Remove trailing slash
        $path = rtrim($path, '/');
        // Ensure leading slash
        if (empty($path)) {
            $path = '/';
        } elseif ($path[0] !== '/') {
            $path = '/' . $path;
        }
        return $path;
    }
}