<?php
/**
 * Micronaut.php - Complete PHP Implementation
 * 
 * Micronaut µ - Orchestration Layer for PHP
 * K'UHUL π - Enforcement Layer Integration
 * 
 * @package Micronaut
 * @version 3.0.0
 * @author PHP Runtime · K'UHUL π · Micronaut
 * @license MIT
 * 
 * Architecture:
 * - Micronaut orchestrates contexts
 * - K'UHUL π enforces law
 * - They are orthogonal
 * - The boundary is permanent
 */

declare(strict_types=1);

// ============================================================
// 1. CORE TYPES & CONSTANTS
// ============================================================

/**
 * Phase Glyphs - K'UHUL π Enforcement Phases
 */
define('KUHUL_GLYPHS', [
    'Pop'    => 'Perceive / Input',
    'Wo'     => 'Represent / Build / Bind',
    'Yax'    => 'Plan / Condition / Intention',
    'Sek'    => 'Execute / Compute / Act',
    "Ch'en"  => 'Project / Output',
    'Xul'    => 'Consolidate / Collapse',
    'Noj'    => 'Reflect / Bounded Reasoning'
]);

/**
 * Micronaut Status States
 */
define('MICRONAUT_STATUS', [
    'created', 'initializing', 'ready', 'running',
    'paused', 'degraded', 'recovering', 'terminating', 'terminated'
]);

/**
 * Fold Types
 */
define('FOLD_TYPES', [
    'orchestrator', 'compute', 'storage', 'network',
    'reasoning', 'generation', 'planning', 'persistence',
    'codegen', 'filesystem', 'graphics', 'inference'
]);

/**
 * Node Types
 */
define('NODE_TYPES', [
    'input', 'output', 'process', 'transform',
    'gate', 'memory', 'dispatch'
]);

/**
 * Field Types
 */
define('FIELD_TYPES', [
    'working', 'episodic', 'semantic', 'procedural', 'persistent'
]);

/**
 * Persistence Types
 */
define('PERSISTENCE_TYPES', [
    'volatile', 'persistent', 'ephemeral'
]);

/**
 * Agent Types
 */
define('AGENT_TYPES', [
    'worker', 'manager', 'explorer', 'creator', 'helper'
]);

/**
 * Action Types
 */
define('ACTION_TYPES', [
    'dispatch', 'mutate', 'halt', 'checkpoint', 'propagate', 'log'
]);

/**
 * Priority Types
 */
define('PRIORITY_TYPES', [
    'balanced', 'precision', 'innovation', 'efficiency',
    'conservation', 'correctness', 'integrity', 'quality',
    'reliability', 'performance'
]);

// ============================================================
// 2. K'UHUL π - LAW ENFORCEMENT LAYER
// ============================================================

/**
 * K'UHUL Phase - Represents a single phase glyph
 */
class KuhulPhase {
    private string $glyph;
    private int $index;
    private string $meaning;
    private array $metadata;

    public function __construct(string $glyph) {
        $glyphs = array_keys(KUHUL_GLYPHS);
        $index = array_search($glyph, $glyphs);
        
        if ($index === false) {
            throw new InvalidArgumentException("Invalid K'UHUL glyph: {$glyph}");
        }
        
        $this->glyph = $glyph;
        $this->index = $index;
        $this->meaning = KUHUL_GLYPHS[$glyph];
        $this->metadata = [
            'phase' => $glyph,
            'index' => $index,
            'meaning' => $this->meaning,
            'timestamp' => date('c')
        ];
    }

    public function getGlyph(): string { return $this->glyph; }
    public function getIndex(): int { return $this->index; }
    public function getMeaning(): string { return $this->meaning; }
    public function getMetadata(): array { return $this->metadata; }

    public function isBefore(KuhulPhase $other): bool {
        return $this->index < $other->getIndex();
    }

    public function isAfter(KuhulPhase $other): bool {
        return $this->index > $other->getIndex();
    }

    public function isValidTransition(KuhulPhase $to): bool {
        $diff = $to->getIndex() - $this->index;
        return $diff === 0 || $diff === 1;
    }

    public function __toString(): string {
        return "[{$this->glyph}] {$this->meaning}";
    }

    public function toArray(): array {
        return [
            'glyph' => $this->glyph,
            'index' => $this->index,
            'meaning' => $this->meaning,
            'metadata' => $this->metadata
        ];
    }
}

/**
 * K'UHUL Law - Enforceable law definition
 */
class KuhulLaw {
    private string $name;
    private string $hash;
    private array $definition;
    private array $invariants;
    private array $phases;

    public function __construct(string $name, array $definition) {
        $this->name = $name;
        $this->definition = $definition;
        $this->invariants = [
            'collapse_only' => true,
            'field_perception' => true,
            'compression_law' => true,
            'unreachable_states' => true
        ];
        $this->phases = [];

        // Extract phases from definition
        if (isset($definition['phases']) && is_array($definition['phases'])) {
            foreach ($definition['phases'] as $glyph) {
                $this->phases[] = new KuhulPhase($glyph);
            }
        }

        // Override invariants if provided
        if (isset($definition['invariants']) && is_array($definition['invariants'])) {
            $this->invariants = array_merge($this->invariants, $definition['invariants']);
        }

        // Compute semantic hash
        $json = json_encode($definition, JSON_THROW_ON_ERROR);
        $this->hash = hash('sha256', $json);
    }

    public function getName(): string { return $this->name; }
    public function getHash(): string { return $this->hash; }
    public function getPhases(): array { return $this->phases; }
    public function getInvariants(): array { return $this->invariants; }

    public function enforce(array &$state): bool {
        $logger = MicronautLogger::getInstance();
        $logger->enforce("π ENFORCE: {$this->name} | Hash: " . substr($this->hash, 0, 8));

        // Check each invariant
        foreach ($this->invariants as $key => $required) {
            if ($required && !$this->validateInvariant($key, $state)) {
                $logger->error("π REJECT: Invariant '{$key}' violated");
                return false;
            }
        }

        // Validate phase progression
        if (!empty($this->phases)) {
            if (!$this->validatePhaseProgression($state)) {
                $logger->error("π REJECT: Invalid phase progression");
                return false;
            }
        }

        $logger->enforce("π COLLAPSE: Law enforced successfully");
        return true;
    }

    private function validateInvariant(string $name, array $state): bool {
        switch ($name) {
            case 'collapse_only':
                // Only Xul can collapse
                return !isset($state['phase']) || $state['phase'] !== 'Xul' || 
                       (isset($state['phase']) && $state['phase'] === 'Xul');
            case 'field_perception':
                // Must have a field to perceive
                return isset($state['field']) || isset($state['fields']);
            case 'compression_law':
                // State must be compressible
                try {
                    json_encode($state, JSON_THROW_ON_ERROR);
                    return true;
                } catch (JsonException $e) {
                    return false;
                }
            default:
                return true;
        }
    }

    private function validatePhaseProgression(array $state): bool {
        $currentPhase = $state['phase'] ?? 'Pop';
        $glyphs = array_keys(KUHUL_GLYPHS);
        $currentIdx = array_search($currentPhase, $glyphs);
        
        if ($currentIdx === false) {
            return false;
        }

        // Find the last phase in our law
        $lastPhase = end($this->phases);
        $lastIdx = $lastPhase->getIndex();

        // Can't go beyond the law's defined phases
        return $currentIdx <= $lastIdx;
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'hash' => $this->hash,
            'phases' => array_map(fn($p) => $p->toArray(), $this->phases),
            'invariants' => $this->invariants,
            'definition' => $this->definition
        ];
    }
}

/**
 * K'UHUL Runtime - Main enforcement engine
 */
class KuhulRuntime {
    private array $laws = [];
    private array $state = [];
    private array $phaseHistory = [];
    private KuhulPhase $currentPhase;
    private bool $isEnforcing = false;
    private MicronautLogger $logger;

    public function __construct() {
        $this->state = [
            'fields' => [],
            'phase' => 'Pop',
            'entropy' => 0.0,
            'coherence' => 1.0,
            'perception' => null,
            'intention' => null,
            'result' => null,
            'output' => null,
            'consolidated' => false
        ];
        $this->currentPhase = new KuhulPhase('Pop');
        $this->logger = MicronautLogger::getInstance();
        $this->logger->phase("π K'UHUL runtime initialized");
        
        // Define default laws
        $this->defineLaw('collapse_only', [
            'phases' => ['Pop', 'Wo', 'Yax', 'Sek', "Ch'en", 'Xul'],
            'invariants' => [
                'collapse_only' => true,
                'field_perception' => true,
                'compression_law' => true,
                'unreachable_states' => true
            ]
        ]);
        
        $this->defineLaw('perception_field', [
            'phases' => ['Pop', 'Wo', 'Xul'],
            'invariants' => [
                'field_perception' => true,
                'collapse_only' => true
            ]
        ]);
    }

    public function defineLaw(string $name, array $definition): void {
        $this->laws[$name] = new KuhulLaw($name, $definition);
        $this->logger->enforce("π DEFINED: {$name} | Hash: " . substr($this->laws[$name]->getHash(), 0, 8));
    }

    public function enforceLaw(string $name, ?array $state = null): bool {
        if (!isset($this->laws[$name])) {
            $this->logger->error("π ERROR: Law '{$name}' not found");
            return false;
        }

        $this->isEnforcing = true;

        // Merge state
        if ($state !== null) {
            $this->state = array_merge($this->state, $state);
        }

        $law = $this->laws[$name];
        $result = $law->enforce($this->state);

        $this->isEnforcing = false;
        return $result;
    }

    public function transitionPhase(string $toGlyph): void {
        $toPhase = new KuhulPhase($toGlyph);

        if (!$this->currentPhase->isValidTransition($toPhase)) {
            $this->logger->error("π REJECT: Invalid phase transition {$this->currentPhase->getGlyph()} → {$toGlyph}");
            throw new InvalidArgumentException("Invalid K'UHUL phase transition");
        }

        $this->phaseHistory[] = $this->currentPhase;
        $this->currentPhase = $toPhase;
        $this->state['phase'] = $toGlyph;

        $this->logger->phase("π PHASE: " . $this->currentPhase->__toString());
    }

    public function perceive(array $input): array {
        $this->transitionPhase('Pop');
        $this->state['perception'] = $input;
        $this->logger->phase("π Pop: Perceived input");
        return $this->state;
    }

    public function represent(string $symbol, $value): array {
        $this->transitionPhase('Wo');
        $this->state['fields'][$symbol] = $value;
        $this->logger->phase("π Wo: Bound {$symbol} = " . json_encode($value));
        return $this->state;
    }

    public function plan(string $intention): array {
        $this->transitionPhase('Yax');
        $this->state['intention'] = $intention;
        $this->logger->phase("π Yax: Planned {$intention}");
        return $this->state;
    }

    public function execute(callable $action): array {
        $this->transitionPhase('Sek');
        $result = $action($this->state);
        $this->state['result'] = $result;
        $this->logger->phase("π Sek: Executed action");
        return $this->state;
    }

    public function project(string $target): array {
        $this->transitionPhase("Ch'en");
        $output = $this->state[$target] ?? $this->state['result'] ?? null;
        $this->state['output'] = $output;
        $this->logger->phase("π Ch'en: Projected {$target}");
        return $this->state;
    }

    public function consolidate(): array {
        $this->transitionPhase('Xul');
        $this->state['consolidated'] = true;
        $this->state['timestamp'] = date('c');
        $this->logger->phase("π Xul: Consolidated");
        return $this->state;
    }

    public function reflect(): array {
        $this->transitionPhase('Noj');
        $reflection = [
            'phases' => count($this->phaseHistory),
            'current_phase' => $this->currentPhase->getGlyph(),
            'entropy' => $this->state['entropy'],
            'coherence' => $this->state['coherence'],
            'fields' => count($this->state['fields']),
            'laws' => count($this->laws),
            'enforcing' => $this->isEnforcing
        ];
        $this->state['reflection'] = $reflection;
        $this->logger->phase("π Noj: Reflected on state");
        return $this->state;
    }

    public function getState(): array { return $this->state; }
    public function getLaws(): array { return $this->laws; }
    public function getCurrentPhase(): KuhulPhase { return $this->currentPhase; }
    public function getPhaseHistory(): array { return $this->phaseHistory; }
    public function isEnforcing(): bool { return $this->isEnforcing; }

    public function toArray(): array {
        return [
            'phase' => $this->currentPhase->toArray(),
            'state' => $this->state,
            'laws' => array_map(fn($l) => $l->toArray(), $this->laws),
            'history' => array_map(fn($p) => $p->toArray(), $this->phaseHistory),
            'enforcing' => $this->isEnforcing
        ];
    }
}

// ============================================================
// 3. MICRONAUT - ORCHESTRATION LAYER
// ============================================================

/**
 * Micronaut Logger - Centralized logging
 */
class MicronautLogger {
    private static ?MicronautLogger $instance = null;
    private string $logFile;
    private string $kuhulLogFile;
    private string $micronautLogFile;
    private array $handlers = [];

