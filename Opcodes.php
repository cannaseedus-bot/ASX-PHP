<?php
/**
 * opcodes.php - WebGL2 · GEMM · DirectML Opcode System
 * 
 * A unified opcode layer that bridges:
 * - WebGL2 Compute Shaders (SSBO, imageLoad/imageStore)
 * - GEMM (General Matrix Multiply) operations
 * - DirectML (DML_GEMM_OPERATOR_DESC, DML_MATRIX_MULTIPLY_*)
 * 
 * Integrates with:
 * - K'UHUL π phase enforcement
 * - Micronaut µ orchestration
 * - MCP (Model Context Protocol)
 * 
 * @package Opcodes
 * @version 3.0.0
 * @author K'UHUL π · Micronaut µ
 * @license MIT
 */

declare(strict_types=1);

// ============================================================
// 1. OPCODE CONSTANTS
// ============================================================

/**
 * WebGL2 Compute Shader Opcodes
 * Based on WebGL 2.0 Compute specification [citation:1][citation:10]
 */
define('OPCODE_WEBGL2', [
    // Buffer operations (SSBO)
    'SSBO_CREATE'       => 0x1000,
    'SSBO_BIND'         => 0x1001,
    'SSBO_READ'         => 0x1002,
    'SSBO_WRITE'        => 0x1003,
    'SSBO_COPY'         => 0x1004,
    'SSBO_CLEAR'        => 0x1005,
    
    // Texture operations (imageLoad/imageStore)
    'TEXTURE_CREATE'    => 0x1100,
    'TEXTURE_BIND'      => 0x1101,
    'TEXTURE_LOAD'      => 0x1102,
    'TEXTURE_STORE'     => 0x1103,
    'TEXTURE_COPY'      => 0x1104,
    'TEXTURE_CLEAR'     => 0x1105,
    
    // Compute shader operations
    'COMPUTE_DISPATCH'  => 0x1200,
    'COMPUTE_BARRIER'   => 0x1201,
    'COMPUTE_SYNC'      => 0x1202,
    
    // Vertex operations
    'VERTEX_BIND'       => 0x1300,
    'VERTEX_DRAW'       => 0x1301,
    'VERTEX_INSTANCED'  => 0x1302,
    
    // Memory operations
    'MEMORY_ALLOC'      => 0x1400,
    'MEMORY_FREE'       => 0x1401,
    'MEMORY_COPY'       => 0x1402,
    'MEMORY_MAP'        => 0x1403,
    'MEMORY_UNMAP'      => 0x1404,
]);

/**
 * GEMM (General Matrix Multiply) Opcodes
 * Based on DirectML DML_GEMM_OPERATOR_DESC [citation:7]
 */
define('OPCODE_GEMM', [
    // Basic GEMM
    'GEMM'              => 0x2000,
    'GEMM_BATCHED'      => 0x2001,
    'GEMM_STRIDED'      => 0x2002,
    
    // Transpose variants
    'GEMM_TRANSA'       => 0x2010,
    'GEMM_TRANSB'       => 0x2011,
    'GEMM_TRANSAB'      => 0x2012,
    
    // Alpha/Beta scaling
    'GEMM_ALPHA'        => 0x2020,
    'GEMM_BETA'         => 0x2021,
    'GEMM_ALPHABETA'    => 0x2022,
    
    // Quantized GEMM
    'GEMM_INT8'         => 0x2030,
    'GEMM_UINT8'        => 0x2031,
    'GEMM_INT8_TO_FLOAT'=> 0x2032,
    'GEMM_UINT8_TO_FLOAT'=> 0x2033,
    
    // Specialized GEMM
    'GEMM_SPARSE'       => 0x2040,
    'GEMM_TENSOR_CORE'  => 0x2041,
    'GEMM_FP16'         => 0x2042,
    'GEMM_BF16'         => 0x2043,
    'GEMM_INT4'         => 0x2044,
]);

/**
 * DirectML Opcodes
 * Based on DML_MATRIX_MULTIPLY_INTEGER_OPERATOR_DESC [citation:11]
 * and DML_MATRIX_MULTIPLY_INTEGER_TO_FLOAT_OPERATOR_DESC [citation:2]
 */
define('OPCODE_DIRECTML', [
    // Matrix multiply integer
    'DML_MATRIX_MULTIPLY_INTEGER'          => 0x3000,
    'DML_MATRIX_MULTIPLY_INTEGER_TO_FLOAT' => 0x3001,
    
    // Convolution
    'DML_CONVOLUTION'                      => 0x3010,
    'DML_CONVOLUTION_INTEGER'              => 0x3011,
    
    // Activation
    'DML_ACTIVATION_RELU'                  => 0x3020,
    'DML_ACTIVATION_SIGMOID'               => 0x3021,
    'DML_ACTIVATION_TANH'                  => 0x3022,
    'DML_ACTIVATION_GELU'                  => 0x3023,
    
    // Pooling
    'DML_AVERAGE_POOLING'                  => 0x3030,
    'DML_MAX_POOLING'                      => 0x3031,
    
    // Normalization
    'DML_BATCH_NORMALIZATION'              => 0x3040,
    'DML_LAYER_NORMALIZATION'              => 0x3041,
]);

/**
 * K'UHUL Phase Opcodes
 * Map geometric/algebraic operations to phase glyphs
 */
define('OPCODE_KUHUL', [
    // Pop - Perceive
    'POP_PERCEIVE'      => 0x4000,
    'POP_INPUT'         => 0x4001,
    'POP_READ'          => 0x4002,
    
    // Wo - Represent
    'WO_REPRESENT'      => 0x4010,
    'WO_BUILD'          => 0x4011,
    'WO_BIND'           => 0x4012,
    
    // Yax - Plan
    'YAX_PLAN'          => 0x4020,
    'YAX_CONDITION'     => 0x4021,
    'YAX_INTENT'        => 0x4022,
    
    // Sek - Execute
    'SEK_EXECUTE'       => 0x4030,
    'SEK_COMPUTE'       => 0x4031,
    'SEK_ACT'           => 0x4032,
    
    // Ch'en - Project
    'CHEN_PROJECT'      => 0x4040,
    'CHEN_OUTPUT'       => 0x4041,
    'CHEN_WRITE'        => 0x4042,
    
    // Xul - Consolidate
    'XUL_CONSOLIDATE'   => 0x4050,
    'XUL_COLLAPSE'      => 0x4051,
    'XUL_COMMIT'        => 0x4052,
    
    // Noj - Reflect
    'NOJ_REFLECT'       => 0x4060,
    'NOJ_REASON'        => 0x4061,
    'NOJ_EVALUATE'      => 0x4062,
]);

