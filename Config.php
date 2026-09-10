<?php
// src/Config.php

class Config {
    private static $instance = null;
    private $config = [];
    
    private function __construct() {
        $this->config = [
            // Cache settings
            'cache_dir' => __DIR__ . '/../cache',
            'cache_ttl' => 3600, // 1 hour default
            
            // DNS settings
            'dns_ttl' => 300, // 5 minutes
            'dns_nameservers' => ['8.8.8.8', '1.1.1.1'],
            
            // API routes
            'rest_prefix' => '/api/v1',
            'rpc_prefix' => '/api/rpc',
            'mcp_prefix' => '/mcp',
            
            // MCP settings
            'mcp_transport' => 'stdio', // or 'http'
            'mcp_server_name' => 'PHP Runtime',
            'mcp_server_version' => '1.0.0',
            
            // Logging
            'log_dir' => __DIR__ . '/../logs',
            'log_level' => 'info',
            
            // Security
            'allowed_origins' => ['*'],
            'rate_limit' => 100, // requests per minute
        ];
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function get($key, $default = null) {
        return $this->config[$key] ?? $default;
    }
    
    public function set($key, $value) {
        $this->config[$key] = $value;
    }
}