    private function __construct() {
        $this->logFile = __DIR__ . '/logs/server.log';
        $this->kuhulLogFile = __DIR__ . '/logs/kuhul.log';
        $this->micronautLogFile = __DIR__ . '/logs/micronaut.log';
        
        // Create log directories
        foreach ([__DIR__ . '/logs'] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function addHandler(callable $handler): void {
        $this->handlers[] = $handler;
    }

    private function log(string $message, string $level = 'INFO', string $domain = 'SERVER'): void {
        $timestamp = date('Y-m-d H:i:s.u');
        $glyph = match($domain) {
            'KUHUL' => 'π',
            'MICRONAUT' => 'µ',
            'MCP' => '⚡',
            'PHP' => '🐘',
            default => '◆'
        };
        
        $logEntry = "[{$timestamp}] [{$glyph}] [{$domain}] [{$level}] {$message}";
        
        // Write to main log
        file_put_contents($this->logFile, $logEntry . PHP_EOL, FILE_APPEND);
        
        // Write to domain-specific logs
        if ($domain === 'KUHUL') {
            file_put_contents($this->kuhulLogFile, $logEntry . PHP_EOL, FILE_APPEND);
        }
        if ($domain === 'MICRONAUT') {
            file_put_contents($this->micronautLogFile, $logEntry . PHP_EOL, FILE_APPEND);
        }
        
        // Call handlers
        foreach ($this->handlers as $handler) {
            $handler($logEntry, $level, $domain);
        }
        
        // Output to console with colors
        $colors = [
            'ERROR' => "\033[31m",
            'WARNING' => "\033[33m",
            'PHASE' => "\033[35m",
            'ORCHESTRATE' => "\033[36m",
            'ENFORCE' => "\033[32m",
            'INFO' => "\033[37m"
        ];
        $reset = "\033[0m";
        $color = $colors[$level] ?? $colors['INFO'];
        echo $color . $logEntry . $reset . PHP_EOL;
    }

    public function info(string $message, string $domain = 'SERVER'): void {
        $this->log($message, 'INFO', $domain);
    }

    public function error(string $message, string $domain = 'SERVER'): void {
        $this->log($message, 'ERROR', $domain);
    }

    public function warning(string $message, string $domain = 'SERVER'): void {
        $this->log($message, 'WARNING', $domain);
    }

    public function phase(string $message): void {
        $this->log($message, 'PHASE', 'KUHUL');
    }

    public function enforce(string $message): void {
        $this->log($message, 'ENFORCE', 'KUHUL');
    }

    public function orchestrate(string $message): void {
        $this->log($message, 'ORCHESTRATE', 'MICRONAUT');
    }

    public function mcp(string $message): void {
        $this->log($message, 'INFO', 'MCP');
    }
}

/**
 * Micronaut Identity
 */
class MicronautIdentity {
    private string $name;
    private string $role;
    private string $version;
    private string $created;
    private string $type;

    public function __construct(string $name, string $role, string $version = '1.0.0') {
        $this->name = $name;
        $this->role = $role;
        $this->version = $version;
        $this->created = date('c');
        $this->type = 'orchestrator';
    }

    public function getName(): string { return $this->name; }
    public function getRole(): string { return $this->role; }
    public function getVersion(): string { return $this->version; }
    public function getCreated(): string { return $this->created; }
    public function getType(): string { return $this->type; }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'role' => $this->role,
            'version' => $this->version,
            'created' => $this->created,
            'type' => $this->type
        ];
    }
}

/**
 * Micronaut Policy
 */
class MicronautPolicy {
    private string $priority;
    private float $entropyBudget;
    private int $timeoutMs;
    private array $retryPolicy;

    public function __construct(
        string $priority = 'balanced',
        float $entropyBudget = 0.5,
        int $timeoutMs = 5000
    ) {
        if (!in_array($priority, PRIORITY_TYPES)) {
            throw new InvalidArgumentException("Invalid priority: {$priority}");
        }
        $this->priority = $priority;
        $this->entropyBudget = $entropyBudget;
        $this->timeoutMs = $timeoutMs;
        $this->retryPolicy = [
            'max_attempts' => 3,
            'backoff_ms' => 1000
        ];
    }

    public function getPriority(): string { return $this->priority; }
    public function getEntropyBudget(): float { return $this->entropyBudget; }
    public function getTimeoutMs(): int { return $this->timeoutMs; }
    public function getRetryPolicy(): array { return $this->retryPolicy; }

    public function toArray(): array {
        return [
            'priority' => $this->priority,
            'entropy_budget' => $this->entropyBudget,
            'timeout_ms' => $this->timeoutMs,
            'retry_policy' => $this->retryPolicy
        ];
    }
}

/**
 * Micronaut State
 */
class MicronautState {
    private string $status;
    private float $coherence;
    private float $entropy;
    private int $uptimeMs;
    private string $lastAction;
    private array $history = [];

    public function __construct() {
        $this->status = 'created';
        $this->coherence = 1.0;
        $this->entropy = 0.0;
        $this->uptimeMs = 0;
        $this->lastAction = date('c');
    }

    public function getStatus(): string { return $this->status; }
    public function getCoherence(): float { return $this->coherence; }
    public function getEntropy(): float { return $this->entropy; }
    public function getUptimeMs(): int { return $this->uptimeMs; }
    public function getLastAction(): string { return $this->lastAction; }
    public function getHistory(): array { return $this->history; }

    public function transition(string $newStatus): void {
        if (!in_array($newStatus, MICRONAUT_STATUS)) {
            throw new InvalidArgumentException("Invalid status: {$newStatus}");
        }
        
        $this->history[] = [
            'from' => $this->status,
            'to' => $newStatus,
            'timestamp' => date('c')
        ];
        
        $this->status = $newStatus;
        $this->lastAction = date('c');
        
        $logger = MicronautLogger::getInstance();
        $logger->orchestrate("µ STATUS: {$newStatus}");
    }

    public function updateCoherence(float $coherence): void {
        $this->coherence = max(0, min(1, $coherence));
    }

    public function updateEntropy(float $entropy): void {
        $this->entropy = max(0, min(1, $entropy));
    }

    public function toArray(): array {
        return [
            'status' => $this->status,
            'coherence' => $this->coherence,
            'entropy' => $this->entropy,
            'uptime_ms' => $this->uptimeMs,
            'last_action' => $this->lastAction,
            'history' => $this->history
        ];
    }
}

/**
 * Micronaut Agent
 */
class MicronautAgent {
    private MicronautIdentity $identity;
    private string $type;
    private array $tools;
    private array $goals;
    private array $constraints;
    private MicronautState $state;
    private array $metrics;
    private MicronautLogger $logger;

    public function __construct(string $name, string $type = 'worker') {
        if (!in_array($type, AGENT_TYPES)) {
            throw new InvalidArgumentException("Invalid agent type: {$type}");
        }
        
        $this->identity = new MicronautIdentity($name, 'agent', '1.0.0');
        $this->type = $type;
        $this->tools = [];
        $this->goals = [];
        $this->constraints = [];
        $this->state = new MicronautState();
        $this->metrics = [
            'tasks_completed' => 0,
            'tasks_failed' => 0,
            'avg_latency_ms' => 0
        ];
        $this->logger = MicronautLogger::getInstance();
        $this->state->transition('ready');
        $this->logger->orchestrate("µ Agent {$name} ({$type}) created");
    }

    public function getName(): string { return $this->identity->getName(); }
    public function getType(): string { return $this->type; }
    public function getTools(): array { return $this->tools; }
    public function getGoals(): array { return $this->goals; }
    public function getConstraints(): array { return $this->constraints; }
    public function getState(): MicronautState { return $this->state; }
    public function getMetrics(): array { return $this->metrics; }

    public function addTool(string $toolName): void {
        if (!in_array($toolName, $this->tools)) {
            $this->tools[] = $toolName;
            $this->logger->orchestrate("µ Tool added: {$toolName}");
        }
    }

    public function addGoal(string $description, float $priority = 1.0, ?string $deadline = null): void {
        $this->goals[] = [
            'description' => $description,
            'priority' => $priority,
            'deadline' => $deadline ?? date('c', strtotime('+7 days'))
        ];
        $this->logger->orchestrate("µ Goal: {$description}");
    }

    public function addConstraint(string $constraint): void {
        $this->constraints[] = $constraint;
        $this->logger->orchestrate("µ Constraint: {$constraint}");
    }

    public function execute(string $toolName, array $params = []): array {
        if (!in_array($toolName, $this->tools)) {
            throw new RuntimeException("Tool '{$toolName}' not available");
        }

        $this->state->transition('running');
        $startTime = microtime(true);

        try {
            $this->logger->orchestrate("µ Executing tool: {$toolName}");
            
            // Simulate execution (in real implementation, this would call the tool)
            $result = [
                'tool' => $toolName,
                'params' => $params,
                'result' => 'Success',
                'timestamp' => date('c')
            ];
            
            $this->metrics['tasks_completed']++;
            $elapsed = (microtime(true) - $startTime) * 1000;
            $this->metrics['avg_latency_ms'] = (
                $this->metrics['avg_latency_ms'] * ($this->metrics['tasks_completed'] - 1) + $elapsed
            ) / $this->metrics['tasks_completed'];
            
            $this->state->transition('ready');
            return $result;
            
        } catch (Exception $e) {
            $this->metrics['tasks_failed']++;
            $this->state->transition('degraded');
            $this->logger->error("µ ERROR: " . $e->getMessage(), 'MICRONAUT');
            throw $e;
        }
    }

    public function getStatus(): array {
        return [
            'identity' => $this->identity->toArray(),
            'type' => $this->type,
            'tools' => $this->tools,
            'goals' => $this->goals,
            'constraints' => $this->constraints,
            'state' => $this->state->toArray(),
            'metrics' => $this->metrics
        ];
    }

    public function toArray(): array {
        return $this->getStatus();
    }
}

/**
 * Micronaut - Main Orchestrator
 */
class Micronaut {
    private MicronautIdentity $identity;
    private array $orchestrates;
    private MicronautPolicy $policy;
    private array $routing;
    private array $permissions;
    private MicronautState $state;
    private array $memory;
    private array $tools;
    private array $hierarchy;
    private array $metrics;
    private array $lifecycle;
    private array $agents;
    private array $fields;
    private array $folds;
    private MicronautLogger $logger;

    public function __construct(string $name, string $role = 'orchestrator') {
        $this->identity = new MicronautIdentity($name, $role);
        $this->orchestrates = [];
        $this->policy = new MicronautPolicy();
        $this->routing = [
            'strategy' => 'round_robin',
            'capability_map' => []
        ];
        $this->permissions = ['fold:execute', 'field:read', 'tool:use'];
        $this->state = new MicronautState();
        $this->memory = [
            'field' => null,
            'working' => null,
            'episodic' => null
        ];
        $this->tools = [];
        $this->hierarchy = [
            'parent' => null,
            'children' => []
        ];
        $this->metrics = [
            'folds_executed' => 0,
            'fields_projected' => 0,
            'grams_resolved' => 0,
            'traversals_completed' => 0,
            'errors' => 0,
            'avg_latency_ms' => 0
        ];
        $this->lifecycle = [
            'on_before_create' => [],
            'on_after_create' => [],
            'on_before_start' => [],
            'on_after_start' => [],
            'on_before_stop' => [],
            'on_after_stop' => [],
            'on_error' => []
        ];
        $this->agents = [];
        $this->fields = [];
        $this->folds = [];
        $this->logger = MicronautLogger::getInstance();
        
        $this->state->transition('initializing');
        $this->logger->orchestrate("µ Micronaut {$name} created");
        $this->state->transition('ready');
    }

    public function getName(): string { return $this->identity->getName(); }
    public function getState(): MicronautState { return $this->state; }
    public function getAgents(): array { return $this->agents; }
    public function getFields(): array { return $this->fields; }
    public function getFolds(): array { return $this->folds; }
    public function getTools(): array { return $this->tools; }

    public function orchestrate(string $foldName): void {
        if (!in_array($foldName, $this->orchestrates)) {
            $this->orchestrates[] = $foldName;
            $this->logger->orchestrate("µ Orchestrating: {$foldName}");
        }
    }

    public function addTool(string $toolName): void {
        if (!in_array($toolName, $this->tools)) {
            $this->tools[] = $toolName;
            $this->logger->orchestrate("µ Tool registered: {$toolName}");
        }
    }

    public function addAgent(MicronautAgent $agent): void {
        $this->agents[] = $agent;
        $this->logger->orchestrate("µ Agent registered: {$agent->getName()}");
    }

    public function createField(string $name, string $type = 'working', string $persistence = 'volatile'): void {
        if (!in_array($type, FIELD_TYPES)) {
            throw new InvalidArgumentException("Invalid field type: {$type}");
        }
        if (!in_array($persistence, PERSISTENCE_TYPES)) {
            throw new InvalidArgumentException("Invalid persistence: {$persistence}");
        }
        
        $this->fields[$name] = [
            'identity' => [
                'name' => $name,
                'type' => $type,
                'persistence' => $persistence
            ],
            'data' => [
                'rows' => 0,
                'cols' => 0,
                'values' => []
            ],
            'created' => date('c')
        ];
        $this->logger->orchestrate("µ FIELD Φ_{$name} ({$type}) created");
    }