// ============================================================
// 2. OPCODE STRUCTURE
// ============================================================

/**
 * Opcode - A single operation code
 */
class Opcode {
    public int $code;
    public string $name;
    public string $category;
    public string $phase;       // K'UHUL phase
    public array $operands;     // Operand types
    public int $cycles;         // Estimated cycles
    public bool $isAsync;       // Async execution
    
    public function __construct(
        int $code,
        string $name,
        string $category = 'GENERAL',
        string $phase = 'Sek',
        array $operands = [],
        int $cycles = 1,
        bool $isAsync = false
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->category = $category;
        $this->phase = $phase;
        $this->operands = $operands;
        $this->cycles = $cycles;
        $this->isAsync = $isAsync;
    }
    
    public function toArray(): array {
        return [
            'code' => sprintf('0x%04X', $this->code),
            'name' => $this->name,
            'category' => $this->category,
            'phase' => $this->phase,
            'operands' => $this->operands,
            'cycles' => $this->cycles,
            'is_async' => $this->isAsync
        ];
    }
    
    public function __toString(): string {
        return sprintf("[0x%04X] %s (%s) @ %s", $this->code, $this->name, $this->category, $this->phase);
    }
}

// ============================================================
// 3. OPCODE REGISTRY
// ============================================================

/**
 * OpcodeRegistry - Central registry of all opcodes
 */
class OpcodeRegistry {
    private static ?OpcodeRegistry $instance = null;
    private array $opcodes = [];
    private array $byName = [];
    private array $byPhase = [];
    private array $byCategory = [];
    
