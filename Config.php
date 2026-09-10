<?php
/**
 * Config.php - Complete Configuration System
 * 
 * Supports K'UHUL π grammar, Micronaut µ orchestration,
 * PHP runtime, MCP, and all server components.
 * 
 * @package PHP Runtime
 * @version 3.0.0
 * @author K'UHUL π · Micronaut µ
 * @license MIT
 */

declare(strict_types=1);

// ============================================================
// 1. CONFIGURATION INTERFACE
// ============================================================

/**
 * Config Interface - Defines all available configuration options
 */
interface ConfigInterface {
    public function get(string $key, $default = null);
    public function set(string $key, $value): void;
    public function has(string $key): bool;
    public function all(): array;
    public function load(array $config): void;
    public function merge(array $config): void;
    public function save(string $path): bool;
    public function loadFromFile(string $path): bool;
}

// ============================================================
// 2. MAIN CONFIGURATION CLASS
// ============================================================

class Config implements ConfigInterface {
    private static ?Config $instance = null;
    private array $config = [];
    private array $loadedFiles = [];
    private array $validationRules = [];
    private array $defaults = [];
    
    /**
     * Private constructor (Singleton pattern)
     */
    private function __construct() {
        $this->defaults = $this->getDefaults();
        $this->config = $this->defaults;
        $this->validationRules = $this->getValidationRules();
        $this->loadEnvironment();
        $this->loadKuhulGrammar();
        $this->loadMicronautGrammar();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get default configuration
     */
    private function getDefaults(): array {
        return [
            // ============================================================
            // SERVER CONFIGURATION
            // ============================================================
            'server' => [
                'host' => '0.0.0.0',
                'port' => 8080,
                'document_root' => __DIR__ . '/../public',
                'timeout' => 30,
                'max_connections' => 100,
                'keep_alive' => true,
                'keep_alive_timeout' => 30,
            ],
            
            // ============================================================
            // K'UHUL π CONFIGURATION
            // ============================================================
            'kuhul' => [
                'enabled' => true,
                'phase' => 'Pop', // Pop, Wo, Yax, Sek, Ch'en, Xul, Noj
                'enforce_on_startup' => true,
                'default_law' => 'collapse_only',
                'laws' => [
                    'collapse_only' => [
                        'phases' => ['Pop', 'Wo', 'Yax', 'Sek', "Ch'en", 'Xul'],
                        'invariants' => [
                            'collapse_only' => true,
                            'field_perception' => true,
                            'compression_law' => true,
                            'unreachable_states' => true,
                        ],
                        'description' => 'Only Xul can collapse state',
                        'version' => '1.0.0'
                    ],
                    'perception_field' => [
                        'phases' => ['Pop', 'Wo', 'Xul'],
                        'invariants' => [
                            'field_perception' => true,
                            'collapse_only' => true,
                        ],
                        'description' => 'Field perception law',
                        'version' => '1.0.0'
                    ],
                    'pure_enforcement' => [
                        'phases' => ['Pop', 'Sek', 'Xul'],
                        'invariants' => [
                            'collapse_only' => true,
                            'compression_law' => true,
                        ],
                        'description' => 'Pure enforcement without planning',
                        'version' => '1.0.0'
                    ],
                ],
                'custom_phases' => [
                    // Allow custom phase definitions
                ],
                'invariants' => [
                    'collapse_only' => true,
                    'field_perception' => true,
                    'compression_law' => true,
                    'unreachable_states' => true,
                ],
                'grammar' => [
                    'enabled' => true,
                    'version' => '2.0.0',
                    'strict_mode' => true,
                    'allow_comments' => true,
                    'allow_attributes' => true,
                    'allow_union_types' => true,
                    'allow_intersection_types' => true,
                    'allow_dnf_types' => true,
                    'allow_readonly_classes' => true,
                    'allow_enums' => true,
                    'allow_match_expressions' => true,
                    'allow_arrow_functions' => true,
                ],
                'runtime' => [
                    'max_phase_transitions' => 1000,
                    'enforce_on_phase_change' => true,
                    'auto_consolidate' => false,
                    'track_history' => true,
                    'max_history' => 100,
                ]
            ],
            
            // ============================================================
            // MICRONAUT µ CONFIGURATION
            // ============================================================
            'micronaut' => [
                'enabled' => true,
                'name' => 'PrimaryOrchestrator',
                'role' => 'orchestrator',
                'version' => '3.0.0',
                'status' => 'ready',
                'priority' => 'balanced',
                'entropy_budget' => 0.5,
                'timeout_ms' => 5000,
                'retry_policy' => [
                    'max_attempts' => 3,
                    'backoff_ms' => 1000,
                ],
                'routing' => [
                    'strategy' => 'round_robin',
                    'capability_map' => [],
                ],
                'permissions' => [
                    'fold:execute',
                    'fold:compose',
                    'field:create',
                    'field:read',
                    'field:write',
                    'tool:use',
                    'gram:resolve',
                    'geodesic:traverse',
                ],
                'folds' => [
                    'compute' => [
                        'type' => 'compute',
                        'nodes' => [
                            ['id' => 'input_1', 'type' => 'input'],
                            ['id' => 'process_1', 'type' => 'process'],
                            ['id' => 'output_1', 'type' => 'output'],
                        ]
                    ],
                    'reasoning' => [
                        'type' => 'reasoning',
                        'nodes' => [
                            ['id' => 'perceive', 'type' => 'input'],
                            ['id' => 'analyze', 'type' => 'process'],
                            ['id' => 'decide', 'type' => 'gate'],
                            ['id' => 'act', 'type' => 'dispatch'],
                        ]
                    ],
                    'storage' => [
                        'type' => 'storage',
                        'nodes' => [
                            ['id' => 'cache_read', 'type' => 'input'],
                            ['id' => 'cache_write', 'type' => 'output'],
                        ]
                    ],
                ],
                'fields' => [
                    'working' => [
                        'type' => 'working',
                        'persistence' => 'volatile',
                        'rows' => 0,
                        'cols' => 0,
                    ],
                    'episodic' => [
                        'type' => 'episodic',
                        'persistence' => 'persistent',
                        'rows' => 0,
                        'cols' => 0,
                    ],
                    'semantic' => [
                        'type' => 'semantic',
                        'persistence' => 'persistent',
                        'rows' => 0,
                        'cols' => 0,
                    ],
                    'procedural' => [
                        'type' => 'procedural',
                        'persistence' => 'volatile',
                        'rows' => 0,
                        'cols' => 0,
                    ],
                ],
                'agents' => [
                    'assistant' => [
                        'type' => 'helper',
                        'tools' => ['echo', 'dns_lookup', 'cache_stats'],
                        'goals' => [
                            ['description' => 'Help users with their requests', 'priority' => 1.0]
                        ],
                        'constraints' => [
                            'Be helpful and accurate',
                            'Respect user privacy'
                        ]
                    ],
                    'explorer' => [
                        'type' => 'explorer',
                        'tools' => ['dns_lookup', 'system_info'],
                        'goals' => [
                            ['description' => 'Explore system capabilities', 'priority' => 0.8]
                        ],
                        'constraints' => [
                            'Do not modify system state'
                        ]
                    ],
                    'manager' => [
                        'type' => 'manager',
                        'tools' => ['micronaut_status', 'system_info'],
                        'goals' => [
                            ['description' => 'Manage system resources', 'priority' => 0.9]
                        ],
                        'constraints' => [
                            'Maintain system stability'
                        ]
                    ],
                ],
                'lifecycle' => [
                    'on_before_create' => [],
                    'on_after_create' => [],
                    'on_before_start' => [],
                    'on_after_start' => [],
                    'on_before_stop' => [],
                    'on_after_stop' => [],
                    'on_error' => [],
                ],
                'metrics' => [
                    'enabled' => true,
                    'collect_fold_executions' => true,
                    'collect_field_projections' => true,
                    'collect_gram_resolutions' => true,
                    'collect_traversals' => true,
                    'collect_errors' => true,
                    'collect_latency' => true,
                ],
                'orchestrates' => ['compute', 'reasoning', 'storage'],
            ],
            
            // ============================================================
            // MCP (Model Context Protocol) CONFIGURATION
            // ============================================================
            'mcp' => [
                'enabled' => true,
                'transport' => 'http', // 'http' or 'stdio'
                'server_name' => 'PHP Runtime · K\'UHUL π · Micronaut µ',
                'server_version' => '3.0.0',
                'protocol_version' => '2025-03-26',
                'capabilities' => [
                    'tools' => true,
                    'resources' => true,
                    'prompts' => true,
                    'streaming' => false,
                    'kuhul' => true,
                    'micronaut' => true,
                    'kxml' => true,
                ],
                'tools' => [
                    'echo' => [
                        'description' => 'Echo back a message',
                        'version' => '1.0.0',
                        'enabled' => true,
                    ],
                    'kuhul_perceive' => [
                        'description' => 'K\'UHUL Pop: Perceive input',
                        'version' => '1.0.0',
                        'enabled' => true,
                    ],
                    'kuhul_represent' => [
                        'description' => 'K\'UHUL Wo: Represent symbol',
                        'version' => '1.0.0',
                        'enabled' => true,
                    ],
                    'kuhul_plan' => [
                        'description' => 'K\'UHUL Yax: Plan intention',
                        'version' => '1.0.0',
                        'enabled' => true,
                    ],
                    'kuhul_execute' => [
                        'description' => 'K\'UHUL Sek: Execute action',
                        'version' => '1.0.0',
                        'enabled' => true,
                    ],
                    'kuhul_project' => [
                        'description' => 'K\'UHUL Ch\'en: Project output',
                        'version' => '1.0.0',
                        'enabled' => true,
                    ],
                    'kuhul_consolidate' => [
                        'description' => 'K\'UHUL Xul: Consolidate state',
                        'version' => '1.0.0',
                        'enabled' => true,
                    ],
                    'kuhul_reflect' => [
                        'description' => 'K\'UHUL Noj: Reflect on state',
                        'version' => '1.0.0',
                        'enabled' => true,
                    ],
                    'micronaut_status' => [
                        'description' => 'Get Micronaut status',
                        'version' => '1.0.0',
                        'enabled' => true,
                    ],
                    'micronaut_execute_fold' => [
                        'description' => 'Execute a Micronaut fold',
                        'version' => '1.0.0',
                        'enabled' => true,
                    ],
                    'dns_lookup' => [
                        'description' => 'Look up DNS records',
                        'version' => '1.0.0',
                        'enabled' => true,
                    ],
                    'cache_stats' => [
                        'description' => 'Get cache statistics',
                        'version' => '1.0.0',
                        'enabled' => true,
                    ],
                ],
                'resources' => [
                    'info://server' => [
                        'name' => 'Server Info',
                        'mimeType' => 'application/json',
                        'enabled' => true,
                    ],
                    'info://kuhul' => [
                        'name' => 'K\'UHUL State',
                        'mimeType' => 'application/json',
                        'enabled' => true,
                    ],
                    'info://micronaut' => [
                        'name' => 'Micronaut State',
                        'mimeType' => 'application/json',
                        'enabled' => true,
                    ],
                ],
                'prompts' => [
                    'kuhul_phase' => [
                        'description' => 'K\'UHUL phase guidance',
                        'enabled' => true,
                    ],
                    'micronaut_orchestrate' => [
                        'description' => 'Micronaut orchestration guidance',
                        'enabled' => true,
                    ],
                    'system_status' => [
                        'description' => 'System status prompt',
                        'enabled' => true,
                    ],
                ],
            ],
            
            // ============================================================
            // PHP GRAMMAR CONFIGURATION
            // ============================================================
            'php' => [
                'enabled' => true,
                'version' => '8.2',
                'strict_mode' => true,
                'error_reporting' => E_ALL,
                'display_errors' => false,
                'log_errors' => true,
                'error_log' => __DIR__ . '/../logs/php_errors.log',
                'memory_limit' => '128M',
                'max_execution_time' => 30,
                'max_input_time' => 60,
                'upload_max_filesize' => '2M',
                'post_max_size' => '8M',
                'date_timezone' => 'UTC',
                'grammar' => [
                    'enabled' => true,
                    'version' => '1.0.0',
                    'allow_short_tags' => false,
                    'allow_asp_tags' => false,
                    'allow_attributes' => true,
                    'allow_union_types' => true,
                    'allow_intersection_types' => true,
                    'allow_dnf_types' => true,
                    'allow_readonly_classes' => true,
                    'allow_enums' => true,
                    'allow_match_expressions' => true,
                    'allow_arrow_functions' => true,
                    'allow_named_arguments' => true,
                    'allow_trailing_comma' => true,
                    'allow_fibers' => true,
                ],
                'extensions' => [
                    'json' => true,
                    'mbstring' => true,
                    'openssl' => true,
                    'curl' => true,
                    'pdo' => true,
                    'pdo_mysql' => true,
                    'pdo_pgsql' => true,
                    'pdo_sqlite' => true,
                    'session' => true,
                    'fileinfo' => true,
                    'gd' => true,
                    'exif' => true,
                    'xml' => true,
                    'simplexml' => true,
                    'dom' => true,
                    'xsl' => true,
                ],
            ],
            
            // ============================================================
            // CACHE CONFIGURATION
            // ============================================================
            'cache' => [
                'enabled' => true,
                'driver' => 'file', // 'file', 'apcu', 'redis', 'memcached'
                'directory' => __DIR__ . '/../cache',
                'ttl' => 3600, // Default TTL in seconds
                'prefix' => 'kuhul_',
                'compress' => false,
                'serialize' => true,
                'redis' => [
                    'host' => '127.0.0.1',
                    'port' => 6379,
                    'database' => 0,
                    'password' => null,
                    'timeout' => 2.5,
                ],
                'memcached' => [
                    'host' => '127.0.0.1',
                    'port' => 11211,
                    'weight' => 100,
                ],
                'apcu' => [
                    'ttl' => 3600,
                ],
            ],
            
            // ============================================================
            // DNS CONFIGURATION
            // ============================================================
            'dns' => [
                'enabled' => true,
                'ttl' => 300, // 5 minutes
                'nameservers' => ['8.8.8.8', '1.1.1.1', '9.9.9.9'],
                'timeout' => 5,
                'retries' => 3,
                'cache_enabled' => true,
                'cache_ttl' => 300,
            ],
            
            // ============================================================
            // LOGGING CONFIGURATION
            // ============================================================
            'logging' => [
                'enabled' => true,
                'level' => 'info', // debug, info, warning, error, critical
                'directory' => __DIR__ . '/../logs',
                'files' => [
                    'server' => 'server.log',
                    'kuhul' => 'kuhul.log',
                    'micronaut' => 'micronaut.log',
                    'mcp' => 'mcp.log',
                    'php' => 'php.log',
                    'error' => 'error.log',
                ],
                'format' => '[%timestamp%] [%glyph%] [%domain%] [%level%] %message%',
                'date_format' => 'Y-m-d H:i:s.u',
                'rotation' => [
                    'enabled' => true,
                    'size' => 10485760, // 10MB
                    'max_files' => 5,
                ],
                'handlers' => [
                    'console' => true,
                    'file' => true,
                    'syslog' => false,
                ],
            ],
            
            // ============================================================
            // SECURITY CONFIGURATION
            // ============================================================
            'security' => [
                'cors' => [
                    'enabled' => true,
                    'allowed_origins' => ['*'],
                    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                    'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
                    'exposed_headers' => [],
                    'max_age' => 3600,
                    'credentials' => false,
                ],
                'rate_limit' => [
                    'enabled' => true,
                    'requests_per_minute' => 100,
                    'burst' => 10,
                    'storage' => 'cache',
                ],
                'auth' => [
                    'enabled' => false,
                    'type' => 'basic', // basic, bearer, api_key
                    'users' => [],
                    'api_keys' => [],
                ],
                'ssl' => [
                    'enabled' => false,
                    'cert_file' => '',
                    'key_file' => '',
                    'ca_file' => '',
                ],
                'input_validation' => [
                    'enabled' => true,
                    'max_input_vars' => 1000,
                    'max_post_size' => '8M',
                ],
            ],
            
            // ============================================================
            // EXTRAPOLATOR CONFIGURATION
            // ============================================================
            'extrapolator' => [
                'enabled' => true,
                'projection_space' => [
                    'metaphor' => true,
                    'analogy' => true,
                    'framing' => true,
                    'discipline' => true,
                    'perspective' => true,
                    'caveat' => true,
                    'philosophy' => true,
                ],
                'invariants' => [
                    'read_only_collapse_result' => true,
                    'non_authoritative_output' => true,
                    'no_contradiction' => true,
                    'infinite_extrapolation_allowed' => true,
                    'finite_execution_enforced' => true,
                ],
                'max_depth' => 10,
                'max_branches' => 100,
                'timeout' => 30,
            ],
            
            // ============================================================
            // KXML CONFIGURATION
            // ============================================================
            'kxml' => [
                'enabled' => true,
                'runtime' => 'kast/1',
                'tool_context' => 'jinja',
                'service_worker' => [
                    'enabled' => true,
                    'path' => '/service-worker.js',
                ],
                'inference_sidecar' => [
                    'enabled' => false,
                    'host' => 'localhost',
                    'port' => 8081,
                ],
                'templates' => [
                    'enabled' => true,
                    'directory' => __DIR__ . '/../templates',
                    'cache' => true,
                ],
            ],
            
            // ============================================================
            // PHASE GLYPHS MAPPING
            // ============================================================
            'glyphs' => [
                'Pop' => [
                    'phase' => 'Perceive',
                    'description' => 'Input / Perception',
                    'emoji' => '👁️',
                    'color' => '#4CAF50',
                ],
                'Wo' => [
                    'phase' => 'Represent',
                    'description' => 'Build / Bind',
                    'emoji' => '🧩',
                    'color' => '#2196F3',
                ],
                'Yax' => [
                    'phase' => 'Plan',
                    'description' => 'Condition / Intention',
                    'emoji' => '📋',
                    'color' => '#FF9800',
                ],
                'Sek' => [
                    'phase' => 'Execute',
                    'description' => 'Compute / Act',
                    'emoji' => '⚡',
                    'color' => '#F44336',
                ],
                "Ch'en" => [
                    'phase' => 'Project',
                    'description' => 'Output',
                    'emoji' => '📤',
                    'color' => '#9C27B0',
                ],
                'Xul' => [
                    'phase' => 'Consolidate',
                    'description' => 'Collapse',
                    'emoji' => '🔒',
                    'color' => '#795548',
                ],
                'Noj' => [
                    'phase' => 'Reflect',
                    'description' => 'Bounded Reasoning',
                    'emoji' => '🧠',
                    'color' => '#607D8B',
                ],
            ],
            
            // ============================================================
            // MICRONAUT COMPONENT NAMING
            // ============================================================
            'micronaut_naming' => [
                'fold_prefix' => 'F_',
                'field_prefix' => 'Φ_',
                'tool_suffix' => '-T',
                'agent_suffix' => '-A',
                'gram_prefix' => 'G_',
                'rule_prefix' => 'X_',
                'micronaut_suffix' => '-µ',
            ],
            
            // ============================================================
            // K'UHUL GRAMMAR EXTENSIONS
            // ============================================================
            'kuhul_grammar' => [
                'extensions' => [
                    'attributes' => [
                        'enabled' => true,
                        'syntax' => '#[Attribute]',
                    ],
                    'enums' => [
                        'enabled' => true,
                        'backing_types' => ['int', 'string'],
                    ],
                    'readonly_classes' => [
                        'enabled' => true,
                    ],
                    'dnf_types' => [
                        'enabled' => true,
                    ],
                    'match_expressions' => [
                        'enabled' => true,
                    ],
                    'arrow_functions' => [
                        'enabled' => true,
                    ],
                ],
                'strict_phase_order' => true,
                'allow_skip_phases' => false,
                'allow_repeat_phases' => true,
                'required_phases' => ['Pop', 'Xul'],
                'optional_phases' => ['Wo', 'Yax', 'Sek', "Ch'en", 'Noj'],
            ],
            
            // ============================================================
            // MICRONAUT GRAMMAR EXTENSIONS
            // ============================================================
            'micronaut_grammar' => [
                'extensions' => [
                    'conditional_folds' => true,
                    'parallel_execution' => false,
                    'state_persistence' => true,
                    'event_handling' => true,
                    'error_recovery' => true,
                    'load_balancing' => true,
                ],
                'max_folds' => 100,
                'max_agents' => 50,
                'max_fields' => 100,
                'max_rules' => 1000,
                'max_nodes_per_fold' => 100,
                'max_edges_per_fold' => 500,
            ],
        ];
    }
    
    /**
     * Get validation rules
     */
    private function getValidationRules(): array {
        return [
            'server.port' => ['type' => 'int', 'min' => 1, 'max' => 65535],
            'server.host' => ['type' => 'string', 'pattern' => '/^[0-9a-f.:]+$/'],
            'server.timeout' => ['type' => 'int', 'min' => 1, 'max' => 3600],
            'server.max_connections' => ['type' => 'int', 'min' => 1, 'max' => 10000],
            
            'kuhul.enabled' => ['type' => 'bool'],
            'kuhul.phase' => ['type' => 'string', 'in' => ['Pop', 'Wo', 'Yax', 'Sek', "Ch'en", 'Xul', 'Noj']],
            'kuhul.enforce_on_startup' => ['type' => 'bool'],
            
            'micronaut.enabled' => ['type' => 'bool'],
            'micronaut.priority' => ['type' => 'string', 'in' => ['balanced', 'precision', 'innovation', 'efficiency', 'conservation', 'correctness', 'integrity', 'quality', 'reliability', 'performance']],
            'micronaut.entropy_budget' => ['type' => 'float', 'min' => 0, 'max' => 1],
            'micronaut.timeout_ms' => ['type' => 'int', 'min' => 100, 'max' => 30000],
            
            'mcp.enabled' => ['type' => 'bool'],
            'mcp.transport' => ['type' => 'string', 'in' => ['http', 'stdio']],
            
            'cache.driver' => ['type' => 'string', 'in' => ['file', 'apcu', 'redis', 'memcached']],
            'cache.ttl' => ['type' => 'int', 'min' => 0, 'max' => 86400],
            
            'dns.ttl' => ['type' => 'int', 'min' => 0, 'max' => 86400],
            'dns.timeout' => ['type' => 'int', 'min' => 1, 'max' => 30],
            'dns.retries' => ['type' => 'int', 'min' => 0, 'max' => 10],
            
            'logging.level' => ['type' => 'string', 'in' => ['debug', 'info', 'warning', 'error', 'critical']],
            
            'security.rate_limit.requests_per_minute' => ['type' => 'int', 'min' => 1, 'max' => 10000],
            'security.rate_limit.burst' => ['type' => 'int', 'min' => 0, 'max' => 1000],
            
            'extrapolator.max_depth' => ['type' => 'int', 'min' => 1, 'max' => 100],
            'extrapolator.max_branches' => ['type' => 'int', 'min' => 1, 'max' => 10000],
            'extrapolator.timeout' => ['type' => 'int', 'min' => 1, 'max' => 300],
            
            'kuhul_grammar.strict_phase_order' => ['type' => 'bool'],
            'kuhul_grammar.allow_skip_phases' => ['type' => 'bool'],
            'kuhul_grammar.allow_repeat_phases' => ['type' => 'bool'],
            
            'micronaut_grammar.max_folds' => ['type' => 'int', 'min' => 1, 'max' => 1000],
            'micronaut_grammar.max_agents' => ['type' => 'int', 'min' => 1, 'max' => 500],
            'micronaut_grammar.max_fields' => ['type' => 'int', 'min' => 1, 'max' => 1000],
        ];
    }
    
    /**
     * Load environment variables
     */
    private function loadEnvironment(): void {
        // Override config with environment variables
        // Format: KUHUL_SERVER_PORT=8080
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'KUHUL_') === 0) {
                $path = strtolower(str_replace('KUHUL_', '', $key));
                $parts = explode('_', $path);
                $this->setNested($parts, $value);
            }
            if (strpos($key, 'MICRONAUT_') === 0) {
                $path = strtolower(str_replace('MICRONAUT_', '', $key));
                $parts = explode('_', $path);
                $this->setNested($parts, $value);
            }
        }
    }
    
    /**
     * Load K'UHUL grammar definitions
     */
    private function loadKuhulGrammar(): void {
        // Load K'UHUL grammar from file if exists
        $grammarFile = __DIR__ . '/../grammar/kuhul.grammar';
        if (file_exists($grammarFile)) {
            $this->config['kuhul']['grammar']['file'] = $grammarFile;
            $this->config['kuhul']['grammar']['loaded'] = true;
        }
        
        // Load custom grammar extensions
        $extensionsFile = __DIR__ . '/../grammar/kuhul_extensions.grammar';
        if (file_exists($extensionsFile)) {
            $extensions = file_get_contents($extensionsFile);
            $this->config['kuhul']['grammar']['extensions_raw'] = $extensions;
        }
    }
    
    /**
     * Load Micronaut grammar definitions
     */
    private function loadMicronautGrammar(): void {
        $grammarFile = __DIR__ . '/../grammar/micronaut.grammar';
        if (file_exists($grammarFile)) {
            $this->config['micronaut']['grammar']['file'] = $grammarFile;
            $this->config['micronaut']['grammar']['loaded'] = true;
        }
    }
    
    /**
     * Set nested configuration value
     */
    private function setNested(array $parts, $value): void {
        $current = &$this->config;
        foreach ($parts as $part) {
            if (!isset($current[$part])) {
                $current[$part] = [];
            }
            $current = &$current[$part];
        }
        
        // Type conversion
        if (is_numeric($value)) {
            $value = strpos($value, '.') !== false ? (float)$value : (int)$value;
        } elseif ($value === 'true') {
            $value = true;
        } elseif ($value === 'false') {
            $value = false;
        } elseif ($value === 'null') {
            $value = null;
        }
        
        $current = $value;
    }
    
    /**
     * Get configuration value
     */
    public function get($key, $default = null) {
        if (strpos($key, '.') !== false) {
            $parts = explode('.', $key);
            $value = $this->config;
            foreach ($parts as $part) {
                if (!isset($value[$part])) {
                    return $default;
                }
                $value = $value[$part];
            }
            return $value;
        }
        return $this->config[$key] ?? $default;
    }
    
    /**
     * Set configuration value
     */
    public function set(string $key, $value): void {
        if (strpos($key, '.') !== false) {
            $parts = explode('.', $key);
            $current = &$this->config;
            foreach ($parts as $i => $part) {
                if ($i === count($parts) - 1) {
                    $current[$part] = $value;
                } else {
                    if (!isset($current[$part]) || !is_array($current[$part])) {
                        $current[$part] = [];
                    }
                    $current = &$current[$part];
                }
            }
        } else {
            $this->config[$key] = $value;
        }
    }
    
    /**
     * Check if configuration key exists
     */
    public function has(string $key): bool {
        return $this->get($key, null) !== null;
    }
    
    /**
     * Get all configuration
     */
    public function all(): array {
        return $this->config;
    }
    
    /**
     * Load configuration from array
     */
    public function load(array $config): void {
        $this->config = array_merge_recursive($this->config, $config);
    }
    
    /**
     * Merge configuration
     */
    public function merge(array $config): void {
        $this->config = array_merge_recursive($this->config, $config);
    }
    
    /**
     * Save configuration to file
     */
    public function save(string $path): bool {
        $content = "<?php\n\n// Generated configuration\n// " . date('Y-m-d H:i:s') . "\n\nreturn " . $this->exportArray($this->config) . ";\n";
        return file_put_contents($path, $content) !== false;
    }
    
    /**
     * Load configuration from file
     */
    public function loadFromFile(string $path): bool {
        if (!file_exists($path)) {
            return false;
        }
        
        $config = require $path;
        if (is_array($config)) {
            $this->merge($config);
            $this->loadedFiles[] = $path;
            return true;
        }
        
        return false;
    }
    
    /**
     * Export array as PHP code
     */
    private function exportArray(array $array, int $depth = 0): string {
        $indent = str_repeat('    ', $depth);
        $lines = [];
        $lines[] = '[';
        
        foreach ($array as $key => $value) {
            $keyStr = is_string($key) ? "'" . addslashes($key) . "'" : $key;
            if (is_array($value)) {
                $lines[] = $indent . '    ' . $keyStr . ' => ' . $this->exportArray($value, $depth + 1) . ',';
            } else {
                $valueStr = $this->exportValue($value);
                $lines[] = $indent . '    ' . $keyStr . ' => ' . $valueStr . ',';
            }
        }
        
        $lines[] = $indent . ']';
        return implode("\n", $lines);
    }
    
    /**
     * Export value as PHP code
     */
    private function exportValue($value): string {
        if (is_string($value)) {
            return "'" . addslashes($value) . "'";
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_null($value)) {
            return 'null';
        } elseif (is_numeric($value)) {
            return (string)$value;
        } elseif (is_array($value)) {
            return $this->exportArray($value);
        } else {
            return 'null';
        }
    }
    
    /**
     * Validate configuration
     */
    public function validate(): array {
        $errors = [];
        
        foreach ($this->validationRules as $key => $rules) {
            $value = $this->get($key);
            
            if (isset($rules['type'])) {
                $type = $rules['type'];
                $actualType = gettype($value);
                
                if ($type === 'bool' && $actualType !== 'boolean') {
                    $errors[] = "Configuration key '{$key}' should be boolean, got '{$actualType}'";
                } elseif ($type === 'int' && $actualType !== 'integer') {
                    $errors[] = "Configuration key '{$key}' should be integer, got '{$actualType}'";
                } elseif ($type === 'float' && $actualType !== 'double' && $actualType !== 'float') {
                    $errors[] = "Configuration key '{$key}' should be float, got '{$actualType}'";
                } elseif ($type === 'string' && $actualType !== 'string') {
                    $errors[] = "Configuration key '{$key}' should be string, got '{$actualType}'";
                } elseif ($type === 'array' && $actualType !== 'array') {
                    $errors[] = "Configuration key '{$key}' should be array, got '{$actualType}'";
                }
            }
            
            if (isset($rules['min']) && is_numeric($value)) {
                if ($value < $rules['min']) {
                    $errors[] = "Configuration key '{$key}' should be at least {$rules['min']}, got {$value}";
                }
            }
            
            if (isset($rules['max']) && is_numeric($value)) {
                if ($value > $rules['max']) {
                    $errors[] = "Configuration key '{$key}' should be at most {$rules['max']}, got {$value}";
                }
            }
            
            if (isset($rules['in']) && is_string($value)) {
                if (!in_array($value, $rules['in'])) {
                    $errors[] = "Configuration key '{$key}' should be one of: " . implode(', ', $rules['in']) . ", got '{$value}'";
                }
            }
            
            if (isset($rules['pattern']) && is_string($value)) {
                if (!preg_match($rules['pattern'], $value)) {
                    $errors[] = "Configuration key '{$key}' does not match pattern: {$rules['pattern']}";
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Get K'UHUL laws
     */
    public function getKuhulLaws(): array {
        return $this->get('kuhul.laws', []);
    }
    
    /**
     * Get Micronaut folds
     */
    public function getMicronautFolds(): array {
        return $this->get('micronaut.folds', []);
    }
    
    /**
     * Get Micronaut agents
     */
    public function getMicronautAgents(): array {
        return $this->get('micronaut.agents', []);
    }
    
    /**
     * Get MCP tools
     */
    public function getMcpTools(): array {
        return $this->get('mcp.tools', []);
    }
    
    /**
     * Get MCP resources
     */
    public function getMcpResources(): array {
        return $this->get('mcp.resources', []);
    }
    
    /**
     * Get MCP prompts
     */
    public function getMcpPrompts(): array {
        return $this->get('mcp.prompts', []);
    }
    
    /**
     * Get phase glyphs
     */
    public function getGlyphs(): array {
        return $this->get('glyphs', []);
    }
    
    /**
     * Get a specific glyph
     */
    public function getGlyph(string $name): ?array {
        return $this->get('glyphs.' . $name, null);
    }
    
    /**
     * Get a specific law
     */
    public function getLaw(string $name): ?array {
        return $this->get('kuhul.laws.' . $name, null);
    }
    
    /**
     * Get a specific fold
     */
    public function getFold(string $name): ?array {
        return $this->get('micronaut.folds.' . $name, null);
    }
    
    /**
     * Get a specific agent
     */
    public function getAgent(string $name): ?array {
        return $this->get('micronaut.agents.' . $name, null);
    }
    
    /**
     * Get a specific field
     */
    public function getField(string $name): ?array {
        return $this->get('micronaut.fields.' . $name, null);
    }
}

// ============================================================
// 3. CONFIGURATION HELPER FUNCTIONS
// ============================================================

/**
 * Global config helper
 */
function config(?string $key = null, $default = null) {
    $config = Config::getInstance();
    if ($key === null) {
        return $config;
    }
    return $config->get($key, $default);
}

/**
 * Get K'UHUL configuration
 */
function kuhul_config(?string $key = null, $default = null) {
    return config('kuhul' . ($key ? '.' . $key : ''), $default);
}

/**
 * Get Micronaut configuration
 */
function micronaut_config(?string $key = null, $default = null) {
    return config('micronaut' . ($key ? '.' . $key : ''), $default);
}

/**
 * Get MCP configuration
 */
function mcp_config(?string $key = null, $default = null) {
    return config('mcp' . ($key ? '.' . $key : ''), $default);
}

/**
 * Get server configuration
 */
function server_config(?string $key = null, $default = null) {
    return config('server' . ($key ? '.' . $key : ''), $default);
}

// ============================================================
// 4. CONFIGURATION LOADING
// ============================================================

// Load configuration from file if exists
$configFile = __DIR__ . '/../config.php';
if (file_exists($configFile)) {
    Config::getInstance()->loadFromFile($configFile);
}

// Load K'UHUL grammar if exists
$kuhulGrammarFile = __DIR__ . '/../grammar/kuhul.grammar';
if (file_exists($kuhulGrammarFile)) {
    Config::getInstance()->set('kuhul.grammar.file', $kuhulGrammarFile);
    Config::getInstance()->set('kuhul.grammar.loaded', true);
}

// Load Micronaut grammar if exists
$micronautGrammarFile = __DIR__ . '/../grammar/micronaut.grammar';
if (file_exists($micronautGrammarFile)) {
    Config::getInstance()->set('micronaut.grammar.file', $micronautGrammarFile);
    Config::getInstance()->set('micronaut.grammar.loaded', true);
}

// Load JSON schemas if exist
$kuhulSchemaFile = __DIR__ . '/../grammar/kuhul.schema.json';
if (file_exists($kuhulSchemaFile)) {
    Config::getInstance()->set('kuhul.grammar.schema', $kuhulSchemaFile);
}

$micronautSchemaFile = __DIR__ . '/../grammar/micronaut.schema.json';
if (file_exists($micronautSchemaFile)) {
    Config::getInstance()->set('micronaut.grammar.schema', $micronautSchemaFile);
}

// ============================================================
// 5. EXPORT MAIN CLASS
// ============================================================

if (php_sapi_name() === 'cli') {
    // Display configuration summary
    $config = Config::getInstance();
    $errors = $config->validate();
    
    echo "⟁ K'UHUL π · Micronaut µ · Configuration ⟁\n";
    echo "◆ Server: " . $config->get('server.host') . ":" . $config->get('server.port') . "\n";
    echo "π K'UHUL: " . ($config->get('kuhul.enabled') ? 'Enabled' : 'Disabled') . "\n";
    echo "  └ Phase: " . $config->get('kuhul.phase') . "\n";
    echo "  └ Laws: " . implode(', ', array_keys($config->getKuhulLaws())) . "\n";
    echo "µ Micronaut: " . ($config->get('micronaut.enabled') ? 'Enabled' : 'Disabled') . "\n";
    echo "  └ Folds: " . implode(', ', array_keys($config->getMicronautFolds())) . "\n";
    echo "  └ Agents: " . implode(', ', array_keys($config->getMicronautAgents())) . "\n";
    echo "  └ Fields: " . implode(', ', array_keys($config->get('micronaut.fields', []))) . "\n";
    echo "⚡ MCP: " . ($config->get('mcp.enabled') ? 'Enabled' : 'Disabled') . "\n";
    echo "  └ Tools: " . count($config->getMcpTools()) . "\n";
    echo "  └ Resources: " . count($config->getMcpResources()) . "\n";
    echo "  └ Prompts: " . count($config->getMcpPrompts()) . "\n";
    echo "◆ Logging: " . $config->get('logging.level') . "\n";
    echo "◆ Cache: " . $config->get('cache.driver') . "\n";
    
    if (!empty($errors)) {
        echo "\n⚠️ Validation Errors:\n";
        foreach ($errors as $error) {
            echo "  • " . $error . "\n";
        }
    } else {
        echo "\n✅ Configuration is valid\n";
    }
    
    echo "\n⟁ The boundaries are permanent. No further refinement possible. ⟁\n";
}

return Config::getInstance();