    public function createFold(string $name, string $type = 'compute'): void {
        if (!in_array($type, FOLD_TYPES)) {
            throw new InvalidArgumentException("Invalid fold type: {$type}");
        }
        
        $this->folds[$name] = [
            'identity' => [
                'name' => $name,
                'type' => $type,
                'version' => '1.0.0'
            ],
            'nodes' => [],
            'created' => date('c')
        ];
        $this->logger->orchestrate("µ FOLD F_{$name} ({$type}) created");
    }

    public function addFoldNode(string $foldName, array $node): void {
        if (!isset($this->folds[$foldName])) {
            throw new RuntimeException("Fold '{$foldName}' not found");
        }
        
        if (!isset($node['id']) || !isset($node['type'])) {
            throw new InvalidArgumentException("Node must have 'id' and 'type'");
        }
        
        if (!in_array($node['type'], NODE_TYPES)) {
            throw new InvalidArgumentException("Invalid node type: {$node['type']}");
        }
        
        $this->folds[$foldName]['nodes'][] = $node;
        $this->logger->orchestrate("µ Fold {$foldName} node added: {$node['id']}");
    }

    public function executeFold(string $foldName, array $input = []): array {
        if (!in_array($foldName, $this->orchestrates)) {
            throw new RuntimeException("Fold '{$foldName}' is not orchestrated");
        }
        if (!isset($this->folds[$foldName])) {
            throw new RuntimeException("Fold '{$foldName}' not found");
        }

        $this->state->transition('running');
        $startTime = microtime(true);

        try {
            $this->logger->orchestrate("µ Executing fold: {$foldName}");
            
            // Execute fold nodes (simplified)
            $result = [
                'fold' => $foldName,
                'input' => $input,
                'output' => 'Executed',
                'timestamp' => date('c')
            ];
            
            $this->metrics['folds_executed']++;
            $elapsed = (microtime(true) - $startTime) * 1000;
            $this->metrics['avg_latency_ms'] = (
                $this->metrics['avg_latency_ms'] * ($this->metrics['folds_executed'] - 1) + $elapsed
            ) / $this->metrics['folds_executed'];
            
            $this->state->transition('ready');
            return $result;
            
        } catch (Exception $e) {
            $this->metrics['errors']++;
            $this->state->transition('degraded');
            $this->logger->error("µ ERROR: " . $e->getMessage(), 'MICRONAUT');
            throw $e;
        }
    }

    public function getStatus(): array {
        return [
            'identity' => $this->identity->toArray(),
            'orchestrates' => $this->orchestrates,
            'policy' => $this->policy->toArray(),
            'routing' => $this->routing,
            'permissions' => $this->permissions,
            'state' => $this->state->toArray(),
            'memory' => $this->memory,
            'tools' => $this->tools,
            'hierarchy' => $this->hierarchy,
            'metrics' => $this->metrics,
            'agents' => array_map(fn($a) => $a->toArray(), $this->agents),
            'folds' => array_keys($this->folds),
            'fields' => array_keys($this->fields)
        ];
    }

    public function toArray(): array {
        return $this->getStatus();
    }
}

// ============================================================
// 4. PHP GRAMMAR IMPLEMENTATION (EBNF/PEG/JSON Schema)
// ============================================================

/**
 * PHP Token - Token representation
 */
class PHPToken {
    public string $type;
    public string $value;
    public int $line;
    public int $column;

    public function __construct(string $type, string $value, int $line = 0, int $column = 0) {
        $this->type = $type;
        $this->value = $value;
        $this->line = $line;
        $this->column = $column;
    }

    public function __toString(): string {
        return "Token({$this->type}, '{$this->value}', {$this->line}:{$this->column})";
    }
}

/**
 * PHP Lexer - Tokenizer based on PHP grammar
 */
class PHPLexer {
    private string $source;
    private int $position;
    private int $line;
    private int $column;
    private array $tokens;

    public function __construct(string $source) {
        $this->source = $source;
        $this->position = 0;
        $this->line = 1;
        $this->column = 1;
        $this->tokens = [];
    }

    public function tokenize(): array {
        while ($this->position < strlen($this->source)) {
            $char = $this->source[$this->position];
            
            // Skip whitespace
            if (ctype_space($char)) {
                if ($char === "\n") {
                    $this->line++;
                    $this->column = 1;
                } else {
                    $this->column++;
                }
                $this->position++;
                continue;
            }
            
            // Skip comments
            if ($char === '/' && $this->position + 1 < strlen($this->source)) {
                $nextChar = $this->source[$this->position + 1];
                if ($nextChar === '/') {
                    $this->skipSingleLineComment();
                    continue;
                } elseif ($nextChar === '*') {
                    $this->skipMultiLineComment();
                    continue;
                }
            }
            
            // Skip # comments
            if ($char === '#') {
                $this->skipSingleLineComment();
                continue;
            }
            
            // Handle strings
            if ($char === '"' || $char === "'") {
                $this->tokens[] = $this->scanString($char);
                continue;
            }
            
            // Handle heredoc
            if ($char === '<' && $this->position + 2 < strlen($this->source) && 
                substr($this->source, $this->position, 3) === '<<<') {
                $this->tokens[] = $this->scanHeredoc();
                continue;
            }
            
            // Handle identifiers
            if (preg_match('/[a-zA-Z_\x7f-\xff]/', $char)) {
                $this->tokens[] = $this->scanIdentifier();
                continue;
            }
            
            // Handle numbers
            if (ctype_digit($char)) {
                $this->tokens[] = $this->scanNumber();
                continue;
            }
            
            // Handle operators and special characters
            $this->tokens[] = $this->scanOperator();
        }
        
        // Add EOF token
        $this->tokens[] = new PHPToken('EOF', '', $this->line, $this->column);
        return $this->tokens;
    }

    private function skipSingleLineComment(): void {
        while ($this->position < strlen($this->source) && $this->source[$this->position] !== "\n") {
            $this->position++;
        }
        $this->position++;
        $this->line++;
        $this->column = 1;
    }

    private function skipMultiLineComment(): void {
        $this->position += 2;
        while ($this->position < strlen($this->source)) {
            if ($this->source[$this->position] === '*' && $this->position + 1 < strlen($this->source) && 
                $this->source[$this->position + 1] === '/') {
                $this->position += 2;
                break;
            }
            if ($this->source[$this->position] === "\n") {
                $this->line++;
            }
            $this->position++;
        }
    }

    private function scanString(string $quote): PHPToken {
        $startLine = $this->line;
        $startColumn = $this->column;
        $value = '';
        $this->position++; // Skip opening quote
        
        while ($this->position < strlen($this->source)) {
            $char = $this->source[$this->position];
            
            if ($char === $quote) {
                $this->position++;
                $this->column++;
                return new PHPToken('STRING', $value, $startLine, $startColumn);
            }
            
            if ($char === '\\') {
                // Escape character
                $this->position++;
                $this->column++;
                if ($this->position < strlen($this->source)) {
                    $nextChar = $this->source[$this->position];
                    $value .= match($nextChar) {
                        'n' => "\n",
                        'r' => "\r",
                        't' => "\t",
                        '\\' => "\\",
                        '$' => "$",
                        '"' => '"',
                        "'" => "'",
                        default => $nextChar
                    };
                    $this->position++;
                    $this->column++;
                }
            } else {
                $value .= $char;
                $this->position++;
                $this->column++;
                if ($char === "\n") {
                    $this->line++;
                    $this->column = 1;
                }
            }
        }
        
        return new PHPToken('STRING', $value, $startLine, $startColumn);
    }

    private function scanHeredoc(): PHPToken {
        $startLine = $this->line;
        $startColumn = $this->column;
        $this->position += 3;
        
        // Parse label
        $label = $this->scanIdentifier()->value;
        $this->position++; // Skip newline after label
        $this->line++;
        $this->column = 1;
        
        $value = '';
        while ($this->position < strlen($this->source)) {
            $line = $this->readLine();
            if (trim($line) === $label) {
                break;
            }
            $value .= $line . "\n";
        }
        
        return new PHPToken('HEREDOC', rtrim($value), $startLine, $startColumn);
    }

    private function readLine(): string {
        $line = '';
        while ($this->position < strlen($this->source)) {
            $char = $this->source[$this->position];
            if ($char === "\n") {
                $this->position++;
                $this->line++;
                $this->column = 1;
                break;
            }
            $line .= $char;
            $this->position++;
            $this->column++;
        }
        return $line;
    }

    private function scanIdentifier(): PHPToken {
        $startLine = $this->line;
        $startColumn = $this->column;
        $value = '';
        
        while ($this->position < strlen($this->source)) {
            $char = $this->source[$this->position];
            if (preg_match('/[a-zA-Z_\x7f-\xff0-9]/', $char)) {
                $value .= $char;
                $this->position++;
                $this->column++;
            } else {
                break;
            }
        }
        
        $keywords = [
            'if', 'else', 'elseif', 'while', 'do', 'for', 'foreach', 'switch', 'case',
            'default', 'break', 'continue', 'return', 'throw', 'try', 'catch', 'finally',
            'class', 'interface', 'trait', 'enum', 'function', 'use', 'namespace', 'declare',
            'new', 'clone', 'yield', 'match', 'fn', 'abstract', 'final', 'public', 'protected',
            'private', 'static', 'readonly', 'const', 'var', 'echo', 'print', 'die', 'exit',
            'eval', 'isset', 'unset', 'empty', 'list', 'array', 'parent', 'self', 'static'
        ];
        
        if (in_array($value, $keywords)) {
            return new PHPToken('KEYWORD', $value, $startLine, $startColumn);
        }
        
        return new PHPToken('IDENTIFIER', $value, $startLine, $startColumn);
    }

    private function scanNumber(): PHPToken {
        $startLine = $this->line;
        $startColumn = $this->column;
        $value = '';
        $isFloat = false;
        
        // Handle hex
        if ($this->position + 1 < strlen($this->source) && 
            $this->source[$this->position] === '0' && 
            preg_match('/[xX]/', $this->source[$this->position + 1])) {
            $value .= $this->source[$this->position];
            $this->position++;
            $this->column++;
            $value .= $this->source[$this->position];
            $this->position++;
            $this->column++;
            while ($this->position < strlen($this->source) && 
                   preg_match('/[0-9a-fA-F]/', $this->source[$this->position])) {
                $value .= $this->source[$this->position];
                $this->position++;
                $this->column++;
            }
            return new PHPToken('INTEGER', $value, $startLine, $startColumn);
        }
        
        // Handle binary
        if ($this->position + 1 < strlen($this->source) && 
            $this->source[$this->position] === '0' && 
            preg_match('/[bB]/', $this->source[$this->position + 1])) {
            $value .= $this->source[$this->position];
            $this->position++;
            $this->column++;
            $value .= $this->source[$this->position];
            $this->position++;
            $this->column++;
            while ($this->position < strlen($this->source) && 
                   preg_match('/[01]/', $this->source[$this->position])) {
                $value .= $this->source[$this->position];
                $this->position++;
                $this->column++;
            }
            return new PHPToken('INTEGER', $value, $startLine, $startColumn);
        }
        
        // Parse number
        while ($this->position < strlen($this->source)) {
            $char = $this->source[$this->position];
            if (ctype_digit($char)) {
                $value .= $char;
                $this->position++;
                $this->column++;
            } elseif ($char === '.' && !$isFloat) {
                $value .= $char;
                $isFloat = true;
                $this->position++;
                $this->column++;
            } elseif (($char === 'e' || $char === 'E') && !$isFloat) {
                $value .= $char;
                $isFloat = true;
                $this->position++;
                $this->column++;
                if ($this->position < strlen($this->source) && 
                    preg_match('/[+-]/', $this->source[$this->position])) {
                    $value .= $this->source[$this->position];
                    $this->position++;
                    $this->column++;
                }
            } else {
                break;
            }
        }
        
        return new PHPToken($isFloat ? 'FLOAT' : 'INTEGER', $value, $startLine, $startColumn);
    }

    private function scanOperator(): PHPToken {
        $startLine = $this->line;
        $startColumn = $this->column;
        $char = $this->source[$this->position];
        
        // Multi-character operators
        $multiCharOperators = ['++', '--', '**', '<<', '>>', '<=>', '??', '&&', '||', 
                               '==', '===', '!=', '!==', '<=', '>=', '=>', '->'];
        
        foreach ($multiCharOperators as $op) {
            if ($this->position + strlen($op) - 1 < strlen($this->source) &&
                substr($this->source, $this->position, strlen($op)) === $op) {
                $this->position += strlen($op);
                $this->column += strlen($op);
                return new PHPToken('OPERATOR', $op, $startLine, $startColumn);
            }
        }
        
        // Single character operators
        $singleCharOperators = ['+', '-', '*', '/', '%', '=', '!', '<', '>', '&', '|', 
                                '^', '~', '?', ':', ';', ',', '(', ')', '[', ']', '{', 
                                '}', '.', '@', '$'];
        
        if (in_array($char, $singleCharOperators)) {
            $this->position++;
            $this->column++;
            return new PHPToken('OPERATOR', $char, $startLine, $startColumn);
        }
        
        // Unknown character
        $this->position++;
        $this->column++;
        return new PHPToken('UNKNOWN', $char, $startLine, $startColumn);
    }

    public function getTokens(): array { return $this->tokens; }
}