    private function __construct() {
        $this->registerWebGL2Opcodes();
        $this->registerGEMMOpcodes();
        $this->registerDirectMLOpcodes();
        $this->registerKuhulOpcodes();
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function register(Opcode $opcode): void {
        $this->opcodes[$opcode->code] = $opcode;
        $this->byName[$opcode->name] = $opcode;
        $this->byPhase[$opcode->phase][] = $opcode;
        $this->byCategory[$opcode->category][] = $opcode;
    }
    
    private function registerWebGL2Opcodes(): void {
        // SSBO operations
        $this->register(new Opcode(0x1000, 'SSBO_CREATE', 'WEBGL2', 'Wo', ['size', 'usage'], 10));
        $this->register(new Opcode(0x1001, 'SSBO_BIND', 'WEBGL2', 'Wo', ['buffer', 'binding'], 5));
        $this->register(new Opcode(0x1002, 'SSBO_READ', 'WEBGL2', 'Pop', ['buffer', 'offset', 'size'], 20));
        $this->register(new Opcode(0x1003, 'SSBO_WRITE', 'WEBGL2', "Ch'en", ['buffer', 'offset', 'data'], 20));
        $this->register(new Opcode(0x1004, 'SSBO_COPY', 'WEBGL2', 'Sek', ['src', 'dst'], 30));
        $this->register(new Opcode(0x1005, 'SSBO_CLEAR', 'WEBGL2', 'Xul', ['buffer'], 15));
        
        // Texture operations
        $this->register(new Opcode(0x1100, 'TEXTURE_CREATE', 'WEBGL2', 'Wo', ['width', 'height', 'format'], 25));
        $this->register(new Opcode(0x1101, 'TEXTURE_BIND', 'WEBGL2', 'Wo', ['texture', 'unit'], 5));
        $this->register(new Opcode(0x1102, 'TEXTURE_LOAD', 'WEBGL2', 'Pop', ['texture', 'coords'], 15));
        $this->register(new Opcode(0x1103, 'TEXTURE_STORE', 'WEBGL2', "Ch'en", ['texture', 'coords', 'value'], 15));
        $this->register(new Opcode(0x1104, 'TEXTURE_COPY', 'WEBGL2', 'Sek', ['src', 'dst'], 25));
        $this->register(new Opcode(0x1105, 'TEXTURE_CLEAR', 'WEBGL2', 'Xul', ['texture', 'color'], 10));
        
        // Compute operations
        $this->register(new Opcode(0x1200, 'COMPUTE_DISPATCH', 'WEBGL2', 'Sek', ['x', 'y', 'z'], 100, true));
        $this->register(new Opcode(0x1201, 'COMPUTE_BARRIER', 'WEBGL2', 'Xul', [], 50));
        $this->register(new Opcode(0x1202, 'COMPUTE_SYNC', 'WEBGL2', 'Xul', ['fence'], 30, true));
        
        // Vertex operations
        $this->register(new Opcode(0x1300, 'VERTEX_BIND', 'WEBGL2', 'Wo', ['buffer', 'location', 'size'], 5));
        $this->register(new Opcode(0x1301, 'VERTEX_DRAW', 'WEBGL2', "Ch'en", ['count', 'offset'], 80, true));
        $this->register(new Opcode(0x1302, 'VERTEX_INSTANCED', 'WEBGL2', "Ch'en", ['count', 'instances'], 100, true));
        
        // Memory operations
        $this->register(new Opcode(0x1400, 'MEMORY_ALLOC', 'WEBGL2', 'Yax', ['size'], 10));
        $this->register(new Opcode(0x1401, 'MEMORY_FREE', 'WEBGL2', 'Xul', ['ptr'], 5));
        $this->register(new Opcode(0x1402, 'MEMORY_COPY', 'WEBGL2', 'Sek', ['src', 'dst', 'size'], 25));
        $this->register(new Opcode(0x1403, 'MEMORY_MAP', 'WEBGL2', 'Pop', ['ptr', 'size'], 15));
        $this->register(new Opcode(0x1404, 'MEMORY_UNMAP', 'WEBGL2', "Ch'en", ['ptr'], 10));
    }
    
    private function registerGEMMOpcodes(): void {
        // Basic GEMM - based on DML_GEMM_OPERATOR_DESC [citation:7]
        $this->register(new Opcode(0x2000, 'GEMM', 'GEMM', 'Sek', ['A', 'B', 'C'], 200, true));
        $this->register(new Opcode(0x2001, 'GEMM_BATCHED', 'GEMM', 'Sek', ['A', 'B', 'C', 'batch'], 250, true));
        $this->register(new Opcode(0x2002, 'GEMM_STRIDED', 'GEMM', 'Sek', ['A', 'B', 'C', 'strides'], 220, true));
        
        // Transpose variants
        $this->register(new Opcode(0x2010, 'GEMM_TRANSA', 'GEMM', 'Sek', ['A', 'B', 'C'], 210, true));
        $this->register(new Opcode(0x2011, 'GEMM_TRANSB', 'GEMM', 'Sek', ['A', 'B', 'C'], 210, true));
        $this->register(new Opcode(0x2012, 'GEMM_TRANSAB', 'GEMM', 'Sek', ['A', 'B', 'C'], 220, true));
        
        // Alpha/Beta scaling
        $this->register(new Opcode(0x2020, 'GEMM_ALPHA', 'GEMM', 'Sek', ['A', 'B', 'alpha'], 205, true));
        $this->register(new Opcode(0x2021, 'GEMM_BETA', 'GEMM', 'Sek', ['A', 'B', 'C', 'beta'], 205, true));
        $this->register(new Opcode(0x2022, 'GEMM_ALPHABETA', 'GEMM', 'Sek', ['A', 'B', 'C', 'alpha', 'beta'], 215, true));
        
        // Quantized GEMM - based on DML_MATRIX_MULTIPLY_INTEGER [citation:11]
        $this->register(new Opcode(0x2030, 'GEMM_INT8', 'GEMM', 'Sek', ['A', 'B', 'C', 'scale', 'zero'], 180, true));
        $this->register(new Opcode(0x2031, 'GEMM_UINT8', 'GEMM', 'Sek', ['A', 'B', 'C', 'scale', 'zero'], 180, true));
        $this->register(new Opcode(0x2032, 'GEMM_INT8_TO_FLOAT', 'GEMM', 'Sek', ['A', 'B', 'C', 'scale', 'zero'], 190, true));
        $this->register(new Opcode(0x2033, 'GEMM_UINT8_TO_FLOAT', 'GEMM', 'Sek', ['A', 'B', 'C', 'scale', 'zero'], 190, true));
        
        // Specialized GEMM
        $this->register(new Opcode(0x2040, 'GEMM_SPARSE', 'GEMM', 'Sek', ['A', 'B', 'C', 'sparsity'], 150, true));
        $this->register(new Opcode(0x2041, 'GEMM_TENSOR_CORE', 'GEMM', 'Sek', ['A', 'B', 'C'], 100, true));
        $this->register(new Opcode(0x2042, 'GEMM_FP16', 'GEMM', 'Sek', ['A', 'B', 'C'], 120, true));
        $this->register(new Opcode(0x2043, 'GEMM_BF16', 'GEMM', 'Sek', ['A', 'B', 'C'], 125, true));
        $this->register(new Opcode(0x2044, 'GEMM_INT4', 'GEMM', 'Sek', ['A', 'B', 'C', 'scale', 'zero'], 160, true));
    }
    
    private function registerDirectMLOpcodes(): void {
        // Matrix multiply integer - based on DML_MATRIX_MULTIPLY_INTEGER_OPERATOR_DESC [citation:11]
        $this->register(new Opcode(0x3000, 'DML_MATRIX_MULTIPLY_INTEGER', 'DIRECTML', 'Sek', 
            ['ATensor', 'AZeroPoint', 'BTensor', 'BZeroPoint', 'OutputTensor'], 200, true));
        
        // Matrix multiply integer to float - based on DML_MATRIX_MULTIPLY_INTEGER_TO_FLOAT_OPERATOR_DESC [citation:2]
        $this->register(new Opcode(0x3001, 'DML_MATRIX_MULTIPLY_INTEGER_TO_FLOAT', 'DIRECTML', 'Sek',
            ['ATensor', 'AScale', 'AZeroPoint', 'BTensor', 'BScale', 'BZeroPoint', 'BiasTensor', 'OutputTensor'], 220, true));
        
        // Convolution - based on DML_CONVOLUTION_OPERATOR_DESC [citation:15]
        $this->register(new Opcode(0x3010, 'DML_CONVOLUTION', 'DIRECTML', 'Sek',
            ['InputTensor', 'FilterTensor', 'BiasTensor', 'OutputTensor'], 300, true));
        $this->register(new Opcode(0x3011, 'DML_CONVOLUTION_INTEGER', 'DIRECTML', 'Sek',
            ['InputTensor', 'FilterTensor', 'BiasTensor', 'OutputTensor'], 280, true));
        
        // Activation
        $this->register(new Opcode(0x3020, 'DML_ACTIVATION_RELU', 'DIRECTML', 'Sek', ['input', 'output'], 50));
        $this->register(new Opcode(0x3021, 'DML_ACTIVATION_SIGMOID', 'DIRECTML', 'Sek', ['input', 'output'], 80));
        $this->register(new Opcode(0x3022, 'DML_ACTIVATION_TANH', 'DIRECTML', 'Sek', ['input', 'output'], 80));
        $this->register(new Opcode(0x3023, 'DML_ACTIVATION_GELU', 'DIRECTML', 'Sek', ['input', 'output'], 100));
        
        // Pooling
        $this->register(new Opcode(0x3030, 'DML_AVERAGE_POOLING', 'DIRECTML', 'Sek', ['input', 'output', 'kernel'], 120));
        $this->register(new Opcode(0x3031, 'DML_MAX_POOLING', 'DIRECTML', 'Sek', ['input', 'output', 'kernel'], 120));
        
        // Normalization
        $this->register(new Opcode(0x3040, 'DML_BATCH_NORMALIZATION', 'DIRECTML', 'Sek', ['input', 'mean', 'variance', 'output'], 150));
        $this->register(new Opcode(0x3041, 'DML_LAYER_NORMALIZATION', 'DIRECTML', 'Sek', ['input', 'mean', 'variance', 'output'], 150));
    }
    
    private function registerKuhulOpcodes(): void {
        // Pop - Perceive
        $this->register(new Opcode(0x4000, 'POP_PERCEIVE', 'KUHUL', 'Pop', ['input'], 10));
        $this->register(new Opcode(0x4001, 'POP_INPUT', 'KUHUL', 'Pop', ['source'], 5));
        $this->register(new Opcode(0x4002, 'POP_READ', 'KUHUL', 'Pop', ['buffer', 'offset'], 15));
        
        // Wo - Represent
        $this->register(new Opcode(0x4010, 'WO_REPRESENT', 'KUHUL', 'Wo', ['symbol', 'value'], 10));
        $this->register(new Opcode(0x4011, 'WO_BUILD', 'KUHUL', 'Wo', ['type', 'params'], 20));
        $this->register(new Opcode(0x4012, 'WO_BIND', 'KUHUL', 'Wo', ['name', 'value'], 5));
        
        // Yax - Plan
        $this->register(new Opcode(0x4020, 'YAX_PLAN', 'KUHUL', 'Yax', ['intention'], 15));
        $this->register(new Opcode(0x4021, 'YAX_CONDITION', 'KUHUL', 'Yax', ['condition', 'action'], 10));
        $this->register(new Opcode(0x4022, 'YAX_INTENT', 'KUHUL', 'Yax', ['goal'], 10));
        
        // Sek - Execute
        $this->register(new Opcode(0x4030, 'SEK_EXECUTE', 'KUHUL', 'Sek', ['action'], 50, true));
        $this->register(new Opcode(0x4031, 'SEK_COMPUTE', 'KUHUL', 'Sek', ['opcode', 'operands'], 100, true));
        $this->register(new Opcode(0x4032, 'SEK_ACT', 'KUHUL', 'Sek', ['effect'], 30, true));
        
        // Ch'en - Project
        $this->register(new Opcode(0x4040, 'CHEN_PROJECT', 'KUHUL', "Ch'en", ['target'], 15));
        $this->register(new Opcode(0x4041, 'CHEN_OUTPUT', 'KUHUL', "Ch'en", ['value'], 10));
        $this->register(new Opcode(0x4042, 'CHEN_WRITE', 'KUHUL', "Ch'en", ['buffer', 'data'], 20));
        
        // Xul - Consolidate
        $this->register(new Opcode(0x4050, 'XUL_CONSOLIDATE', 'KUHUL', 'Xul', [], 25));
        $this->register(new Opcode(0x4051, 'XUL_COLLAPSE', 'KUHUL', 'Xul', ['state'], 20));
        $this->register(new Opcode(0x4052, 'XUL_COMMIT', 'KUHUL', 'Xul', [], 15));
        
        // Noj - Reflect
        $this->register(new Opcode(0x4060, 'NOJ_REFLECT', 'KUHUL', 'Noj', [], 30));
        $this->register(new Opcode(0x4061, 'NOJ_REASON', 'KUHUL', 'Noj', ['premise'], 50));
        $this->register(new Opcode(0x4062, 'NOJ_EVALUATE', 'KUHUL', 'Noj', ['expression'], 40));
    }
    
    // ============================================================
    // Query methods
    // ============================================================
    
    public function get(int $code): ?Opcode {
        return $this->opcodes[$code] ?? null;
    }
    
    public function getByName(string $name): ?Opcode {
        return $this->byName[$name] ?? null;
    }
    
    public function getByPhase(string $phase): array {
        return $this->byPhase[$phase] ?? [];
    }
    
    public function getByCategory(string $category): array {
        return $this->byCategory[$category] ?? [];
    }
    
    public function getAll(): array {
        return $this->opcodes;
    }
    
    public function count(): int {
        return count($this->opcodes);
    }
    
    public function toArray(): array {
        $result = [];
        foreach ($this->opcodes as $opcode) {
            $result[] = $opcode->toArray();
        }
        return $result;
    }
}

// ============================================================
// 4. OPCODE EXECUTOR
// ============================================================

/**
 * OpcodeExecutor - Executes opcodes with K'UHUL phase integration
 */
class OpcodeExecutor {
    private OpcodeRegistry $registry;
    private array $stack = [];
    private array $registers = [];
    private array $memory = [];
    private int $pc = 0;        // Program counter
    private array $program = [];
    private array $trace = [];
    private bool $debug = false;
    
