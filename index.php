<?php
// public/index.php

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Include all classes
spl_autoload_register(function($class) {
    $file = __DIR__ . '/../src/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Ensure cache and logs directories exist
$cacheDir = __DIR__ . '/../cache';
$logsDir = __DIR__ . '/../logs';
foreach ([$cacheDir, $logsDir] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get request data
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$input = json_decode(file_get_contents('php://input'), true);
$query = $_GET;

// Initialize router
$router = Router::getInstance();

// Register some REST routes
$router->get('/', function($input) {
    return [
        'service' => 'PHP Runtime',
        'version' => '1.0.0',
        'endpoints' => [
            'rest' => '/api/v1/*',
            'rpc' => '/api/rpc',
            'mcp' => '/mcp',
            'health' => '/health'
        ]
    ];
});

$router->get('/health', function($input) {
    return [
        'status' => 'ok',
        'timestamp' => date('c'),
        'cache' => Cache::getInstance()->stats()
    ];
});

// Example REST endpoint: get cache stats
$router->get('/api/v1/cache/stats', function($input) {
    return Cache::getInstance()->stats();
});

// Example REST endpoint: clear cache
$router->delete('/api/v1/cache', function($input) {
    return ['cleared' => Cache::getInstance()->clear()];
});

// Example REST endpoint: DNS lookup
$router->get('/api/v1/dns', function($input) {
    $domain = $_GET['domain'] ?? $_GET['d'] ?? null;
    $type = $_GET['type'] ?? 'A';
    if (!$domain) {
        return ['error' => 'Domain parameter required'];
    }
    return DnsResolver::getInstance()->resolve($domain, $type);
});

// Register MCP tools
$mcp = McpServer::getInstance();

$mcp->registerTool('echo', 'Echo back a message', function($params) {
    return ['echo' => $params['message'] ?? 'No message provided'];
}, ['message' => ['type' => 'string', 'description' => 'Message to echo']]);

$mcp->registerTool('dns_lookup', 'Look up DNS records', function($params) {
    $domain = $params['domain'] ?? null;
    if (!$domain) {
        throw new Exception('Domain required');
    }
    return DnsResolver::getInstance()->resolve($domain, $params['type'] ?? 'A');
}, [
    'domain' => ['type' => 'string', 'description' => 'Domain to look up'],
    'type' => ['type' => 'string', 'description' => 'Record type (A, AAAA, MX, etc.)']
]);

$mcp->registerResource('info://server', 'Server Info', function() {
    return json_encode([
        'php_version' => phpversion(),
        'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'time' => date('c')
    ], JSON_PRETTY_PRINT);
}, 'application/json');

$mcp->registerResource('info://cache', 'Cache Stats', function() {
    return json_encode(Cache::getInstance()->stats(), JSON_PRETTY_PRINT);
}, 'application/json');

$mcp->registerPrompt('greeting', 'A greeting prompt', function($params) {
    $name = $params['name'] ?? 'World';
    return "Hello, {$name}! Welcome to the PHP Runtime.";
}, ['name' => ['type' => 'string', 'description' => 'Name to greet']]);

// Dispatch the request
$response = $router->dispatch($method, $uri, $input);

// Send response
if ($response !== null) {
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
}

// Log request
error_log(sprintf(
    "[%s] %s %s - %s",
    date('Y-m-d H:i:s'),
    $method,
    $uri,
    http_response_code()
));