/**
 * PHP Parser - Based on PHP grammar
 */
class PHPParser {
    private array $tokens;
    private int $position;
    private array $ast;

    public function __construct(array $tokens) {
        $this->tokens = $tokens;
        $this->position = 0;
        $this->ast = [];
    }

    public function parse(): array {
        return $this->parseProgram();
    }

    private function parseProgram(): array {
        $program = [
            'type' => 'program',
            'statements' => []
        ];

        while ($this->peek()->type !== 'EOF') {
            $stmt = $this->parseStatement();
            if ($stmt !== null) {
                $program['statements'][] = $stmt;
            }
        }

        return $program;
    }

    private function parseStatement(): ?array {
        $token = $this->peek();

        switch ($token->type) {
            case 'KEYWORD':
                switch ($token->value) {
                    case 'if': return $this->parseIfStatement();
                    case 'switch': return $this->parseSwitchStatement();
                    case 'while': return $this->parseWhileStatement();
                    case 'do': return $this->parseDoStatement();
                    case 'for': return $this->parseForStatement();
                    case 'foreach': return $this->parseForeachStatement();
                    case 'return': return $this->parseReturnStatement();
                    case 'throw': return $this->parseThrowStatement();
                    case 'try': return $this->parseTryStatement();
                    case 'class': return $this->parseClassDeclaration();
                    case 'interface': return $this->parseInterfaceDeclaration();
                    case 'trait': return $this->parseTraitDeclaration();
                    case 'enum': return $this->parseEnumDeclaration();
                    case 'function': return $this->parseFunctionDeclaration();
                    case 'namespace': return $this->parseNamespaceStatement();
                    case 'use': return $this->parseUseStatement();
                    case 'declare': return $this->parseDeclareStatement();
                    case 'echo': return $this->parseEchoStatement();
                    case 'new': return $this->parseNewExpression();
                    default: return $this->parseExpressionStatement();
                }
            case 'IDENTIFIER':
                return $this->parseExpressionStatement();
            case 'OPERATOR':
                if ($token->value === ';') {
                    $this->consume();
                    return ['type' => 'empty'];
                }
                return $this->parseExpressionStatement();
            default:
                return $this->parseExpressionStatement();
        }
    }