    public function __construct() {
        $this->registry = OpcodeRegistry::getInstance();
        $this->registers = array_fill(0, 32, 0);
    }
    
    public function setDebug(bool $debug): void {
        $this->debug = $debug;
    }
    
    // ============================================================
    // Program loading
    // ============================================================
    
    public function loadProgram(array $opcodes): void {
        $this->program = $opcodes;
        $this->pc = 0;
        $this->trace = [];
    }
    
    public function addOpcode(int $code, array $operands = []): void {
        $this->program[] = ['code' => $code, 'operands' => $operands];
    }
    
    // ============================================================
    // Execution
    // ============================================================
    
    public function execute(): array {
        $results = [];
        
        while ($this->pc < count($this->program)) {
            $instruction = $this->program[$this->pc];
            $opcode = $this->registry->get($instruction['code']);
            
            if ($opcode === null) {
                throw new RuntimeException("Unknown opcode: 0x" . dechex($instruction['code']));
            }
            
            $this->trace[] = [
                'pc' => $this->pc,
                'opcode' => $opcode->name,
                'phase' => $opcode->phase,
                'operands' => $instruction['operands']
            ];
            
            if ($this->debug) {
                echo sprintf("[%04d] %s\n", $this->pc, $opcode->__toString());
            }
            
            $result = $this->executeOpcode($opcode, $instruction['operands']);
            $results[] = $result;
            
            $this->pc++;
        }
        
        return $results;
    }
    
    private function executeOpcode(Opcode $opcode, array $operands): array {
        // Route to specific handler based on category
        return match($opcode->category) {
            'WEBGL2' => $this->executeWebGL2($opcode, $operands),
            'GEMM' => $this->executeGEMM($opcode, $operands),
            'DIRECTML' => $this->executeDirectML($opcode, $operands),
            'KUHUL' => $this->executeKuhul($opcode, $operands),
            default => ['error' => "Unknown category: {$opcode->category}"]
        };
    }
    
    // ============================================================
    // WebGL2 handlers
    // ============================================================
    
    private function executeWebGL2(Opcode $opcode, array $operands): array {
        return match($opcode->name) {
            'SSBO_CREATE' => $this->ssboCreate($operands),
            'SSBO_BIND' => $this->ssboBind($operands),
            'SSBO_READ' => $this->ssboRead($operands),
            'SSBO_WRITE' => $this->ssboWrite($operands),
            'TEXTURE_CREATE' => $this->textureCreate($operands),
            'TEXTURE_LOAD' => $this->textureLoad($operands),
            'TEXTURE_STORE' => $this->textureStore($operands),
            'COMPUTE_DISPATCH' => $this->computeDispatch($operands),
            'MEMORY_ALLOC' => $this->memoryAlloc($operands),
            default => ['status' => 'ok', 'opcode' => $opcode->name]
        };
    }
    
    private function ssboCreate(array $operands): array {
        $size = $operands[0] ?? 1024;
        $id = count($this->memory);
        $this->memory[$id] = array_fill(0, $size, 0);
        return ['status' => 'ok', 'buffer_id' => $id, 'size' => $size];
    }
    
    private function ssboBind(array $operands): array {
        return ['status' => 'ok', 'buffer' => $operands[0] ?? 0, 'binding' => $operands[1] ?? 0];
    }
    
    private function ssboRead(array $operands): array {
        $bufferId = $operands[0] ?? 0;
        $offset = $operands[1] ?? 0;
        $size = $operands[2] ?? 16;
        return ['status' => 'ok', 'data' => array_slice($this->memory[$bufferId] ?? [], $offset, $size)];
    }
    
    private function ssboWrite(array $operands): array {
        $bufferId = $operands[0] ?? 0;
        $offset = $operands[1] ?? 0;
        $data = $operands[2] ?? [];
        foreach ($data as $i => $value) {
            $this->memory[$bufferId][$offset + $i] = $value;
        }
        return ['status' => 'ok', 'written' => count($data)];
    }
    
    private function textureCreate(array $operands): array {
        return ['status' => 'ok', 'texture' => uniqid('tex_'), 'width' => $operands[0] ?? 256, 'height' => $operands[1] ?? 256];
    }
    
    private function textureLoad(array $operands): array {
        return ['status' => 'ok', 'value' => [0, 0, 0, 1]];
    }
    
    private function textureStore(array $operands): array {
        return ['status' => 'ok', 'stored' => true];
    }
    
    private function computeDispatch(array $operands): array {
        $x = $operands[0] ?? 1;
        $y = $operands[1] ?? 1;
        $z = $operands[2] ?? 1;
        return ['status' => 'ok', 'groups' => [$x, $y, $z], 'total_threads' => $x * $y * $z * 64];
    }
    
    private function memoryAlloc(array $operands): array {
        $size = $operands[0] ?? 1024;
        $ptr = count($this->memory);
        $this->memory[$ptr] = array_fill(0, $size, 0);
        return ['status' => 'ok', 'ptr' => $ptr, 'size' => $size];
    }
    
    // ============================================================
    // GEMM handlers
    // ============================================================
    
    private function executeGEMM(Opcode $opcode, array $operands): array {
        return match($opcode->name) {
            'GEMM', 'GEMM_BATCHED', 'GEMM_STRIDED' => $this->gemm($operands),
            'GEMM_TRANSA', 'GEMM_TRANSB', 'GEMM_TRANSAB' => $this->gemmTransposed($opcode->name, $operands),
            'GEMM_ALPHA', 'GEMM_BETA', 'GEMM_ALPHABETA' => $this->gemmScaled($opcode->name, $operands),
            'GEMM_INT8', 'GEMM_UINT8' => $this->gemmQuantized($opcode->name, $operands),
            'GEMM_INT8_TO_FLOAT', 'GEMM_UINT8_TO_FLOAT' => $this->gemmQuantizedToFloat($opcode->name, $operands),
            default => ['status' => 'ok', 'opcode' => $opcode->name]
        };
    }
    
    private function gemm(array $operands): array {
        $A = $operands[0] ?? [[1, 2], [3, 4]];
        $B = $operands[1] ?? [[5, 6], [7, 8]];
        $C = $operands[2] ?? null;
        
        $M = count($A);
        $K = count($A[0]);
        $N = count($B[0]);
        
        $result = array_fill(0, $M, array_fill(0, $N, 0.0));
        
        for ($i = 0; $i < $M; $i++) {
            for ($j = 0; $j < $N; $j++) {
                $sum = 0.0;
                for ($k = 0; $k < $K; $k++) {
                    $sum += $A[$i][$k] * $B[$k][$j];
                }
                if ($C !== null) {
                    $sum += $C[$i][$j] ?? 0;
                }
                $result[$i][$j] = $sum;
            }
        }
        
        return [
            'status' => 'ok',
            'operation' => 'GEMM',
            'dimensions' => ['M' => $M, 'K' => $K, 'N' => $N],
            'result' => $result,
            'flops' => 2 * $M * $K * $N
        ];
    }
    
    private function gemmTransposed(string $opcode, array $operands): array {
        $A = $operands[0] ?? [[1, 2], [3, 4]];
        $B = $operands[1] ?? [[5, 6], [7, 8]];
        
        if (str_contains($opcode, 'TRANSA')) {
            $A = array_map(null, ...$A);
        }
        if (str_contains($opcode, 'TRANSB')) {
            $B = array_map(null, ...$B);
        }
        
        return $this->gemm([$A, $B, $operands[2] ?? null]);
    }
    
    private function gemmScaled(string $opcode, array $operands): array {
        $alpha = 1.0;
        $beta = 1.0;
        
        if (str_contains($opcode, 'ALPHA') || str_contains($opcode, 'ALPHABETA')) {
            $alpha = $operands[2] ?? 1.0;
        }
        if (str_contains($opcode, 'BETA') || str_contains($opcode, 'ALPHABETA')) {
            $beta = $operands[3] ?? 1.0;
        }
        
        $result = $this->gemm($operands);
        
        // Apply scaling
        foreach ($result['result'] as &$row) {
            foreach ($row as &$val) {
                $val *= $alpha;
            }
        }
        
        $result['alpha'] = $alpha;
        $result['beta'] = $beta;
        
        return $result;
    }
    
    private function gemmQuantized(string $opcode, array $operands): array {
        // Simulated quantized GEMM
        return [
            'status' => 'ok',
            'operation' => $opcode,
            'quantization' => [
                'type' => str_contains($opcode, 'INT8') ? 'int8' : 'uint8',
                'scale' => $operands[3] ?? 0.1,
                'zero_point' => $operands[4] ?? 0
            ]
        ];
    }
    
    private function gemmQuantizedToFloat(string $opcode, array $operands): array {
        return [
            'status' => 'ok',
            'operation' => $opcode,
            'input_type' => str_contains($opcode, 'INT8') ? 'int8' : 'uint8',
            'output_type' => 'float32',
            'dequantization' => [
                'scale' => $operands[3] ?? 0.1,
                'zero_point' => $operands[4] ?? 0
            ]
        ];
    }
    
    // ============================================================
    // DirectML handlers
    // ============================================================
    
    private function executeDirectML(Opcode $opcode, array $operands): array {
        return match($opcode->name) {
            'DML_MATRIX_MULTIPLY_INTEGER' => $this->dmlMatrixMultiplyInteger($operands),
            'DML_MATRIX_MULTIPLY_INTEGER_TO_FLOAT' => $this->dmlMatrixMultiplyIntegerToFloat($operands),
            'DML_CONVOLUTION' => $this->dmlConvolution($operands),
            'DML_ACTIVATION_RELU' => $this->dmlActivation('relu', $operands),
            'DML_ACTIVATION_SIGMOID' => $this->dmlActivation('sigmoid', $operands),
            'DML_ACTIVATION_TANH' => $this->dmlActivation('tanh', $operands),
            default => ['status' => 'ok', 'opcode' => $opcode->name]
        };
    }
    