    private function parseIfStatement(): array {
        $this->consume(); // if
        $this->expect('OPERATOR', '(');
        $condition = $this->parseExpression();
        $this->expect('OPERATOR', ')');
        $then = $this->parseStatement();

        $ifStmt = [
            'type' => 'if',
            'condition' => $condition,
            'then' => $then
        ];

        if ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'elseif') {
            $ifStmt['elseif'] = [];
            while ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'elseif') {
                $this->consume();
                $this->expect('OPERATOR', '(');
                $cond = $this->parseExpression();
                $this->expect('OPERATOR', ')');
                $stmt = $this->parseStatement();
                $ifStmt['elseif'][] = ['condition' => $cond, 'statement' => $stmt];
            }
        }

        if ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'else') {
            $this->consume();
            $ifStmt['else'] = $this->parseStatement();
        }

        return $ifStmt;
    }

    private function parseSwitchStatement(): array {
        $this->consume(); // switch
        $this->expect('OPERATOR', '(');
        $expression = $this->parseExpression();
        $this->expect('OPERATOR', ')');
        $this->expect('OPERATOR', '{');

        $cases = [];
        while ($this->peek()->type !== 'OPERATOR' || $this->peek()->value !== '}') {
            if ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'case') {
                $this->consume();
                $value = $this->parseExpression();
                $this->expect('OPERATOR', ':');
                $statements = [];
                while ($this->peek()->type !== 'KEYWORD' || 
                       ($this->peek()->value !== 'case' && $this->peek()->value !== 'default')) {
                    $stmt = $this->parseStatement();
                    if ($stmt !== null) {
                        $statements[] = $stmt;
                    }
                    if ($this->peek()->type === 'EOF') break;
                }
                $cases[] = ['type' => 'case', 'value' => $value, 'statements' => $statements];
            } elseif ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'default') {
                $this->consume();
                $this->expect('OPERATOR', ':');
                $statements = [];
                while ($this->peek()->type !== 'OPERATOR' || $this->peek()->value !== '}') {
                    $stmt = $this->parseStatement();
                    if ($stmt !== null) {
                        $statements[] = $stmt;
                    }
                    if ($this->peek()->type === 'EOF') break;
                }
                $cases[] = ['type' => 'default', 'statements' => $statements];
            }
        }

        $this->expect('OPERATOR', '}');
        return ['type' => 'switch', 'expression' => $expression, 'cases' => $cases];
    }

    private function parseWhileStatement(): array {
        $this->consume(); // while
        $this->expect('OPERATOR', '(');
        $condition = $this->parseExpression();
        $this->expect('OPERATOR', ')');
        $body = $this->parseStatement();
        return ['type' => 'while', 'condition' => $condition, 'body' => $body];
    }

    private function parseDoStatement(): array {
        $this->consume(); // do
        $body = $this->parseStatement();
        $this->expect('KEYWORD', 'while');
        $this->expect('OPERATOR', '(');
        $condition = $this->parseExpression();
        $this->expect('OPERATOR', ')');
        $this->expect('OPERATOR', ';');
        return ['type' => 'do_while', 'condition' => $condition, 'body' => $body];
    }

    private function parseForStatement(): array {
        $this->consume(); // for
        $this->expect('OPERATOR', '(');
        
        $init = null;
        if ($this->peek()->value !== ';') {
            $init = $this->parseExpression();
        }
        $this->expect('OPERATOR', ';');
        
        $condition = null;
        if ($this->peek()->value !== ';') {
            $condition = $this->parseExpression();
        }
        $this->expect('OPERATOR', ';');
        
        $increment = null;
        if ($this->peek()->value !== ')') {
            $increment = $this->parseExpression();
        }
        $this->expect('OPERATOR', ')');
        
        $body = $this->parseStatement();
        return ['type' => 'for', 'init' => $init, 'condition' => $condition, 
                'increment' => $increment, 'body' => $body];
    }

    private function parseForeachStatement(): array {
        $this->consume(); // foreach
        $this->expect('OPERATOR', '(');
        $expression = $this->parseExpression();
        $this->expect('KEYWORD', 'as');
        
        $byRef = false;
        if ($this->peek()->value === '&') {
            $this->consume();
            $byRef = true;
        }
        
        $value = $this->parseVariable();
        $key = null;
        
        if ($this->peek()->value === '=>') {
            $this->consume();
            if ($this->peek()->value === '&') {
                $this->consume();
            }
            $key = $value;
            $value = $this->parseVariable();
        }
        
        $this->expect('OPERATOR', ')');
        $body = $this->parseStatement();
        
        return ['type' => 'foreach', 'expression' => $expression, 'key' => $key, 
                'value' => $value, 'body' => $body, 'by_ref' => $byRef];
    }

    private function parseReturnStatement(): array {
        $this->consume(); // return
        $expression = null;
        if ($this->peek()->value !== ';') {
            $expression = $this->parseExpression();
        }
        $this->expect('OPERATOR', ';');
        return ['type' => 'return', 'expression' => $expression];
    }

    private function parseThrowStatement(): array {
        $this->consume(); // throw
        $expression = $this->parseExpression();
        $this->expect('OPERATOR', ';');
        return ['type' => 'throw', 'expression' => $expression];
    }

    private function parseTryStatement(): array {
        $this->consume(); // try
        $body = $this->parseStatement();
        
        $catches = [];
        while ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'catch') {
            $this->consume();
            $this->expect('OPERATOR', '(');
            $type = $this->parseType();
            $variable = $this->parseVariable();
            $this->expect('OPERATOR', ')');
            $catchBody = $this->parseStatement();
            $catches[] = ['type' => $type, 'variable' => $variable, 'body' => $catchBody];
        }
        
        $finally = null;
        if ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'finally') {
            $this->consume();
            $finally = $this->parseStatement();
        }
        
        return ['type' => 'try', 'body' => $body, 'catches' => $catches, 'finally' => $finally];
    }

    private function parseFunctionDeclaration(): array {
        $this->consume(); // function
        $byRef = false;
        if ($this->peek()->value === '&') {
            $this->consume();
            $byRef = true;
        }
        
        $name = $this->expect('IDENTIFIER')->value;
        $this->expect('OPERATOR', '(');
        $params = $this->parseParameterList();
        $this->expect('OPERATOR', ')');
        
        $returnType = null;
        if ($this->peek()->value === ':') {
            $this->consume();
            $nullable = false;
            if ($this->peek()->value === '?') {
                $this->consume();
                $nullable = true;
            }
            $returnType = $this->parseType();
            if ($nullable && $returnType !== null) {
                $returnType['nullable'] = true;
            }
        }
        
        $body = $this->parseStatement();
        return ['type' => 'function', 'name' => $name, 'params' => $params, 
                'return_type' => $returnType, 'body' => $body, 'by_ref' => $byRef];
    }

    private function parseClassDeclaration(): array {
        $this->consume(); // class
        $abstract = false;
        $final = false;
        $readonly = false;
        
        while (true) {
            if ($this->peek()->type === 'KEYWORD') {
                switch ($this->peek()->value) {
                    case 'abstract': $abstract = true; $this->consume(); break;
                    case 'final': $final = true; $this->consume(); break;
                    case 'readonly': $readonly = true; $this->consume(); break;
                    default: break 2;
                }
            } else {
                break;
            }
        }
        
        $name = $this->expect('IDENTIFIER')->value;
        $extends = null;
        if ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'extends') {
            $this->consume();
            $extends = $this->expect('IDENTIFIER')->value;
        }
        
        $implements = [];
        if ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'implements') {
            $this->consume();
            do {
                $implements[] = $this->expect('IDENTIFIER')->value;
            } while ($this->peek()->value === ',');
        }
        
        $this->expect('OPERATOR', '{');
        $body = $this->parseClassBody();
        $this->expect('OPERATOR', '}');
        
        return [
            'type' => 'class', 'name' => $name, 'extends' => $extends,
            'implements' => $implements, 'body' => $body,
            'abstract' => $abstract, 'final' => $final, 'readonly' => $readonly
        ];
    }

    private function parseClassBody(): array {
        $members = [];
        
        while ($this->peek()->type !== 'OPERATOR' || $this->peek()->value !== '}') {
            $token = $this->peek();
            switch ($token->type) {
                case 'KEYWORD':
                    switch ($token->value) {
                        case 'public':
                        case 'protected':
                        case 'private':
                        case 'static':
                            $members[] = $this->parsePropertyOrMethod();
                            break;
                        case 'const':
                            $members[] = $this->parseConstDeclaration();
                            break;
                        case 'use':
                            $members[] = $this->parseTraitUse();
                            break;
                        case 'function':
                            $members[] = $this->parseMethodDeclaration();
                            break;
                        default:
                            $this->consume();
                    }
                    break;
                case 'IDENTIFIER':
                    $members[] = $this->parsePropertyOrMethod();
                    break;
                default:
                    $this->consume();
            }
        }
        
        return $members;
    }

    private function parsePropertyOrMethod(): array {
        $visibility = 'public';
        $static = false;
        $readonly = false;
        
        while (true) {
            $token = $this->peek();
            if ($token->type === 'KEYWORD') {
                switch ($token->value) {
                    case 'public': $visibility = 'public'; $this->consume(); break;
                    case 'protected': $visibility = 'protected'; $this->consume(); break;
                    case 'private': $visibility = 'private'; $this->consume(); break;
                    case 'static': $static = true; $this->consume(); break;
                    case 'readonly': $readonly = true; $this->consume(); break;
                    default: break 2;
                }
            } else {
                break;
            }
        }
        
        if ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'function') {
            return $this->parseMethodDeclaration($visibility, $static);
        } else {
            return $this->parsePropertyDeclaration($visibility, $static, $readonly);
        }
    }

    private function parseMethodDeclaration($visibility = 'public', $static = false): array {
        $this->consume(); // function
        $byRef = false;
        if ($this->peek()->value === '&') {
            $this->consume();
            $byRef = true;
        }
        
        $name = $this->expect('IDENTIFIER')->value;
        $this->expect('OPERATOR', '(');
        $params = $this->parseParameterList();
        $this->expect('OPERATOR', ')');
        
        $returnType = null;
        if ($this->peek()->value === ':') {
            $this->consume();
            $returnType = $this->parseType();
        }
        
        $body = $this->parseStatement();
        
        return [
            'type' => 'method', 'name' => $name, 'params' => $params,
            'return_type' => $returnType, 'body' => $body,
            'visibility' => $visibility, 'static' => $static, 'by_ref' => $byRef
        ];
    }

    private function parsePropertyDeclaration($visibility = 'public', $static = false, $readonly = false): array {
        $type = null;
        if ($this->peek()->type === 'IDENTIFIER' && 
            preg_match('/^(int|float|string|bool|array|callable|iterable|void|never|mixed|object|parent|self|static)$/', 
                       $this->peek()->value)) {
            $type = $this->parseType();
        }
        
        $name = $this->parseVariable();
        $default = null;
        if ($this->peek()->value === '=') {
            $this->consume();
            $default = $this->parseExpression();
        }
        
        $this->expect('OPERATOR', ';');
        
        return [
            'type' => 'property', 'name' => $name['name'] ?? '',
            'type' => $type, 'default' => $default,
            'visibility' => $visibility, 'static' => $static, 'readonly' => $readonly
        ];
    }

    private function parseConstDeclaration(): array {
        $this->consume(); // const
        $visibility = 'public';
        
        $name = $this->expect('IDENTIFIER')->value;
        $this->expect('OPERATOR', '=');
        $value = $this->parseExpression();
        $this->expect('OPERATOR', ';');
        
        return ['type' => 'const', 'name' => $name, 'value' => $value, 'visibility' => $visibility];
    }

    private function parseTraitUse(): array {
        $this->consume(); // use
        $traits = [];
        do {
            $traits[] = $this->expect('IDENTIFIER')->value;
        } while ($this->peek()->value === ',');
        
        $adaptations = [];
        if ($this->peek()->value === '{') {
            $this->consume();
            while ($this->peek()->value !== '}') {
                // Parse trait adaptation (simplified)
                $adaptations[] = ['type' => 'adaptation'];
            }
            $this->consume(); // }
        }
        
        $this->expect('OPERATOR', ';');
        return ['type' => 'trait_use', 'traits' => $traits, 'adaptations' => $adaptations];
    }

    private function parseInterfaceDeclaration(): array {
        $this->consume(); // interface
        $name = $this->expect('IDENTIFIER')->value;
        $extends = [];
        
        if ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'extends') {
            $this->consume();
            do {
                $extends[] = $this->expect('IDENTIFIER')->value;
            } while ($this->peek()->value === ',');
        }
        
        $this->expect('OPERATOR', '{');
        $body = $this->parseClassBody();
        $this->expect('OPERATOR', '}');
        
        return ['type' => 'interface', 'name' => $name, 'extends' => $extends, 'body' => $body];
    }

    private function parseTraitDeclaration(): array {
        $this->consume(); // trait
        $name = $this->expect('IDENTIFIER')->value;
        $this->expect('OPERATOR', '{');
        $body = $this->parseClassBody();
        $this->expect('OPERATOR', '}');
        
        return ['type' => 'trait', 'name' => $name, 'body' => $body];
    }

    private function parseEnumDeclaration(): array {
        $this->consume(); // enum
        $name = $this->expect('IDENTIFIER')->value;
        $backingType = null;
        
        if ($this->peek()->value === ':') {
            $this->consume();
            $backingType = $this->expect('IDENTIFIER')->value;
        }
        
        $implements = [];
        if ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'implements') {
            $this->consume();
            do {
                $implements[] = $this->expect('IDENTIFIER')->value;
            } while ($this->peek()->value === ',');
        }
        
        $this->expect('OPERATOR', '{');
        $cases = [];
        $members = [];
        
        while ($this->peek()->value !== '}') {
            if ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'case') {
                $this->consume();
                $caseName = $this->expect('IDENTIFIER')->value;
                $caseValue = null;
                if ($this->peek()->value === '=') {
                    $this->consume();
                    $caseValue = $this->parseExpression();
                }
                $this->expect('OPERATOR', ';');
                $cases[] = ['name' => $caseName, 'value' => $caseValue];
            } else {
                $members[] = $this->parsePropertyOrMethod();
            }
        }
        
        $this->expect('OPERATOR', '}');
        return ['type' => 'enum', 'name' => $name, 'backing_type' => $backingType,
                'implements' => $implements, 'cases' => $cases, 'body' => $members];
    }

    private function parseNamespaceStatement(): ?array {
        $this->consume(); // namespace
        $name = null;
        
        if ($this->peek()->type === 'IDENTIFIER') {
            $name = $this->expect('IDENTIFIER')->value;
            if ($this->peek()->value === ';') {
                $this->consume();
                return ['type' => 'namespace', 'name' => $name];
            }
        }
        
        if ($this->peek()->value === '{') {
            $this->consume();
            $statements = [];
            while ($this->peek()->value !== '}') {
                $stmt = $this->parseStatement();
                if ($stmt !== null) {
                    $statements[] = $stmt;
                }
            }
            $this->expect('OPERATOR', '}');
            return ['type' => 'namespace', 'name' => $name, 'statements' => $statements];
        }
        
        return null;
    }

    private function parseUseStatement(): array {
        $this->consume(); // use
        $items = [];
        $isFunction = false;
        $isConst = false;
        
        if ($this->peek()->value === 'function') {
            $isFunction = true;
            $this->consume();
        } elseif ($this->peek()->value === 'const') {
            $isConst = true;
            $this->consume();
        }
        
        do {
            $name = $this->expect('IDENTIFIER')->value;
            $alias = null;
            if ($this->peek()->value === 'as') {
                $this->consume();
                $alias = $this->expect('IDENTIFIER')->value;
            }
            $items[] = [
                'name' => $name, 
                'alias' => $alias, 
                'type' => $isFunction ? 'function' : ($isConst ? 'const' : 'class')
            ];
        } while ($this->peek()->value === ',');
        
        $this->expect('OPERATOR', ';');
        return ['type' => 'use', 'items' => $items];
    }

    private function parseDeclareStatement(): array {
        $this->consume(); // declare
        $this->expect('OPERATOR', '(');
        $directives = [];
        
        do {
            $name = $this->expect('IDENTIFIER')->value;
            $this->expect('OPERATOR', '=');
            $value = $this->parseExpression();
            $directives[$name] = $value;
        } while ($this->peek()->value === ',');
        
        $this->expect('OPERATOR', ')');
        $statement = $this->parseStatement();
        
        return ['type' => 'declare', 'directives' => $directives, 'statement' => $statement];
    }

    private function parseEchoStatement(): array {
        $this->consume(); // echo
        $expressions = [];
        do {
            $expressions[] = $this->parseExpression();
        } while ($this->peek()->value === ',');
        $this->expect('OPERATOR', ';');
        return ['type' => 'echo', 'expressions' => $expressions];
    }

    private function parseExpressionStatement(): array {
        $expression = $this->parseExpression();
        $this->expect('OPERATOR', ';');
        return ['type' => 'expression_statement', 'expression' => $expression];
    }

    private function parseNewExpression(): array {
        $this->consume(); // new
        $className = $this->expect('IDENTIFIER')->value;
        $args = [];
        if ($this->peek()->value === '(') {
            $this->consume();
            $args = $this->parseArgumentList();
            $this->expect('OPERATOR', ')');
        }
        return ['type' => 'new', 'class' => $className, 'arguments' => $args];
    }

    private function parseParameterList(): array {
        $params = [];
        if ($this->peek()->value === ')') {
            return $params;
        }
        
        do {
            $param = [];
            
            if ($this->peek()->type === 'KEYWORD' && 
                preg_match('/^(public|protected|private)$/', $this->peek()->value)) {
                $param['visibility'] = $this->consume()->value;
            }
            
            if ($this->peek()->type === 'KEYWORD' && $this->peek()->value === 'readonly') {
                $param['readonly'] = true;
                $this->consume();
            }
            
            if ($this->peek()->type === 'IDENTIFIER' && 
                preg_match('/^(int|float|string|bool|array|callable|iterable|void|never|mixed|object|parent|self|static)$/', 
                           $this->peek()->value)) {
                $param['type'] = $this->parseType();
            }
            
            if ($this->peek()->value === '&') {
                $this->consume();
                $param['by_ref'] = true;
            }
            
            if ($this->peek()->value === '...') {
                $this->consume();
                $param['variadic'] = true;
            }
            
            $param['name'] = $this->parseVariable()['name'] ?? '';
            
            if ($this->peek()->value === '=') {
                $this->consume();
                $param['default'] = $this->parseExpression();
            }
            
            $params[] = $param;
        } while ($this->peek()->value === ',');
        
        return $params;
    }

    private function parseArgumentList(): array {
        $args = [];
        if ($this->peek()->value === ')') {
            return $args;
        }
        
        do {
            $arg = [];
            if ($this->peek()->value === '...') {
                $this->consume();
                $arg['spread'] = true;
            }
            $arg['value'] = $this->parseExpression();
            $args[] = $arg;
        } while ($this->peek()->value === ',');
        
        return $args;
    }

    private function parseExpression(): array {
        return $this->parseAssignmentExpression();
    }

    private function parseAssignmentExpression(): array {
        $expr = $this->parseConditionalExpression();
        
        if (preg_match('/^(=|\\+=|-=|\\*=|/=|\\.=|%=|&=|\\|=|\\^=|<<=|>>=|\\*\\*=|\?\?=)$/', 
                       $this->peek()->value)) {
            $operator = $this->consume()->value;
            $right = $this->parseAssignmentExpression();
            return ['type' => 'assignment', 'operator' => $operator, 
                    'left' => $expr, 'right' => $right];
        }
        
        return $expr;
    }

    private function parseConditionalExpression(): array {
        $expr = $this->parseLogicalOrExpression();
        
        if ($this->peek()->value === '?') {
            $this->consume();
            if ($this->peek()->value === ':') {
                // Elvis operator
                $this->consume();
                $else = $this->parseExpression();
                return ['type' => 'conditional', 'condition' => $expr, 
                        'else' => $else, 'elvis' => true];
            } else {
                $then = $this->parseExpression();
                $this->expect('OPERATOR', ':');
                $else = $this->parseExpression();
                return ['type' => 'conditional', 'condition' => $expr, 
                        'then' => $then, 'else' => $else];
            }
        }
        
        return $expr;
    }

    private function parseLogicalOrExpression(): array {
        $expr = $this->parseLogicalAndExpression();
        
        while (preg_match('/^(or|\\|\\|)$/', $this->peek()->value)) {
            $operator = $this->consume()->value;
            $right = $this->parseLogicalAndExpression();
            $expr = ['type' => 'binary_op', 'operator' => $operator, 
                     'left' => $expr, 'right' => $right];
        }
        
        return $expr;
    }

    private function parseLogicalAndExpression(): array {
        $expr = $this->parseBitwiseOrExpression();
        
        while (preg_match('/^(and|&&)$/', $this->peek()->value)) {
            $operator = $this->consume()->value;
            $right = $this->parseBitwiseOrExpression();
            $expr = ['type' => 'binary_op', 'operator' => $operator, 
                     'left' => $expr, 'right' => $right];
        }
        
        return $expr;
    }

    private function parseBitwiseOrExpression(): array {
        $expr = $this->parseBitwiseXorExpression();
        
        while ($this->peek()->value === '|') {
            $operator = $this->consume()->value;
            $right = $this->parseBitwiseXorExpression();
            $expr = ['type' => 'binary_op', 'operator' => $operator, 
                     'left' => $expr, 'right' => $right];
        }
        
        return $expr;
    }

    private function parseBitwiseXorExpression(): array {
        $expr = $this->parseBitwiseAndExpression();
        
        while ($this->peek()->value === '^') {
            $operator = $this->consume()->value;
            $right = $this->parseBitwiseAndExpression();
            $expr = ['type' => 'binary_op', 'operator' => $operator, 
                     'left' => $expr, 'right' => $right];
        }
        
        return $expr;
    }

    private function parseBitwiseAndExpression(): array {
        $expr = $this->parseEqualityExpression();
        
        while ($this->peek()->value === '&') {
            $operator = $this->consume()->value;
            $right = $this->parseEqualityExpression();
            $expr = ['type' => 'binary_op', 'operator' => $operator, 
                     'left' => $expr, 'right' => $right];
        }
        
        return $expr;
    }

    private function parseEqualityExpression(): array {
        $expr = $this->parseComparativeExpression();
        
        while (preg_match('/^(==|!=|===|!==|<=>)$/', $this->peek()->value)) {
            $operator = $this->consume()->value;
            $right = $this->parseComparativeExpression();
            $expr = ['type' => 'binary_op', 'operator' => $operator, 
                     'left' => $expr, 'right' => $right];
        }
        
        return $expr;
    }

    private function parseComparativeExpression(): array {
        $expr = $this->parseShiftExpression();
        
        while (preg_match('/^(<|>|<=|>=)$/', $this->peek()->value)) {
            $operator = $this->consume()->value;
            $right = $this->parseShiftExpression();
            $expr = ['type' => 'binary_op', 'operator' => $operator, 
                     'left' => $expr, 'right' => $right];
        }
        
        return $expr;
    }

    private function parseShiftExpression(): array {
        $expr = $this->parseAdditiveExpression();
        
        while (preg_match('/^(<<|>>)$/', $this->peek()->value)) {
            $operator = $this->consume()->value;
            $right = $this->parseAdditiveExpression();
            $expr = ['type' => 'binary_op', 'operator' => $operator, 
                     'left' => $expr, 'right' => $right];
        }
        
        return $expr;
    }

    private function parseAdditiveExpression(): array {
        $expr = $this->parseMultiplicativeExpression();
        
        while (preg_match('/^(\\+|-|\\.)$/', $this->peek()->value)) {
            $operator = $this->consume()->value;
            $right = $this->parseMultiplicativeExpression();
            $expr = ['type' => 'binary_op', 'operator' => $operator, 
                     'left' => $expr, 'right' => $right];
        }
        
        return $expr;
    }

    private function parseMultiplicativeExpression(): array {
        $expr = $this->parseExponentiationExpression();
        
        while (preg_match('/^(\\*|/|%)$/', $this->peek()->value)) {
            $operator = $this->consume()->value;
            $right = $this->parseExponentiationExpression();
            $expr = ['type' => 'binary_op', 'operator' => $operator, 
                     'left' => $expr, 'right' => $right];
        }
        
        return $expr;
    }

    private function parseExponentiationExpression(): array {
        $expr = $this->parseUnaryExpression();
        
        while ($this->peek()->value === '**') {
            $operator = $this->consume()->value;
            $right = $this->parseUnaryExpression();
            $expr = ['type' => 'binary_op', 'operator' => $operator, 
                     'left' => $expr, 'right' => $right];
        }
        
        return $expr;
    }

    private function parseUnaryExpression(): array {
        if (preg_match('/^(\\+|-|!|~|@)$/', $this->peek()->value)) {
            $operator = $this->consume()->value;
            $expr = $this->parseUnaryExpression();
            return ['type' => 'unary_op', 'operator' => $operator, 'operand' => $expr];
        }
        
        return $this->parsePostfixExpression();
    }

    private function parsePostfixExpression(): array {
        $expr = $this->parsePrimaryExpression();
        
        while (true) {
            if ($this->peek()->value === '[') {
                $this->consume();
                $index = $this->parseExpression();
                $this->expect('OPERATOR', ']');
                $expr = ['type' => 'array_access', 'array' => $expr, 'index' => $index];
            } elseif ($this->peek()->value === '{') {
                $this->consume();
                $index = $this->parseExpression();
                $this->expect('OPERATOR', '}');
                $expr = ['type' => 'array_access', 'array' => $expr, 'index' => $index];
            } elseif ($this->peek()->value === '->') {
                $this->consume();
                $property = $this->expect('IDENTIFIER')->value;
                $expr = ['type' => 'property_access', 'object' => $expr, 'property' => $property];
            } elseif ($this->peek()->value === '?->') {
                $this->consume();
                $property = $this->expect('IDENTIFIER')->value;
                $expr = ['type' => 'nullsafe_property_access', 'object' => $expr, 'property' => $property];
            } elseif ($this->peek()->value === '::') {
                $this->consume();
                $property = $this->expect('IDENTIFIER')->value;
                $expr = ['type' => 'static_property_access', 'class' => $expr, 'property' => $property];
            } elseif ($this->peek()->value === '(') {
                $this->consume();
                $args = $this->parseArgumentList();
                $this->expect('OPERATOR', ')');
                $expr = ['type' => 'function_call', 'function' => $expr, 'arguments' => $args];
            } elseif ($this->peek()->value === '++') {
                $this->consume();
                $expr = ['type' => 'post_increment', 'operand' => $expr];
            } elseif ($this->peek()->value === '--') {
                $this->consume();
                $expr = ['type' => 'post_decrement', 'operand' => $expr];
            } else {
                break;
            }
        }
        
        return $expr;
    }

    private function parsePrimaryExpression(): array {
        $token = $this->peek();
        
        switch ($token->type) {
            case 'IDENTIFIER':
                $value = $this->consume()->value;
                if ($value === 'true' || $value === 'false') {
                    return ['type' => 'literal', 'value' => filter_var($value, FILTER_VALIDATE_BOOLEAN), 
                            'literal_type' => 'boolean'];
                } elseif ($value === 'null') {
                    return ['type' => 'literal', 'value' => null, 'literal_type' => 'null'];
                }
                return ['type' => 'variable', 'name' => $value];
            case 'STRING':
                $value = $this->consume()->value;
                return ['type' => 'literal', 'value' => $value, 'literal_type' => 'string'];
            case 'INTEGER':
                $value = intval($this->consume()->value);
                return ['type' => 'literal', 'value' => $value, 'literal_type' => 'integer'];
            case 'FLOAT':
                $value = floatval($this->consume()->value);
                return ['type' => 'literal', 'value' => $value, 'literal_type' => 'float'];
            case 'HEREDOC':
                $value = $this->consume()->value;
                return ['type' => 'literal', 'value' => $value, 'literal_type' => 'string'];
            case 'OPERATOR':
                if ($token->value === '(') {
                    $this->consume();
                    $expr = $this->parseExpression();
                    $this->expect('OPERATOR', ')');
                    return $expr;
                } elseif ($token->value === '$') {
                    return $this->parseVariable();
                } elseif ($token->value === '[') {
                    return $this->parseArrayCreation();
                }
        }
        
        return $this->parseVariable();
    }

    private function parseVariable(): array {
        if ($this->peek()->value === '$') {
            $this->consume();
            if ($this->peek()->type === 'IDENTIFIER') {
                $name = $this->consume()->value;
                return ['type' => 'variable', 'name' => $name];
            } elseif ($this->peek()->value === '{') {
                $this->consume();
                $expr = $this->parseExpression();
                $this->expect('OPERATOR', '}');
                return ['type' => 'variable_variable', 'expression' => $expr];
            }
        }
        
        if ($this->peek()->type === 'IDENTIFIER') {
            $name = $this->consume()->value;
            return ['type' => 'variable', 'name' => $name];
        }
        
        return ['type' => 'variable', 'name' => ''];
    }

    private function parseArrayCreation(): array {
        $this->consume(); // [
        $items = [];
        
        if ($this->peek()->value !== ']') {
            do {
                $item = [];
                if ($this->peek()->value === '...') {
                    $this->consume();
                    $item['spread'] = true;
                }
                $item['key'] = null;
                $item['value'] = $this->parseExpression();
                
                if ($this->peek()->value === '=>') {
                    $this->consume();
                    $item['key'] = $item['value'];
                    $item['value'] = $this->parseExpression();
                }
                $items[] = $item;
            } while ($this->peek()->value === ',');
        }
        
        $this->expect('OPERATOR', ']');
        return ['type' => 'array_creation', 'items' => $items];
    }

    private function parseType(): ?array {
        $token = $this->peek();
        if ($token->type === 'IDENTIFIER') {
            $typeName = $this->consume()->value;
            return ['type' => $typeName, 'nullable' => false];
        }
        return null;
    }

    private function peek(): PHPToken {
        if ($this->position < count($this->tokens)) {
            return $this->tokens[$this->position];
        }
        return new PHPToken('EOF', '', 0, 0);
    }

    private function consume(): ?PHPToken {
        $token = $this->peek();
        if ($token !== null) {
            $this->position++;
            return $token;
        }
        return null;
    }

    private function expect(string $type, ?string $value = null): PHPToken {
        $token = $this->peek();
        if ($token->type === $type && ($value === null || $token->value === $value)) {
            return $this->consume();
        }
        throw new RuntimeException("Expected {$type} '{$value}' but got {$token->type} '{$token->value}' at line {$token->line}:{$token->column}");
    }
}