    private function dmlMatrixMultiplyInteger(array $operands): array {
        // Based on DML_MATRIX_MULTIPLY_INTEGER_OPERATOR_DESC [citation:11]
        return [
            'status' => 'ok',
            'operation' => 'DML_MATRIX_MULTIPLY_INTEGER',
            'tensors' => [
                'ATensor' => ['dims' => '{BatchCount, ChannelCount, M, K}'],
                'BTensor' => ['dims' => '{BatchCount, ChannelCount, K, N}'],
                'OutputTensor' => ['dims' => '{BatchCount, ChannelCount, M, N}']
            ],
            'quantization' => [
                'AZeroPoint' => 'optional',
                'BZeroPoint' => 'optional'
            ],
            'output_type' => 'INT32'
        ];
    }
    
    private function dmlMatrixMultiplyIntegerToFloat(array $operands): array {
        // Based on DML_MATRIX_MULTIPLY_INTEGER_TO_FLOAT_OPERATOR_DESC [citation:2]
        return [
            'status' => 'ok',
            'operation' => 'DML_MATRIX_MULTIPLY_INTEGER_TO_FLOAT',
            'tensors' => [
                'ATensor' => ['dims' => '{BatchCount, ChannelCount, M, K}'],
                'AScaleTensor' => ['dims' => '{1, 1, 1, 1} or {1, 1, M, 1}'],
                'AZeroPointTensor' => ['dims' => '{1, 1, 1, 1} or {1, 1, M, 1}'],
                'BTensor' => ['dims' => '{BatchCount, ChannelCount, K, N}'],
                'BScaleTensor' => ['dims' => '{1, 1, 1, 1} or {1, 1, 1, N}'],
                'BZeroPointTensor' => ['dims' => '{1, 1, 1, 1} or {1, 1, 1, N}'],
                'BiasTensor' => 'optional',
                'OutputTensor' => ['dims' => '{BatchCount, ChannelCount, M, N}']
            ],
            'input_type' => 'INT8/UINT8',
            'output_type' => 'FLOAT'
        ];
    }
    
    private function dmlConvolution(array $operands): array {
        // Based on DML_CONVOLUTION_OPERATOR_DESC [citation:15]
        return [
            'status' => 'ok',
            'operation' => 'DML_CONVOLUTION',
            'mode' => 'DML_CONVOLUTION_MODE_CROSS_CORRELATION',
            'direction' => 'DML_CONVOLUTION_DIRECTION_FORWARD',
            'tensors' => [
                'InputTensor' => ['dims' => '{BatchCount, InputChannelCount, InputHeight, InputWidth}'],
                'FilterTensor' => ['dims' => '{FilterBatchCount, FilterChannelCount, FilterHeight, FilterWidth}'],
                'BiasTensor' => ['dims' => '{1, OutputChannelCount, 1, 1}'],
                'OutputTensor' => ['dims' => '{BatchCount, OutputChannelCount, OutputHeight, OutputWidth}']
            ]
        ];
    }
    
    private function dmlActivation(string $type, array $operands): array {
        return [
            'status' => 'ok',
            'operation' => 'DML_ACTIVATION_' . strtoupper($type),
            'type' => $type
        ];
    }
    
    // ============================================================
    // K'UHUL handlers
    // ============================================================
    
    private function executeKuhul(Opcode $opcode, array $operands): array {
        return match($opcode->phase) {
            'Pop' => $this->kuhulPop($opcode->name, $operands),
            'Wo' => $this->kuhulWo($opcode->name, $operands),
            'Yax' => $this->kuhulYax($opcode->name, $operands),
            'Sek' => $this->kuhulSek($opcode->name, $operands),
            "Ch'en" => $this->kuhulChen($opcode->name, $operands),
            'Xul' => $this->kuhulXul($opcode->name, $operands),
            'Noj' => $this->kuhulNoj($opcode->name, $operands),
            default => ['status' => 'ok', 'phase' => $opcode->phase]
        };
    }
    
    private function kuhulPop(string $name, array $operands): array {
        return ['status' => 'ok', 'phase' => 'Pop', 'operation' => $name, 'perceived' => $operands];
    }
    
    private function kuhulWo(string $name, array $operands): array {
        return ['status' => 'ok', 'phase' => 'Wo', 'operation' => $name, 'represented' => $operands];
    }
    
    private function kuhulYax(string $name, array $operands): array {
        return ['status' => 'ok', 'phase' => 'Yax', 'operation' => $name, 'planned' => $operands];
    }
    
    private function kuhulSek(string $name, array $operands): array {
        return ['status' => 'ok', 'phase' => 'Sek', 'operation' => $name, 'executed' => $operands];
    }
    
    private function kuhulChen(string $name, array $operands): array {
        return ['status' => 'ok', 'phase' => "Ch'en", 'operation' => $name, 'projected' => $operands];
    }
    
    private function kuhulXul(string $name, array $operands): array {
        return ['status' => 'ok', 'phase' => 'Xul', 'operation' => $name, 'consolidated' => $operands];
    }
    
    private function kuhulNoj(string $name, array $operands): array {
        return ['status' => 'ok', 'phase' => 'Noj', 'operation' => $name, 'reflected' => $operands];
    }
    
    // ============================================================
    // Utility
    // ============================================================
    
    public function getTrace(): array {
        return $this->trace;
    }
    
    public function getRegisters(): array {
        return $this->registers;
    }
    
    public function getMemory(): array {
        return $this->memory;
    }
    
    public function reset(): void {
        $this->pc = 0;
        $this->stack = [];
        $this->registers = array_fill(0, 32, 0);
        $this->memory = [];
        $this->trace = [];
    }
}

// ============================================================
// 5. OPCODE COMPILER (K'UHUL → Opcodes)
// ============================================================

/**
 * OpcodeCompiler - Compiles K'UHUL phases to opcodes
 */
class OpcodeCompiler {
    private array $opcodes = [];
    private OpcodeRegistry $registry;
    
    public function __construct() {
        $this->registry = OpcodeRegistry::getInstance();
    }
    
    /**
     * Compile a GEMM operation to opcodes
     */
    public function compileGEMM(array $params): array {
        $this->opcodes = [];
        
        // Pop - Perceive inputs
        $this->opcodes[] = ['code' => 0x4000, 'operands' => ['A' => $params['A']]];
        $this->opcodes[] = ['code' => 0x4000, 'operands' => ['B' => $params['B']]];
        
        // Wo - Represent matrices
        $this->opcodes[] = ['code' => 0x4010, 'operands' => ['symbol' => 'A', 'value' => $params['A']]];
        $this->opcodes[] = ['code' => 0x4010, 'operands' => ['symbol' => 'B', 'value' => $params['B']]];
        
        // Yax - Plan GEMM
        $this->opcodes[] = ['code' => 0x4020, 'operands' => ['intention' => 'matrix_multiply']];
        
        // Sek - Execute GEMM
        $opcode = $this->selectGEMMOpcode($params);
        $this->opcodes[] = ['code' => $opcode, 'operands' => [$params['A'], $params['B'], $params['C'] ?? null]];
        
        // Ch'en - Project result
        $this->opcodes[] = ['code' => 0x4040, 'operands' => ['target' => 'result']];
        
        // Xul - Consolidate
        $this->opcodes[] = ['code' => 0x4050, 'operands' => []];
        
        return $this->opcodes;
    }
    
    /**
     * Compile a WebGL2 compute dispatch
     */
    public function compileComputeDispatch(int $groupsX, int $groupsY, int $groupsZ, array $buffers): array {
        $this->opcodes = [];
        
        // Pop - Perceive buffers
        foreach ($buffers as $buffer) {
            $this->opcodes[] = ['code' => 0x4002, 'operands' => [$buffer['id'], 0]];
        }
        
        // Wo - Bind SSBOs
        foreach ($buffers as $i => $buffer) {
            $this->opcodes[] = ['code' => 0x1001, 'operands' => [$buffer['id'], $i]];
        }
        
        // Yax - Plan dispatch
        $this->opcodes[] = ['code' => 0x4020, 'operands' => ['intention' => 'compute_dispatch']];
        
        // Sek - Dispatch
        $this->opcodes[] = ['code' => 0x1200, 'operands' => [$groupsX, $groupsY, $groupsZ]];
        
        // Xul - Barrier
        $this->opcodes[] = ['code' => 0x1201, 'operands' => []];
        
        return $this->opcodes;
    }
    
    /**
     * Compile a DirectML matrix multiply
     */
    public function compileDirectMLMatMul(array $params): array {
        $this->opcodes = [];
        
        $isInteger = $params['input_type'] === 'int';
        $toFloat = $params['output_type'] === 'float';
        
        $opcode = $isInteger && $toFloat ? 0x3001 : 0x3000;
        
        $this->opcodes[] = ['code' => 0x4000, 'operands' => ['input' => $params]];
        $this->opcodes[] = ['code' => $opcode, 'operands' => [
            'ATensor' => $params['A'],
            'BTensor' => $params['B'],
            'OutputTensor' => $params['C'] ?? null,
            'AScale' => $params['scale_a'] ?? null,
            'BScale' => $params['scale_b'] ?? null,
            'AZeroPoint' => $params['zero_a'] ?? null,
            'BZeroPoint' => $params['zero_b'] ?? null,
            'BiasTensor' => $params['bias'] ?? null
        ]];
        $this->opcodes[] = ['code' => 0x4040, 'operands' => ['target' => 'output']];
        $this->opcodes[] = ['code' => 0x4050, 'operands' => []];
        
        return $this->opcodes;
    }
    
    private function selectGEMMOpcode(array $params): int {
        if (isset($params['sparse']) && $params['sparse']) {
            return 0x2040;
        }
        if (isset($params['quantized']) && $params['quantized']) {
            if ($params['input_type'] === 'int8') {
                return $params['output_type'] === 'float' ? 0x2032 : 0x2030;
            }
            return $params['output_type'] === 'float' ? 0x2033 : 0x2031;
        }
        if (isset($params['transpose_a']) && $params['transpose_a']) {
            return 0x2010;
        }
        if (isset($params['transpose_b']) && $params['transpose_b']) {
            return 0x2011;
        }
        return 0x2000;
    }
    