// ============================================================
// 5. SERVER INITIALIZATION
// ============================================================

// Initialize logger
$logger = MicronautLogger::getInstance();

// Initialize K'UHUL π
$kuhul = new KuhulRuntime();
$logger->phase("π K'UHUL runtime initialized");

// Initialize Micronaut µ
$micronaut = new Micronaut("PrimaryOrchestrator", "orchestrator");
$micronaut->createFold("compute", "compute");
$micronaut->orchestrate("compute");
$micronaut->createFold("reasoning", "reasoning");
$micronaut->orchestrate("reasoning");
$micronaut->createField("working", "working", "volatile");
$micronaut->createField("episodic", "episodic", "persistent");

// Create an agent
$agent = new MicronautAgent("Assistant", "helper");
$agent->addTool("echo");
$agent->addTool("dns_lookup");
$agent->addGoal("Help users with their requests", 1.0);
$agent->addConstraint("Be helpful and accurate");
$micronaut->addAgent($agent);

$logger->orchestrate("µ Micronaut initialized");
$logger->info("⟁ K'UHUL π · Micronaut µ · PHP Runtime v3.0 ⟁");

// ============================================================
// 6. SERVER ROUTING
// ============================================================

// Simple router
$routes = [];

function addRoute(string $method, string $path, callable $handler): void {
    global $routes;
    $routes[strtoupper($method) . ':' . $path] = $handler;
}

function dispatch(string $method, string $uri, array $input = []): array {
    global $routes, $kuhul, $micronaut;
    
    // K'UHUL perceives the request
    $kuhul->perceive([
        'method' => $method,
        'uri' => $uri,
        'input' => $input,
        'timestamp' => date('c')
    ]);
    
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    $path = rtrim($path, '/');
    if (empty($path)) $path = '/';
    
    $key = strtoupper($method) . ':' . $path;
    
    if (isset($routes[$key])) {
        $result = $routes[$key]($input);
        $kuhul->project('result');
        return $result;
    }
    
    // K'UHUL endpoints
    if ($path === '/kuhul') {
        return handleKuhul($method, $input);
    }
    
    // Micronaut endpoints
    if ($path === '/micronaut') {
        return handleMicronaut($method, $input);
    }
    
    $kuhul->consolidate();
    return ['error' => 'Not Found', 'path' => $path];
}

function handleKuhul(string $method, array $input): array {
    global $kuhul;
    
    if ($method === 'GET') {
        return [
            'kuhul' => true,
            'phase' => $kuhul->getCurrentPhase()->getGlyph(),
            'phases' => array_keys(KUHUL_GLYPHS),
            'meaning' => KUHUL_GLYPHS,
            'laws' => array_keys($kuhul->getLaws()),
            'state' => $kuhul->getState(),
            'history' => array_map(fn($p) => $p->getGlyph(), $kuhul->getPhaseHistory()),
            'version' => 'π 3.0.0',
            'invariant' => 'Micronaut orchestrates. K\'UHUL enforces. They are orthogonal.'
        ];
    }
    
    if ($method === 'POST' && isset($input['type'])) {
        switch ($input['type']) {
            case 'phase':
                $phase = $input['phase'] ?? 'Pop';
                $kuhul->transitionPhase($phase);
                return [
                    'transitioned' => true,
                    'from' => $kuhul->getPhaseHistory()[count($kuhul->getPhaseHistory()) - 1]->getGlyph(),
                    'to' => $phase,
                    'meaning' => KUHUL_GLYPHS[$phase]
                ];
            case 'law':
                $lawName = $input['name'] ?? 'custom_law';
                $definition = $input['definition'] ?? [
                    'phases' => ['Pop', 'Wo', 'Yax', 'Sek', "Ch'en", 'Xul'],
                    'invariants' => [
                        'collapse_only' => true,
                        'field_perception' => true,
                        'compression_law' => true,
                        'unreachable_states' => true
                    ]
                ];
                $kuhul->defineLaw($lawName, $definition);
                return [
                    'defined' => $lawName,
                    'hash' => substr($kuhul->getLaws()[$lawName]->getHash(), 0, 8)
                ];
            case 'enforce':
                $lawName = $input['law'] ?? 'collapse_only';
                $state = $input['state'] ?? null;
                return [
                    'enforced' => $kuhul->enforceLaw($lawName, $state),
                    'state' => $kuhul->getState()
                ];
            default:
                return ['error' => "Unknown K'UHUL operation: {$input['type']}"];
        }
    }
    
    return ['error' => 'Invalid K\'UHUL request'];
}

function handleMicronaut(string $method, array $input): array {
    global $micronaut;
    
    if ($method === 'GET') {
        return [
            'micronaut' => true,
            'status' => $micronaut->getStatus(),
            'version' => 'µ 3.0.0',
            'invariant' => 'Micronaut orchestrates contexts. K\'UHUL enforces law.'
        ];
    }
    
    if ($method === 'POST' && isset($input['type'])) {
        switch ($input['type']) {
            case 'fold':
                $name = $input['name'] ?? 'fold_' . uniqid();
                $type = $input['type_fold'] ?? 'compute';
                $micronaut->createFold($name, $type);
                $micronaut->orchestrate($name);
                return ['created' => $name, 'type' => $type, 'folds' => array_keys($micronaut->getFolds())];
            case 'execute_fold':
                $name = $input['name'] ?? '';
                if (empty($name)) {
                    return ['error' => 'Fold name required'];
                }
                $params = $input['params'] ?? [];
                return $micronaut->executeFold($name, $params);
            case 'agent':
                $name = $input['name'] ?? 'agent_' . uniqid();
                $type = $input['type_agent'] ?? 'worker';
                $agent = new MicronautAgent($name, $type);
                foreach ($input['tools'] ?? [] as $tool) {
                    $agent->addTool($tool);
                }
                $micronaut->addAgent($agent);
                return ['created' => $name, 'type' => $type, 'agents' => count($micronaut->getAgents())];
            case 'field':
                $name = $input['name'] ?? 'field_' . uniqid();
                $type = $input['type_field'] ?? 'working';
                $persistence = $input['persistence'] ?? 'volatile';
                $micronaut->createField($name, $type, $persistence);
                return ['created' => $name, 'type' => $type, 'persistence' => $persistence, 
                        'fields' => array_keys($micronaut->getFields())];
            default:
                return ['error' => "Unknown Micronaut operation: {$input['type']}"];
        }
    }
    
    return ['error' => 'Invalid Micronaut request'];
}

// ============================================================
// 7. ROUTE DEFINITIONS
// ============================================================

// Service info
addRoute('GET', '/', function() use ($kuhul, $micronaut) {
    return [
        'service' => 'Micronaut.php · K\'UHUL π · Micronaut µ',
        'version' => '3.0.0',
        'architecture' => [
            'orchestrator' => 'Micronaut',
            'enforcer' => 'K\'UHUL π',
            'boundary' => 'They are orthogonal. The boundary is permanent.'
        ],
        'endpoints' => [
            ['path' => '/', 'method' => 'GET', 'description' => 'Service info'],
            ['path' => '/health', 'method' => 'GET', 'description' => 'Health check'],
            ['path' => '/kuhul', 'method' => 'GET|POST', 'description' => 'K\'UHUL π enforcement'],
            ['path' => '/micronaut', 'method' => 'GET|POST', 'description' => 'Micronaut µ orchestration'],
            ['path' => '/api/rpc', 'method' => 'POST', 'description' => 'JSON-RPC endpoint'],
            ['path' => '/mcp', 'method' => 'POST', 'description' => 'MCP endpoint']
        ],
        'phases' => [
            'glyphs' => array_keys(KUHUL_GLYPHS),
            'current' => $kuhul->getCurrentPhase()->getGlyph(),
            'meaning' => KUHUL_GLYPHS
        ],
        'micronaut' => [
            'status' => $micronaut->getState()->getStatus(),
            'folds' => array_keys($micronaut->getFolds()),
            'agents' => count($micronaut->getAgents()),
            'fields' => array_keys($micronaut->getFields())
        ],
        'canonical' => [
            'Micronaut orchestrates contexts.',
            'KUHUL π enforces law.',
            'Extrapolator expands without altering outcomes.',
            'They are orthogonal.',
            'The boundary is permanent.',
            'No further refinement possible.'
        ]
    ];
});

// Health check
addRoute('GET', '/health', function() use ($kuhul, $micronaut) {
    return [
        'status' => 'ok',
        'timestamp' => date('c'),
        'kuhul' => [
            'phase' => $kuhul->getCurrentPhase()->getGlyph(),
            'enforcing' => $kuhul->isEnforcing(),
            'laws' => count($kuhul->getLaws())
        ],
        'micronaut' => [
            'status' => $micronaut->getState()->getStatus(),
            'coherence' => $micronaut->getState()->getCoherence(),
            'entropy' => $micronaut->getState()->getEntropy()
        ]
    ];
});

// K'UHUL info
addRoute('GET', '/api/v1/kuhul/info', function() use ($kuhul) {
    return $kuhul->toArray();
});

// Micronaut info
addRoute('GET', '/api/v1/micronaut/info', function() use ($micronaut) {
    return $micronaut->toArray();
});

// ============================================================
// 8. JSON-RPC SERVER
// ============================================================

class JsonRpcServer {
    private array $methods = [];
    private array $notifications = [];
    private KuhulRuntime $kuhul;
    private Micronaut $micronaut;
    
    public function __construct(KuhulRuntime $kuhul, Micronaut $micronaut) {
        $this->kuhul = $kuhul;
        $this->micronaut = $micronaut;
        $this->registerDefaultMethods();
    }
    
    public function registerMethod(string $method, callable $handler): void {
        $this->methods[$method] = $handler;
    }
    
    public function registerNotification(string $method, callable $handler): void {
        $this->notifications[$method] = $handler;
    }
    
    public function handle($request): array {
        if (is_string($request)) {
            $request = json_decode($request, true);
        }
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'Invalid JSON'];
        }
        
        if (isset($request[0]) && is_array($request[0])) {
            $responses = [];
            foreach ($request as $req) {
                $resp = $this->handleSingle($req);
                if ($resp !== null) {
                    $responses[] = $resp;
                }
            }
            return $responses;
        }
        
        return $this->handleSingle($request);
    }
    
    private function handleSingle(array $request): ?array {
        $error = $this->validateRequest($request);
        if ($error !== null) {
            return $error;
        }
        
        $method = $request['method'] ?? '';
        $params = $request['params'] ?? [];
        $id = $request['id'] ?? null;
        $isNotification = !array_key_exists('id', $request);
        
        if (!isset($this->methods[$method]) && !isset($this->notifications[$method])) {
            return [
                'jsonrpc' => '2.0',
                'error' => ['code' => -32601, 'message' => 'Method not found'],
                'id' => $id
            ];
        }
        
        try {
            $handler = $this->methods[$method] ?? $this->notifications[$method];
            $result = $handler($params);
            
            if (isset($this->methods[$method])) {
                return [
                    'jsonrpc' => '2.0',
                    'result' => $result,
                    'id' => $id
                ];
            }
            
            return null;
        } catch (Exception $e) {
            return [
                'jsonrpc' => '2.0',
                'error' => ['code' => -32000, 'message' => $e->getMessage()],
                'id' => $id
            ];
        }
    }
    
    private function validateRequest(array $request): ?array {
        if (!isset($request['jsonrpc']) || $request['jsonrpc'] !== '2.0') {
            return [
                'jsonrpc' => '2.0',
                'error' => ['code' => -32600, 'message' => 'Invalid Request'],
                'id' => null
            ];
        }
        
        if (!isset($request['method']) || !is_string($request['method']) || empty($request['method'])) {
            return [
                'jsonrpc' => '2.0',
                'error' => ['code' => -32600, 'message' => 'Invalid Request'],
                'id' => $request['id'] ?? null
            ];
        }
        
        return null;
    }
    
    private function registerDefaultMethods(): void {
        // System methods
        $this->registerMethod('system.info', function() {
            return [
                'server' => 'Micronaut.php · K\'UHUL π · Micronaut µ',
                'version' => '3.0.0',
                'kuhul' => [
                    'phase' => $this->kuhul->getCurrentPhase()->getGlyph(),
                    'laws' => array_keys($this->kuhul->getLaws())
                ],
                'micronaut' => [
                    'status' => $this->micronaut->getState()->getStatus(),
                    'folds' => array_keys($this->micronaut->getFolds()),
                    'agents' => count($this->micronaut->getAgents())
                ]
            ];
        });
        
        $this->registerMethod('system.echo', function($params) {
            return $params['message'] ?? $params[0] ?? 'No message';
        });
        
        $this->registerMethod('system.ping', function() {
            return ['pong' => date('c')];
        });
        
        // K'UHUL methods
        $this->registerMethod('kuhul.phase', function($params) {
            $phase = $params['phase'] ?? 'Pop';
            return [
                'phase' => $phase,
                'meaning' => KUHUL_GLYPHS[$phase],
                'current' => $this->kuhul->getCurrentPhase()->getGlyph(),
                'history' => array_map(fn($p) => $p->getGlyph(), $this->kuhul->getPhaseHistory())
            ];
        });
        
        $this->registerMethod('kuhul.state', function() {
            return $this->kuhul->getState();
        });
        
        $this->registerMethod('kuhul.enforce', function($params) {
            $lawName = $params['law'] ?? '';
            if (empty($lawName)) {
                throw new InvalidArgumentException('Law name required');
            }
            $state = $params['state'] ?? null;
            return $this->kuhul->enforceLaw($lawName, $state);
        });
        
        $this->registerMethod('kuhul.define', function($params) {
            $lawName = $params['name'] ?? '';
            if (empty($lawName)) {
                throw new InvalidArgumentException('Law name required');
            }
            $definition = $params['definition'] ?? [
                'phases' => ['Pop', 'Wo', 'Yax', 'Sek', "Ch'en", 'Xul'],
                'invariants' => [
                    'collapse_only' => true,
                    'field_perception' => true,
                    'compression_law' => true,
                    'unreachable_states' => true
                ]
            ];
            $this->kuhul->defineLaw($lawName, $definition);
            return [
                'defined' => $lawName,
                'hash' => substr($this->kuhul->getLaws()[$lawName]->getHash(), 0, 8)
            ];
        });
        
        // Micronaut methods
        $this->registerMethod('micronaut.status', function() {
            return $this->micronaut->getStatus();
        });
        
        $this->registerMethod('micronaut.fold.create', function($params) {
            $name = $params['name'] ?? '';
            if (empty($name)) {
                throw new InvalidArgumentException('Fold name required');
            }
            $type = $params['type'] ?? 'compute';
            $this->micronaut->createFold($name, $type);
            $this->micronaut->orchestrate($name);
            return ['created' => $name, 'type' => $type, 'folds' => array_keys($this->micronaut->getFolds())];
        });
        
        $this->registerMethod('micronaut.fold.execute', function($params) {
            $name = $params['name'] ?? '';
            if (empty($name)) {
                throw new InvalidArgumentException('Fold name required');
            }
            $input = $params['input'] ?? [];
            return $this->micronaut->executeFold($name, $input);
        });
        
        $this->registerMethod('micronaut.agent.create', function($params) {
            $name = $params['name'] ?? '';
            if (empty($name)) {
                throw new InvalidArgumentException('Agent name required');
            }
            $type = $params['type'] ?? 'worker';
            $agent = new MicronautAgent($name, $type);
            foreach ($params['tools'] ?? [] as $tool) {
                $agent->addTool($tool);
            }
            $this->micronaut->addAgent($agent);
            return ['created' => $name, 'type' => $type, 'agents' => count($this->micronaut->getAgents())];
        });
    }
}

// Initialize JSON-RPC
$jsonRpc = new JsonRpcServer($kuhul, $micronaut);

// ============================================================
// 9. MCP SERVER
// ============================================================

class McpServer {
    private array $tools = [];
    private array $resources = [];
    private array $prompts = [];
    private KuhulRuntime $kuhul;
    private Micronaut $micronaut;
    private array $serverInfo;
    private bool $initialized = false;
    
    public function __construct(KuhulRuntime $kuhul, Micronaut $micronaut) {
        $this->kuhul = $kuhul;
        $this->micronaut = $micronaut;
        $this->serverInfo = [
            'name' => 'Micronaut.php · K\'UHUL π · Micronaut µ',
            'version' => '3.0.0',
            'protocol_version' => '2025-03-26',
            'capabilities' => [
                'kuhul' => true,
                'micronaut' => true,
                'kxml' => true,
                'mcp' => true
            ]
        ];
        $this->registerDefaultTools();
        $this->registerDefaultResources();
        $this->registerDefaultPrompts();
    }
    
    public function registerTool(string $name, string $description, callable $handler, array $params = []): void {
        $this->tools[$name] = [
            'name' => $name,
            'description' => $description,
            'handler' => $handler,
            'params' => $params,
            'version' => '1.0.0'
        ];
    }
    
    public function registerResource(string $uri, string $name, callable $handler, string $mimeType = 'text/plain'): void {
        $this->resources[$uri] = [
            'uri' => $uri,
            'name' => $name,
            'handler' => $handler,
            'mimeType' => $mimeType
        ];
    }
    
    public function registerPrompt(string $name, string $description, callable $handler, array $params = []): void {
        $this->prompts[$name] = [
            'name' => $name,
            'description' => $description,
            'handler' => $handler,
            'params' => $params
        ];
    }
    
    public function handleHttpRequest(array $message): array {
        if (!isset($message['type'])) {
            return $this->errorResponse('Invalid MCP message');
        }
        
        return $this->processMessage($message);
    }
    
    private function processMessage(array $message): array {
        switch ($message['type']) {
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
            case 'kuhul/phase':
                return $this->handleKuhulPhase($message);
            case 'kuhul/law':
                return $this->handleKuhulLaw($message);
            case 'micronaut/fold':
                return $this->handleMicronautFold($message);
            case 'micronaut/agent':
                return $this->handleMicronautAgent($message);
            default:
                return $this->errorResponse("Unknown MCP message type: {$message['type']}");
        }
    }
    
    private function handleInitialize(array $message): array {
        $this->initialized = true;
        return [
            'type' => 'initialize_response',
            'server_info' => $this->serverInfo,
            'capabilities' => [
                'tools' => count($this->tools) > 0,
                'resources' => count($this->resources) > 0,
                'prompts' => count($this->prompts) > 0,
                'streaming' => false,
                'kuhul' => true,
                'micronaut' => true
            ]
        ];
    }
    
    private function handleToolsList(array $message): array {
        $tools = [];
        foreach ($this->tools as $name => $tool) {
            $tools[] = [
                'name' => $name,
                'description' => $tool['description'],
                'parameters' => $tool['params'],
                'version' => $tool['version']
            ];
        }
        return ['type' => 'tools/list_response', 'tools' => $tools];
    }
    
    private function handleToolsCall(array $message): array {
        $toolName = $message['name'] ?? '';
        $params = $message['parameters'] ?? [];
        
        if (!isset($this->tools[$toolName])) {
            return $this->errorResponse("Tool not found: {$toolName}");
        }
        
        try {
            $handler = $this->tools[$toolName]['handler'];
            $result = $handler($params);
            return ['type' => 'tools/call_response', 'result' => $result, 'isError' => false];
        } catch (Exception $e) {
            return ['type' => 'tools/call_response', 'result' => ['error' => $e->getMessage()], 'isError' => true];
        }
    }
    
    private function handleResourcesList(array $message): array {
        $resources = [];
        foreach ($this->resources as $uri => $resource) {
            $resources[] = [
                'uri' => $uri,
                'name' => $resource['name'],
                'mimeType' => $resource['mimeType']
            ];
        }
        return ['type' => 'resources/list_response', 'resources' => $resources];
    }
    