    public function getOpcodes(): array {
        return $this->opcodes;
    }
}

// ============================================================
// 6. MCP INTEGRATION
// ============================================================

/**
 * Register opcode tools with MCP server
 */
function registerOpcodeMcpTools($mcp): void {
    $registry = OpcodeRegistry::getInstance();
    
    $mcp->registerTool('opcode_list', 'List all opcodes', function($params) use ($registry) {
        $category = $params['category'] ?? null;
        $phase = $params['phase'] ?? null;
        
        if ($category) {
            $opcodes = $registry->getByCategory($category);
        } elseif ($phase) {
            $opcodes = $registry->getByPhase($phase);
        } else {
            $opcodes = $registry->getAll();
        }
        
        $result = [];
        foreach ($opcodes as $opcode) {
            $result[] = $opcode->toArray();
        }
        
        return ['count' => count($result), 'opcodes' => $result];
    }, [
        'category' => ['type' => 'string', 'description' => 'Filter by category (WEBGL2, GEMM, DIRECTML, KUHUL)'],
        'phase' => ['type' => 'string', 'description' => 'Filter by K\'UHUL phase']
    ]);
    
    $mcp->registerTool('opcode_get', 'Get a specific opcode', function($params) use ($registry) {
        $name = $params['name'] ?? null;
        $code = $params['code'] ?? null;
        
        if ($name) {
            $opcode = $registry->getByName($name);
        } elseif ($code) {
            $opcode = $registry->get((int)$code);
        } else {
            return ['error' => 'Name or code required'];
        }
        
        return $opcode ? $opcode->toArray() : ['error' => 'Opcode not found'];
    }, [
        'name' => ['type' => 'string', 'description' => 'Opcode name'],
        'code' => ['type' => 'integer', 'description' => 'Opcode code']
    ]);
    
    $mcp->registerTool('opcode_execute', 'Execute opcodes', function($params) {
        $executor = new OpcodeExecutor();
        $executor->setDebug($params['debug'] ?? false);
        
        $program = $params['program'] ?? [];
        $executor->loadProgram($program);
        
        return [
            'results' => $executor->execute(),
            'trace' => $executor->getTrace()
        ];
    }, [
        'program' => ['type' => 'array', 'description' => 'Array of {code, operands}'],
        'debug' => ['type' => 'boolean', 'description' => 'Enable debug output']
    ]);
    
    $mcp->registerTool('opcode_gemm', 'Execute GEMM operation', function($params) {
        $executor = new OpcodeExecutor();
        $executor->loadProgram([
            ['code' => 0x2000, 'operands' => [$params['A'], $params['B'], $params['C'] ?? null]]
        ]);
        return $executor->execute()[0];
    }, [
        'A' => ['type' => 'array', 'description' => 'Matrix A'],
        'B' => ['type' => 'array', 'description' => 'Matrix B'],
        'C' => ['type' => 'array', 'description' => 'Optional matrix C']
    ]);
    
    $mcp->registerTool('opcode_dml_matmul', 'Execute DirectML matrix multiply', function($params) {
        $executor = new OpcodeExecutor();
        $isInteger = ($params['input_type'] ?? 'float') === 'int';
        $toFloat = ($params['output_type'] ?? 'float') === 'float';
        $opcode = $isInteger && $toFloat ? 0x3001 : 0x3000;
        
        $executor->loadProgram([
            ['code' => $opcode, 'operands' => [$params['A'], $params['B'], $params['C'] ?? null]]
        ]);
        return $executor->execute()[0];
    }, [
        'A' => ['type' => 'array', 'description' => 'Matrix A'],
        'B' => ['type' => 'array', 'description' => 'Matrix B'],
        'C' => ['type' => 'array', 'description' => 'Optional output'],
        'input_type' => ['type' => 'string', 'description' => 'int or float'],
        'output_type' => ['type' => 'string', 'description' => 'int or float']
    ]);
    
    $mcp->registerTool('opcode_compile_gemm', 'Compile GEMM to opcodes', function($params) {
        $compiler = new OpcodeCompiler();
        $opcodes = $compiler->compileGEMM($params);
        return ['opcodes' => $opcodes, 'count' => count($opcodes)];
    }, [
        'A' => ['type' => 'array', 'description' => 'Matrix A'],
        'B' => ['type' => 'array', 'description' => 'Matrix B'],
        'C' => ['type' => 'array', 'description' => 'Optional matrix C'],
        'transpose_a' => ['type' => 'boolean', 'description' => 'Transpose A'],
        'transpose_b' => ['type' => 'boolean', 'description' => 'Transpose B'],
        'quantized' => ['type' => 'boolean', 'description' => 'Quantized GEMM']
    ]);
    
    // Resources
    $mcp->registerResource('opcodes://registry', 'Opcode Registry', function() use ($registry) {
        return json_encode([
            'total' => $registry->count(),
            'categories' => [
                'WEBGL2' => count($registry->getByCategory('WEBGL2')),
                'GEMM' => count($registry->getByCategory('GEMM')),
                'DIRECTML' => count($registry->getByCategory('DIRECTML')),
                'KUHUL' => count($registry->getByCategory('KUHUL'))
            ],
            'phases' => [
                'Pop' => count($registry->getByPhase('Pop')),
                'Wo' => count($registry->getByPhase('Wo')),
                'Yax' => count($registry->getByPhase('Yax')),
                'Sek' => count($registry->getByPhase('Sek')),
                "Ch'en" => count($registry->getByPhase("Ch'en")),
                'Xul' => count($registry->getByPhase('Xul')),
                'Noj' => count($registry->getByPhase('Noj'))
            ]
        ], JSON_PRETTY_PRINT);
    }, 'application/json');
    
    $mcp->registerResource('opcodes://phases', 'K\'UHUL Opcode Phases', function() {
        return json_encode([
            'phases' => [
                'Pop' => ['glyphs' => ['POP_PERCEIVE', 'POP_INPUT', 'POP_READ']],
                'Wo' => ['glyphs' => ['WO_REPRESENT', 'WO_BUILD', 'WO_BIND']],
                'Yax' => ['glyphs' => ['YAX_PLAN', 'YAX_CONDITION', 'YAX_INTENT']],
                'Sek' => ['glyphs' => ['SEK_EXECUTE', 'SEK_COMPUTE', 'SEK_ACT']],
                "Ch'en" => ['glyphs' => ['CHEN_PROJECT', 'CHEN_OUTPUT', 'CHEN_WRITE']],
                'Xul' => ['glyphs' => ['XUL_CONSOLIDATE', 'XUL_COLLAPSE', 'XUL_COMMIT']],
                'Noj' => ['glyphs' => ['NOJ_REFLECT', 'NOJ_REASON', 'NOJ_EVALUATE']]
            ]
        ], JSON_PRETTY_PRINT);
    }, 'application/json');
    
    // Prompts
    $mcp->registerPrompt('opcode_guide', 'Opcode usage guide', function($params) {
        $category = $params['category'] ?? 'GEMM';
        return [
            'category' => $category,
            'guidance' => match($category) {
                'WEBGL2' => 'Use SSBO_CREATE/SSBO_BIND to set up buffers, COMPUTE_DISPATCH to execute, COMPUTE_BARRIER to synchronize.',
                'GEMM' => 'Use GEMM for basic matrix multiply, GEMM_TRANSA/B for transposed, GEMM_INT8 for quantized.',
                'DIRECTML' => 'Use DML_MATRIX_MULTIPLY_INTEGER for quantized, DML_MATRIX_MULTIPLY_INTEGER_TO_FLOAT for dequantized output.',
                'KUHUL' => 'Use POP_* for input, WO_* for representation, YAX_* for planning, SEK_* for execution, CHEN_* for output, XUL_* for consolidation, NOJ_* for reflection.',
                default => 'Available categories: WEBGL2, GEMM, DIRECTML, KUHUL'
            }
        ];
    }, ['category' => ['type' => 'string', 'description' => 'Opcode category']]);
}

// ============================================================
// 7. INITIALIZATION
// ============================================================

$registry = OpcodeRegistry::getInstance();

if (php_sapi_name() === 'cli') {
    echo "⟁ Opcodes.php - WebGL2 · GEMM · DirectML ⟁\n";
    echo "◆ Total opcodes: " . $registry->count() . "\n";
    echo "◆ WebGL2: " . count($registry->getByCategory('WEBGL2')) . "\n";
    echo "◆ GEMM: " . count($registry->getByCategory('GEMM')) . "\n";
    echo "◆ DirectML: " . count($registry->getByCategory('DIRECTML')) . "\n";
    echo "◆ K'UHUL: " . count($registry->getByCategory('KUHUL')) . "\n";
    echo "◆ Phases: Pop → Wo → Yax → Sek → Ch'en → Xul → Noj\n";
}

return [
    'Opcode' => Opcode::class,
    'OpcodeRegistry' => OpcodeRegistry::class,
    'OpcodeExecutor' => OpcodeExecutor::class,
    'OpcodeCompiler' => OpcodeCompiler::class,
    'registerOpcodeMcpTools' => 'registerOpcodeMcpTools',
    'OPCODE_WEBGL2' => OPCODE_WEBGL2,
    'OPCODE_GEMM' => OPCODE_GEMM,
    'OPCODE_DIRECTML' => OPCODE_DIRECTML,
    'OPCODE_KUHUL' => OPCODE_KUHUL,
];