    private function handleResourcesRead(array $message): array {
        $uri = $message['uri'] ?? '';
        
        if (!isset($this->resources[$uri])) {
            return $this->errorResponse("Resource not found: {$uri}");
        }
        
        try {
            $handler = $this->resources[$uri]['handler'];
            $result = $handler();
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
    
    private function handlePromptsList(array $message): array {
        $prompts = [];
        foreach ($this->prompts as $name => $prompt) {
            $prompts[] = [
                'name' => $name,
                'description' => $prompt['description'],
                'parameters' => $prompt['params']
            ];
        }
        return ['type' => 'prompts/list_response', 'prompts' => $prompts];
    }
    
    private function handlePromptsGet(array $message): array {
        $name = $message['name'] ?? '';
        $params = $message['parameters'] ?? [];
        
        if (!isset($this->prompts[$name])) {
            return $this->errorResponse("Prompt not found: {$name}");
        }
        
        try {
            $handler = $this->prompts[$name]['handler'];
            $result = $handler($params);
            return ['type' => 'prompts/get_response', 'prompt' => $result];
        } catch (Exception $e) {
            return $this->errorResponse("Error getting prompt: " . $e->getMessage());
        }
    }
    
    private function handleKuhulPhase(array $message): array {
        $phase = $message['phase'] ?? 'Pop';
        try {
            return [
                'type' => 'kuhul/phase_response',
                'phase' => $phase,
                'meaning' => KUHUL_GLYPHS[$phase],
                'current' => $this->kuhul->getCurrentPhase()->getGlyph()
            ];
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    
    private function handleKuhulLaw(array $message): array {
        $lawName = $message['name'] ?? '';
        $definition = $message['definition'] ?? [
            'phases' => ['Pop', 'Wo', 'Yax', 'Sek', "Ch'en", 'Xul'],
            'invariants' => [
                'collapse_only' => true,
                'field_perception' => true,
                'compression_law' => true,
                'unreachable_states' => true
            ]
        ];
        
        try {
            $this->kuhul->defineLaw($lawName, $definition);
            return [
                'type' => 'kuhul/law_response',
                'name' => $lawName,
                'hash' => substr($this->kuhul->getLaws()[$lawName]->getHash(), 0, 8),
                'enforced' => true
            ];
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    
    private function handleMicronautFold(array $message): array {
        $foldName = $message['name'] ?? '';
        $type = $message['type'] ?? 'compute';
        
        try {
            $this->micronaut->createFold($foldName, $type);
            $this->micronaut->orchestrate($foldName);
            return [
                'type' => 'micronaut/fold_response',
                'name' => $foldName,
                'type' => $type,
                'created' => true,
                'folds' => array_keys($this->micronaut->getFolds())
            ];
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    
    private function handleMicronautAgent(array $message): array {
        $agentName = $message['name'] ?? '';
        $type = $message['type'] ?? 'worker';
        
        try {
            $agent = new MicronautAgent($agentName, $type);
            $this->micronaut->addAgent($agent);
            return [
                'type' => 'micronaut/agent_response',
                'name' => $agentName,
                'type' => $type,
                'created' => true,
                'agents' => count($this->micronaut->getAgents())
            ];
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
    
    private function errorResponse(string $message, int $code = -1): array {
        return [
            'type' => 'error',
            'error' => ['code' => $code, 'message' => $message]
        ];
    }
    
    private function registerDefaultTools(): void {
        // K'UHUL Tools
        $this->registerTool('kuhul_perceive', 'K\'UHUL Pop: Perceive input', function($params) {
            $input = $params['input'] ?? [];
            return $this->kuhul->perceive($input);
        }, ['input' => ['type' => 'object', 'description' => 'Input to perceive']]);
        
        $this->registerTool('kuhul_represent', 'K\'UHUL Wo: Represent symbol', function($params) {
            $symbol = $params['symbol'] ?? '';
            $value = $params['value'] ?? null;
            return $this->kuhul->represent($symbol, $value);
        }, [
            'symbol' => ['type' => 'string', 'description' => 'Symbol name'],
            'value' => ['type' => 'any', 'description' => 'Value to bind']
        ]);
        
        $this->registerTool('kuhul_plan', 'K\'UHUL Yax: Plan intention', function($params) {
            $intention = $params['intention'] ?? '';
            return $this->kuhul->plan($intention);
        }, ['intention' => ['type' => 'string', 'description' => 'Intention to plan']]);
        
        $this->registerTool('kuhul_execute', 'K\'UHUL Sek: Execute action', function($params) {
            $action = $params['action'] ?? '';
            return $this->kuhul->execute(function($state) use ($action) {
                return "Executed: {$action}";
            });
        }, ['action' => ['type' => 'string', 'description' => 'Action to execute']]);
        
        $this->registerTool('kuhul_project', 'K\'UHUL Ch\'en: Project output', function($params) {
            $target = $params['target'] ?? 'result';
            return $this->kuhul->project($target);
        }, ['target' => ['type' => 'string', 'description' => 'Target to project']]);
        
        $this->registerTool('kuhul_consolidate', 'K\'UHUL Xul: Consolidate state', function($params) {
            return $this->kuhul->consolidate();
        }, []);
        
        $this->registerTool('kuhul_reflect', 'K\'UHUL Noj: Reflect on state', function($params) {
            return $this->kuhul->reflect();
        }, []);
        
        // Micronaut Tools
        $this->registerTool('micronaut_status', 'Get Micronaut status', function($params) {
            return $this->micronaut->getStatus();
        }, []);
        
        $this->registerTool('micronaut_execute_fold', 'Execute a Micronaut fold', function($params) {
            $foldName = $params['fold'] ?? '';
            $input = $params['input'] ?? [];
            return $this->micronaut->executeFold($foldName, $input);
        }, [
            'fold' => ['type' => 'string', 'description' => 'Fold name'],
            'input' => ['type' => 'object', 'description' => 'Input data']
        ]);
        
        // Basic tools
        $this->registerTool('echo', 'Echo back a message', function($params) {
            return ['echo' => $params['message'] ?? 'No message provided'];
        }, ['message' => ['type' => 'string', 'description' => 'Message to echo']]);
    }
    
    private function registerDefaultResources(): void {
        $this->registerResource('info://server', 'Server Info', function() {
            return json_encode([
                'name' => 'Micronaut.php · K\'UHUL π · Micronaut µ',
                'version' => '3.0.0',
                'kuhul' => [
                    'phase' => $this->kuhul->getCurrentPhase()->getGlyph(),
                    'laws' => array_keys($this->kuhul->getLaws()),
                    'history' => count($this->kuhul->getPhaseHistory())
                ],
                'micronaut' => [
                    'name' => $this->micronaut->getName(),
                    'status' => $this->micronaut->getState()->getStatus(),
                    'folds' => array_keys($this->micronaut->getFolds()),
                    'agents' => count($this->micronaut->getAgents())
                ],
                'timestamp' => date('c')
            ], JSON_PRETTY_PRINT);
        }, 'application/json');
        
        $this->registerResource('info://kuhul', 'K\'UHUL State', function() {
            return json_encode($this->kuhul->toArray(), JSON_PRETTY_PRINT);
        }, 'application/json');
        
        $this->registerResource('info://micronaut', 'Micronaut State', function() {
            return json_encode($this->micronaut->toArray(), JSON_PRETTY_PRINT);
        }, 'application/json');
    }
    
    private function registerDefaultPrompts(): void {
        $this->registerPrompt('kuhul_phase', 'K\'UHUL phase guidance', function($params) {
            $phase = $params['phase'] ?? 'Pop';
            return [
                'phase' => $phase,
                'meaning' => KUHUL_GLYPHS[$phase],
                'current' => $this->kuhul->getCurrentPhase()->getGlyph(),
                'history' => array_map(fn($p) => $p->getGlyph(), $this->kuhul->getPhaseHistory()),
                'guidance' => [
                    'Pop' => 'Begin by perceiving the input',
                    'Wo' => 'Represent the data structure',
                    'Yax' => 'Plan the intention',
                    'Sek' => 'Execute the action',
                    "Ch'en" => 'Project the output',
                    'Xul' => 'Consolidate the result',
                    'Noj' => 'Reflect on the process'
                ][$phase] ?? ''
            ];
        }, ['phase' => ['type' => 'string', 'description' => 'Phase to guide']]);
        
        $this->registerPrompt('micronaut_orchestrate', 'Micronaut orchestration guidance', function($params) {
            $fold = $params['fold'] ?? 'default';
            return [
                'orchestration' => 'Micronaut orchestrates contexts.',
                'fold' => $fold,
                'available' => array_keys($this->micronaut->getFolds()),
                'status' => $this->micronaut->getState()->getStatus(),
                'policy' => [
                    'priority' => $this->micronaut->getStatus()['policy']['priority'] ?? 'balanced'
                ],
                'guidance' => 'SELECT, ARRANGE, CHOOSE, MANAGE — Micronaut selects fields, arranges collapse timing, chooses field selections, and manages host reality.'
            ];
        }, ['fold' => ['type' => 'string', 'description' => 'Fold to orchestrate']]);
    }
}

// Initialize MCP
$mcp = new McpServer($kuhul, $micronaut);

// Add MCP route
addRoute('POST', '/mcp', function($input) use ($mcp) {
    return $mcp->handleHttpRequest($input);
});

// Add JSON-RPC route
addRoute('POST', '/api/rpc', function($input) use ($jsonRpc) {
    return $jsonRpc->handle($input);
});

// ============================================================
// 10. SERVER EXECUTION
// ============================================================

// If running from command line, start the server
if (php_sapi_name() === 'cli') {
    $logger->info("⟁ Starting Micronaut.php server on http://localhost:8080 ⟁");
    $logger->info("π K'UHUL endpoint: http://localhost:8080/kuhul");
    $logger->info("µ Micronaut endpoint: http://localhost:8080/micronaut");
    $logger->info("⚡ MCP endpoint: http://localhost:8080/mcp");
    $logger->info("🐘 JSON-RPC endpoint: http://localhost:8080/api/rpc");
    $logger->info("◆ Press Ctrl+C to stop the server");
    
    // Simple built-in server
    $host = '0.0.0.0';
    $port = 8080;
    $socket = stream_socket_server("tcp://{$host}:{$port}", $errno, $errstr);
    
    if (!$socket) {
        $logger->error("Failed to create socket: {$errstr} ({$errno})");
        exit(1);
    }
    
    $logger->info("⟁ Server listening on http://localhost:{$port} ⟁");
    
    while (true) {
        $client = stream_socket_accept($socket, -1);
        if ($client) {
            $request = fread($client, 8192);
            
            // Parse request
            $lines = explode("\n", $request);
            $firstLine = $lines[0] ?? '';
            $parts = explode(' ', $firstLine);
            $method = $parts[0] ?? 'GET';
            $uri = $parts[1] ?? '/';
            
            // Parse body
            $body = '';
            $inBody = false;
            foreach ($lines as $line) {
                if ($line === "\r" || $line === "") {
                    $inBody = true;
                    continue;
                }
                if ($inBody) {
                    $body .= $line;
                }
            }
            
            // Parse input
            $input = [];
            if (!empty($body)) {
                try {
                    $input = json_decode($body, true) ?? [];
                } catch (Exception $e) {
                    $input = ['raw' => $body];
                }
            }
            
            // Dispatch
            try {
                $response = dispatch($method, $uri, $input);
                
                // Send response
                $json = json_encode($response, JSON_PRETTY_PRINT);
                $headers = "HTTP/1.1 200 OK\r\n";
                $headers .= "Content-Type: application/json\r\n";
                $headers .= "Access-Control-Allow-Origin: *\r\n";
                $headers .= "Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS\r\n";
                $headers .= "Access-Control-Allow-Headers: Content-Type, Authorization\r\n";
                $headers .= "Content-Length: " . strlen($json) . "\r\n";
                $headers .= "Connection: close\r\n\r\n";
                
                fwrite($client, $headers . $json);
            } catch (Exception $e) {
                $error = ['error' => $e->getMessage()];
                $json = json_encode($error);
                $headers = "HTTP/1.1 500 Internal Server Error\r\n";
                $headers .= "Content-Type: application/json\r\n";
                $headers .= "Content-Length: " . strlen($json) . "\r\n";
                $headers .= "Connection: close\r\n\r\n";
                fwrite($client, $headers . $json);
                $logger->error("Server error: " . $e->getMessage());
            }
            
            fclose($client);
        }
    }
    
    fclose($socket);
} else {
    // Running via web server - just export the functions
    $logger->info("Micronaut.php loaded successfully");
}

// ============================================================
// 11. CANONICAL STATEMENTS
// ============================================================

/*
 * Micronaut orchestrates contexts.
 * KUHUL π enforces law.
 * Extrapolator expands without altering outcomes.
 * They are orthogonal.
 * The boundary is permanent.
 * No further refinement possible.
 */

echo "⟁ K'UHUL π · Micronaut µ · PHP Runtime v3.0 ⟁\n";
echo "◆ Micronaut orchestrates. K'UHUL enforces. They are orthogonal.\n";
echo "◆ The boundary is permanent. No further refinement possible.\n";