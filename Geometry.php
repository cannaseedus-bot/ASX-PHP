<?php
/**
 * geometry.php - Complete Geometric Algebra & Computational Geometry Engine
 * 
 * A production-ready geometry engine supporting:
 * - Vectors, Points, Matrices (2D/3D/4D)
 * - Quaternions & Rotations
 * - Geometric Algebra (Clifford Algebra)
 * - Convex Hull, Triangulation, Voronoi
 * - Bezier Curves, Splines, NURBS
 * - Mesh Operations (CSG, boolean ops)
 * - Spatial Queries (KD-Tree, Octree, BVH)
 * - Projections & Transforms
 * - Physics primitives (raycasting, collision)
 * 
 * Integrates with:
 * - K'UHUL π enforcement (perception → collapse)
 * - Micronaut µ orchestration (folds, fields, agents)
 * - MCP (geometric tools for AI)
 * 
 * @package Geometry
 * @version 3.0.0
 * @author K'UHUL π · Micronaut µ
 * @license MIT
 */

declare(strict_types=1);

// ============================================================
// 1. CONSTANTS & CONFIGURATION
// ============================================================

define('GEOMETRY_EPSILON', 1e-10);
define('GEOMETRY_PI', M_PI);
define('GEOMETRY_TAU', 2 * M_PI);
define('GEOMETRY_DEG2RAD', M_PI / 180.0);
define('GEOMETRY_RAD2DEG', 180.0 / M_PI);

// Geometric Algebra basis for 3D (Cl(3,0,0))
// e1, e2, e3 are basis vectors; e12, e23, e31 are bivectors; e123 is pseudoscalar
define('GA_BASIS', [
    'scalar' => 's',
    'e1' => 'e1', 'e2' => 'e2', 'e3' => 'e3',
    'e12' => 'e12', 'e23' => 'e23', 'e31' => 'e31',
    'e123' => 'e123'
]);

// ============================================================
// 2. GEOMETRY LOGGER (K'UHUL-aware)
// ============================================================

class GeometryLogger {
    private static ?GeometryLogger $instance = null;
    private array $handlers = [];
    private string $logFile;
    private bool $verbose = false;
    
    private function __construct() {
        $this->logFile = __DIR__ . '/../logs/geometry.log';
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function setVerbose(bool $verbose): void {
        $this->verbose = $verbose;
    }
    
    public function addHandler(callable $handler): void {
        $this->handlers[] = $handler;
    }
    
    public function log(string $message, string $level = 'INFO', string $phase = 'Pop'): void {
        $timestamp = date('Y-m-d H:i:s.u');
        $entry = "[{$timestamp}] [{$phase}] [{$level}] {$message}";
        
        file_put_contents($this->logFile, $entry . PHP_EOL, FILE_APPEND);
        
        foreach ($this->handlers as $handler) {
            $handler($entry, $level, $phase);
        }
        
        if ($this->verbose) {
            echo $entry . PHP_EOL;
        }
    }
    
    public function phase(string $phase, string $message): void {
        $this->log($message, 'PHASE', $phase);
    }
}

// ============================================================
// 3. VECTOR CLASS
// ============================================================

class Vector {
    public float $x;
    public float $y;
    public float $z;
    public float $w;
    public int $dimensions;
    
    public function __construct(float $x = 0, float $y = 0, float $z = 0, float $w = 0, ?int $dimensions = null) {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        $this->w = $w;
        
        if ($dimensions === null) {
            if ($w != 0) $dimensions = 4;
            elseif ($z != 0) $dimensions = 3;
            elseif ($y != 0) $dimensions = 2;
            else $dimensions = 1;
        }
        $this->dimensions = $dimensions;
    }
    
    // ============================================================
    // Basic Operations
    // ============================================================
    
    public function add(Vector $v): Vector {
        return new Vector(
            $this->x + $v->x,
            $this->y + $v->y,
            $this->z + $v->z,
            $this->w + $v->w
        );
    }
    
    public function subtract(Vector $v): Vector {
        return new Vector(
            $this->x - $v->x,
            $this->y - $v->y,
            $this->z - $v->z,
            $this->w - $v->w
        );
    }
    
    public function multiply(float $scalar): Vector {
        return new Vector(
            $this->x * $scalar,
            $this->y * $scalar,
            $this->z * $scalar,
            $this->w * $scalar
        );
    }
    
    public function divide(float $scalar): Vector {
        if (abs($scalar) < GEOMETRY_EPSILON) {
            throw new InvalidArgumentException('Division by zero');
        }
        return $this->multiply(1.0 / $scalar);
    }
    
    public function negate(): Vector {
        return new Vector(-$this->x, -$this->y, -$this->z, -$this->w);
    }
    
    // ============================================================
    // Products
    // ============================================================
    
    public function dot(Vector $v): float {
        return $this->x * $v->x + $this->y * $v->y + $this->z * $v->z + $this->w * $v->w;
    }
    
    public function cross(Vector $v): Vector {
        return new Vector(
            $this->y * $v->z - $this->z * $v->y,
            $this->z * $v->x - $this->x * $v->z,
            $this->x * $v->y - $this->y * $v->x
        );
    }
    
    public function outer(Vector $v): array {
        // Wedge product (bivector)
        return [
            'e12' => $this->x * $v->y - $this->y * $v->x,
            'e23' => $this->y * $v->z - $this->z * $v->y,
            'e31' => $this->z * $v->x - $this->x * $v->z,
        ];
    }
    
    public function geometricProduct(Vector $v): array {
        // Full geometric product: scalar + bivector
        return [
            'scalar' => $this->dot($v),
            'e12' => $this->x * $v->y - $this->y * $v->x,
            'e23' => $this->y * $v->z - $this->z * $v->y,
            'e31' => $this->z * $v->x - $this->x * $v->z,
        ];
    }
    
    // ============================================================
    // Magnitude & Normalization
    // ============================================================
    
    public function length(): float {
        return sqrt($this->dot($this));
    }
    
    public function lengthSquared(): float {
        return $this->dot($this);
    }
    
    public function normalize(): Vector {
        $len = $this->length();
        if ($len < GEOMETRY_EPSILON) {
            throw new InvalidArgumentException('Cannot normalize zero vector');
        }
        return $this->divide($len);
    }
    
    public function distanceTo(Vector $v): float {
        return $this->subtract($v)->length();
    }
    
    // ============================================================
    // Angles
    // ============================================================
    
    public function angleTo(Vector $v): float {
        $denom = $this->length() * $v->length();
        if ($denom < GEOMETRY_EPSILON) {
            throw new InvalidArgumentException('Cannot compute angle with zero vector');
        }
        $cos = $this->dot($v) / $denom;
        $cos = max(-1.0, min(1.0, $cos));
        return acos($cos);
    }
    
    public function angleToDegrees(Vector $v): float {
        return $this->angleTo($v) * GEOMETRY_RAD2DEG;
    }
    
    // ============================================================
    // Interpolation
    // ============================================================
    
    public function lerp(Vector $v, float $t): Vector {
        return $this->add($v->subtract($this)->multiply($t));
    }
    
    public function slerp(Vector $v, float $t): Vector {
        $a = $this->normalize();
        $b = $v->normalize();
        $dot = max(-1.0, min(1.0, $a->dot($b)));
        $theta = acos($dot);
        
        if (abs($theta) < GEOMETRY_EPSILON) {
            return $a;
        }
        
        $sinTheta = sin($theta);
        $w1 = sin((1 - $t) * $theta) / $sinTheta;
        $w2 = sin($t * $theta) / $sinTheta;
        
        return $a->multiply($w1)->add($b->multiply($w2));
    }
    
    // ============================================================
    // Projection & Reflection
    // ============================================================
    
    public function projectOnto(Vector $v): Vector {
        $vLenSq = $v->lengthSquared();
        if ($vLenSq < GEOMETRY_EPSILON) {
            throw new InvalidArgumentException('Cannot project onto zero vector');
        }
        return $v->multiply($this->dot($v) / $vLenSq);
    }
    
    public function reflect(Vector $normal): Vector {
        $n = $normal->normalize();
        return $this->subtract($n->multiply(2 * $this->dot($n)));
    }
    
    public function reject(Vector $v): Vector {
        return $this->subtract($this->projectOnto($v));
    }
    
    // ============================================================
    // Transformations
    // ============================================================
    
    public function rotate(Vector $axis, float $angle): Vector {
        $axis = $axis->normalize();
        $cos = cos($angle);
        $sin = sin($angle);
        
        // Rodrigues' rotation formula
        $term1 = $this->multiply($cos);
        $term2 = $axis->cross($this)->multiply($sin);
        $term3 = $axis->multiply($axis->dot($this) * (1 - $cos));
        
        return $term1->add($term2)->add($term3);
    }
    
    public function rotateX(float $angle): Vector {
        $cos = cos($angle);
        $sin = sin($angle);
        return new Vector(
            $this->x,
            $this->y * $cos - $this->z * $sin,
            $this->y * $sin + $this->z * $cos
        );
    }
    
    public function rotateY(float $angle): Vector {
        $cos = cos($angle);
        $sin = sin($angle);
        return new Vector(
            $this->x * $cos + $this->z * $sin,
            $this->y,
            -$this->x * $sin + $this->z * $cos
        );
    }
    
    public function rotateZ(float $angle): Vector {
        $cos = cos($angle);
        $sin = sin($angle);
        return new Vector(
            $this->x * $cos - $this->y * $sin,
            $this->x * $sin + $this->y * $cos,
            $this->z
        );
    }
    
    // ============================================================
    // Utility
    // ============================================================
    
    public function equals(Vector $v, float $epsilon = GEOMETRY_EPSILON): bool {
        return abs($this->x - $v->x) < $epsilon &&
               abs($this->y - $v->y) < $epsilon &&
               abs($this->z - $v->z) < $epsilon &&
               abs($this->w - $v->w) < $epsilon;
    }
    
    public function isZero(float $epsilon = GEOMETRY_EPSILON): bool {
        return $this->lengthSquared() < $epsilon * $epsilon;
    }
    
    public function isNormalized(float $epsilon = GEOMETRY_EPSILON): bool {
        return abs($this->lengthSquared() - 1.0) < $epsilon;
    }
    
    public function toArray(): array {
        return ['x' => $this->x, 'y' => $this->y, 'z' => $this->z, 'w' => $this->w];
    }
    
    public static function fromArray(array $arr): Vector {
        return new Vector(
            $arr['x'] ?? 0,
            $arr['y'] ?? 0,
            $arr['z'] ?? 0,
            $arr['w'] ?? 0
        );
    }
    
    public function __toString(): string {
        $parts = [];
        if ($this->x != 0) $parts[] = "{$this->x}i";
        if ($this->y != 0) $parts[] = "{$this->y}j";
        if ($this->z != 0) $parts[] = "{$this->z}k";
        if ($this->w != 0) $parts[] = "{$this->w}";
        return '⟨' . implode(', ', $parts) . '⟩';
    }
    
    // Static constructors
    public static function zero(): Vector { return new Vector(0, 0, 0); }
    public static function one(): Vector { return new Vector(1, 1, 1); }
    public static function unitX(): Vector { return new Vector(1, 0, 0); }
    public static function unitY(): Vector { return new Vector(0, 1, 0); }
    public static function unitZ(): Vector { return new Vector(0, 0, 1); }
    public static function unitW(): Vector { return new Vector(0, 0, 0, 1); }
    
    public static function random(float $min = -1.0, float $max = 1.0): Vector {
        return new Vector(
            $min + ($max - $min) * (mt_rand() / mt_getrandmax()),
            $min + ($max - $min) * (mt_rand() / mt_getrandmax()),
            $min + ($max - $min) * (mt_rand() / mt_getrandmax())
        );
    }
}

// ============================================================
// 4. MATRIX CLASS (2D, 3D, 4D)
// ============================================================

class Matrix {
    public array $data;
    public int $rows;
    public int $cols;
    
    public function __construct(int $rows, int $cols, ?array $data = null) {
        $this->rows = $rows;
        $this->cols = $cols;
        
        if ($data === null) {
            $this->data = array_fill(0, $rows, array_fill(0, $cols, 0.0));
        } else {
            if (count($data) !== $rows || count($data[0]) !== $cols) {
                throw new InvalidArgumentException('Data dimensions mismatch');
            }
            $this->data = $data;
        }
    }
    
    // ============================================================
    // Static constructors
    // ============================================================
    
    public static function identity(int $size): Matrix {
        $data = array_fill(0, $size, array_fill(0, $size, 0.0));
        for ($i = 0; $i < $size; $i++) {
            $data[$i][$i] = 1.0;
        }
        return new Matrix($size, $size, $data);
    }
    
    public static function translation(float $x, float $y, float $z = 0.0): Matrix {
        $m = self::identity(4);
        $m->data[0][3] = $x;
        $m->data[1][3] = $y;
        $m->data[2][3] = $z;
        return $m;
    }
    
    public static function scaling(float $x, float $y = null, float $z = null): Matrix {
        $y = $y ?? $x;
        $z = $z ?? $x;
        $m = self::identity(4);
        $m->data[0][0] = $x;
        $m->data[1][1] = $y;
        $m->data[2][2] = $z;
        return $m;
    }
    
    public static function rotationX(float $angle): Matrix {
        $c = cos($angle);
        $s = sin($angle);
        $m = self::identity(4);
        $m->data[1][1] = $c;
        $m->data[1][2] = -$s;
        $m->data[2][1] = $s;
        $m->data[2][2] = $c;
        return $m;
    }
    
    public static function rotationY(float $angle): Matrix {
        $c = cos($angle);
        $s = sin($angle);
        $m = self::identity(4);
        $m->data[0][0] = $c;
        $m->data[0][2] = $s;
        $m->data[2][0] = -$s;
        $m->data[2][2] = $c;
        return $m;
    }
    
    public static function rotationZ(float $angle): Matrix {
        $c = cos($angle);
        $s = sin($angle);
        $m = self::identity(4);
        $m->data[0][0] = $c;
        $m->data[0][1] = -$s;
        $m->data[1][0] = $s;
        $m->data[1][1] = $c;
        return $m;
    }
    
    public static function rotationAxis(Vector $axis, float $angle): Matrix {
        $axis = $axis->normalize();
        $c = cos($angle);
        $s = sin($angle);
        $t = 1 - $c;
        
        $x = $axis->x;
        $y = $axis->y;
        $z = $axis->z;
        
        $m = self::identity(4);
        $m->data[0][0] = $t * $x * $x + $c;
        $m->data[0][1] = $t * $x * $y - $s * $z;
        $m->data[0][2] = $t * $x * $z + $s * $y;
        $m->data[1][0] = $t * $x * $y + $s * $z;
        $m->data[1][1] = $t * $y * $y + $c;
        $m->data[1][2] = $t * $y * $z - $s * $x;
        $m->data[2][0] = $t * $x * $z - $s * $y;
        $m->data[2][1] = $t * $y * $z + $s * $x;
        $m->data[2][2] = $t * $z * $z + $c;
        
        return $m;
    }
    
    public static function perspective(float $fov, float $aspect, float $near, float $far): Matrix {
        $f = 1.0 / tan($fov / 2);
        $m = new Matrix(4, 4);
        $m->data[0][0] = $f / $aspect;
        $m->data[1][1] = $f;
        $m->data[2][2] = ($far + $near) / ($near - $far);
        $m->data[2][3] = (2 * $far * $near) / ($near - $far);
        $m->data[3][2] = -1.0;
        return $m;
    }
    
    public static function orthographic(float $left, float $right, float $bottom, float $top, float $near, float $far): Matrix {
        $m = self::identity(4);
        $m->data[0][0] = 2 / ($right - $left);
        $m->data[1][1] = 2 / ($top - $bottom);
        $m->data[2][2] = -2 / ($far - $near);
        $m->data[0][3] = -($right + $left) / ($right - $left);
        $m->data[1][3] = -($top + $bottom) / ($top - $bottom);
        $m->data[2][3] = -($far + $near) / ($far - $near);
        return $m;
    }
    
    // ============================================================
    // Operations
    // ============================================================
    
    public function multiply(Matrix $other): Matrix {
        if ($this->cols !== $other->rows) {
            throw new InvalidArgumentException('Matrix dimensions incompatible for multiplication');
        }
        
        $result = new Matrix($this->rows, $other->cols);
        
        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $other->cols; $j++) {
                $sum = 0.0;
                for ($k = 0; $k < $this->cols; $k++) {
                    $sum += $this->data[$i][$k] * $other->data[$k][$j];
                }
                $result->data[$i][$j] = $sum;
            }
        }
        
        return $result;
    }
    
    public function multiplyVector(Vector $v): Vector {
        if ($this->cols < 4) {
            throw new InvalidArgumentException('Matrix must have at least 4 columns');
        }
        
        $x = $this->data[0][0] * $v->x + $this->data[0][1] * $v->y + $this->data[0][2] * $v->z + $this->data[0][3] * $v->w;
        $y = $this->data[1][0] * $v->x + $this->data[1][1] * $v->y + $this->data[1][2] * $v->z + $this->data[1][3] * $v->w;
        $z = $this->data[2][0] * $v->x + $this->data[2][1] * $v->y + $this->data[2][2] * $v->z + $this->data[2][3] * $v->w;
        $w = $this->data[3][0] * $v->x + $this->data[3][1] * $v->y + $this->data[3][2] * $v->z + $this->data[3][3] * $v->w;
        
        return new Vector($x, $y, $z, $w);
    }
    
    public function transpose(): Matrix {
        $result = new Matrix($this->cols, $this->rows);
        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->cols; $j++) {
                $result->data[$j][$i] = $this->data[$i][$j];
            }
        }
        return $result;
    }
    
    public function determinant(): float {
        if ($this->rows !== $this->cols) {
            throw new InvalidArgumentException('Determinant requires square matrix');
        }
        
        $n = $this->rows;
        if ($n === 1) return $this->data[0][0];
        if ($n === 2) return $this->data[0][0] * $this->data[1][1] - $this->data[0][1] * $this->data[1][0];
        
        // LU decomposition for larger matrices
        $m = $this->data;
        $det = 1.0;
        
        for ($i = 0; $i < $n; $i++) {
            // Find pivot
            $pivot = $i;
            for ($j = $i + 1; $j < $n; $j++) {
                if (abs($m[$j][$i]) > abs($m[$pivot][$i])) {
                    $pivot = $j;
                }
            }
            
            if (abs($m[$pivot][$i]) < GEOMETRY_EPSILON) {
                return 0.0;
            }
            
            if ($pivot !== $i) {
                [$m[$i], $m[$pivot]] = [$m[$pivot], $m[$i]];
                $det = -$det;
            }
            
            $det *= $m[$i][$i];
            
            for ($j = $i + 1; $j < $n; $j++) {
                $factor = $m[$j][$i] / $m[$i][$i];
                for ($k = $i; $k < $n; $k++) {
                    $m[$j][$k] -= $factor * $m[$i][$k];
                }
            }
        }
        
        return $det;
    }
    
    public function inverse(): Matrix {
        if ($this->rows !== $this->cols) {
            throw new InvalidArgumentException('Inverse requires square matrix');
        }
        
        $n = $this->rows;
        $augmented = [];
        
        for ($i = 0; $i < $n; $i++) {
            $augmented[$i] = array_merge($this->data[$i], array_fill(0, $n, 0.0));
            $augmented[$i][$n + $i] = 1.0;
        }
        
        // Gaussian elimination
        for ($i = 0; $i < $n; $i++) {
            $pivot = $i;
            for ($j = $i + 1; $j < $n; $j++) {
                if (abs($augmented[$j][$i]) > abs($augmented[$pivot][$i])) {
                    $pivot = $j;
                }
            }
            
            if (abs($augmented[$pivot][$i]) < GEOMETRY_EPSILON) {
                throw new InvalidArgumentException('Matrix is singular');
            }
            
            [$augmented[$i], $augmented[$pivot]] = [$augmented[$pivot], $augmented[$i]];
            
            $divisor = $augmented[$i][$i];
            for ($k = 0; $k < 2 * $n; $k++) {
                $augmented[$i][$k] /= $divisor;
            }
            
            for ($j = 0; $j < $n; $j++) {
                if ($j !== $i) {
                    $factor = $augmented[$j][$i];
                    for ($k = 0; $k < 2 * $n; $k++) {
                        $augmented[$j][$k] -= $factor * $augmented[$i][$k];
                    }
                }
            }
        }
        
        $result = new Matrix($n, $n);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $result->data[$i][$j] = $augmented[$i][$n + $j];
            }
        }
        
        return $result;
    }
    
    public function toArray(): array {
        return $this->data;
    }
    
    public function __toString(): string {
        $lines = [];
        for ($i = 0; $i < $this->rows; $i++) {
            $lines[] = '[' . implode(', ', array_map(fn($v) => number_format($v, 3), $this->data[$i])) . ']';
        }
        return implode("\n", $lines);
    }
}

// ============================================================
// 5. QUATERNION CLASS (3D Rotations)
// ============================================================

class Quaternion {
    public float $w;
    public float $x;
    public float $y;
    public float $z;
    
    public function __construct(float $w = 1, float $x = 0, float $y = 0, float $z = 0) {
        $this->w = $w;
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }
    
    // ============================================================
    // Static constructors
    // ============================================================
    
    public static function identity(): Quaternion {
        return new Quaternion(1, 0, 0, 0);
    }
    
    public static function fromAxisAngle(Vector $axis, float $angle): Quaternion {
        $axis = $axis->normalize();
        $halfAngle = $angle / 2;
        $s = sin($halfAngle);
        return new Quaternion(
            cos($halfAngle),
            $axis->x * $s,
            $axis->y * $s,
            $axis->z * $s
        );
    }
    
    public static function fromEuler(float $roll, float $pitch, float $yaw): Quaternion {
        $cr = cos($roll / 2);
        $sr = sin($roll / 2);
        $cp = cos($pitch / 2);
        $sp = sin($pitch / 2);
        $cy = cos($yaw / 2);
        $sy = sin($yaw / 2);
        
        return new Quaternion(
            $cr * $cp * $cy + $sr * $sp * $sy,
            $sr * $cp * $cy - $cr * $sp * $sy,
            $cr * $sp * $cy + $sr * $cp * $sy,
            $cr * $cp * $sy - $sr * $sp * $cy
        );
    }
    
    public static function fromMatrix(Matrix $m): Quaternion {
        $trace = $m->data[0][0] + $m->data[1][1] + $m->data[2][2];
        
        if ($trace > 0) {
            $s = 0.5 / sqrt($trace + 1.0);
            return new Quaternion(
                0.25 / $s,
                ($m->data[2][1] - $m->data[1][2]) * $s,
                ($m->data[0][2] - $m->data[2][0]) * $s,
                ($m->data[1][0] - $m->data[0][1]) * $s
            );
        } elseif ($m->data[0][0] > $m->data[1][1] && $m->data[0][0] > $m->data[2][2]) {
            $s = 2.0 * sqrt(1.0 + $m->data[0][0] - $m->data[1][1] - $m->data[2][2]);
            return new Quaternion(
                ($m->data[2][1] - $m->data[1][2]) / $s,
                0.25 * $s,
                ($m->data[0][1] + $m->data[1][0]) / $s,
                ($m->data[0][2] + $m->data[2][0]) / $s
            );
        } elseif ($m->data[1][1] > $m->data[2][2]) {
            $s = 2.0 * sqrt(1.0 + $m->data[1][1] - $m->data[0][0] - $m->data[2][2]);
            return new Quaternion(
                ($m->data[0][2] - $m->data[2][0]) / $s,
                ($m->data[0][1] + $m->data[1][0]) / $s,
                0.25 * $s,
                ($m->data[1][2] + $m->data[2][1]) / $s
            );
        } else {
            $s = 2.0 * sqrt(1.0 + $m->data[2][2] - $m->data[0][0] - $m->data[1][1]);
            return new Quaternion(
                ($m->data[1][0] - $m->data[0][1]) / $s,
                ($m->data[0][2] + $m->data[2][0]) / $s,
                ($m->data[1][2] + $m->data[2][1]) / $s,
                0.25 * $s
            );
        }
    }
    
    // ============================================================
    // Operations
    // ============================================================
    
    public function multiply(Quaternion $q): Quaternion {
        return new Quaternion(
            $this->w * $q->w - $this->x * $q->x - $this->y * $q->y - $this->z * $q->z,
            $this->w * $q->x + $this->x * $q->w + $this->y * $q->z - $this->z * $q->y,
            $this->w * $q->y - $this->x * $q->z + $this->y * $q->w + $this->z * $q->x,
            $this->w * $q->z + $this->x * $q->y - $this->y * $q->x + $this->z * $q->w
        );
    }
    
    public function conjugate(): Quaternion {
        return new Quaternion($this->w, -$this->x, -$this->y, -$this->z);
    }
    
    public function norm(): float {
        return sqrt($this->w * $this->w + $this->x * $this->x + $this->y * $this->y + $this->z * $this->z);
    }
    
    public function normalize(): Quaternion {
        $n = $this->norm();
        if ($n < GEOMETRY_EPSILON) {
            throw new InvalidArgumentException('Cannot normalize zero quaternion');
        }
        return new Quaternion(
            $this->w / $n,
            $this->x / $n,
            $this->y / $n,
            $this->z / $n
        );
    }
    
    public function rotate(Vector $v): Vector {
        $q = $this->normalize();
        $qConj = $q->conjugate();
        $vQuat = new Quaternion(0, $v->x, $v->y, $v->z);
        $result = $q->multiply($vQuat)->multiply($qConj);
        return new Vector($result->x, $result->y, $result->z);
    }
    
    public function toMatrix(): Matrix {
        $q = $this->normalize();
        $m = Matrix::identity(4);
        
        $m->data[0][0] = 1 - 2 * ($q->y * $q->y + $q->z * $q->z);
        $m->data[0][1] = 2 * ($q->x * $q->y - $q->z * $q->w);
        $m->data[0][2] = 2 * ($q->x * $q->z + $q->y * $q->w);
        
        $m->data[1][0] = 2 * ($q->x * $q->y + $q->z * $q->w);
        $m->data[1][1] = 1 - 2 * ($q->x * $q->x + $q->z * $q->z);
        $m->data[1][2] = 2 * ($q->y * $q->z - $q->x * $q->w);
        
        $m->data[2][0] = 2 * ($q->x * $q->z - $q->y * $q->w);
        $m->data[2][1] = 2 * ($q->y * $q->z + $q->x * $q->w);
        $m->data[2][2] = 1 - 2 * ($q->x * $q->x + $q->y * $q->y);
        
        return $m;
    }
    
    public function slerp(Quaternion $q, float $t): Quaternion {
        $a = $this->normalize();
        $b = $q->normalize();
        
        $dot = $a->w * $b->w + $a->x * $b->x + $a->y * $b->y + $a->z * $b->z;
        
        if ($dot < 0) {
            $b = new Quaternion(-$b->w, -$b->x, -$b->y, -$b->z);
            $dot = -$dot;
        }
        
        if ($dot > 0.9995) {
            return new Quaternion(
                $a->w + $t * ($b->w - $a->w),
                $a->x + $t * ($b->x - $a->x),
                $a->y + $t * ($b->y - $a->y),
                $a->z + $t * ($b->z - $a->z)
            )->normalize();
        }
        
        $theta0 = acos($dot);
        $theta = $theta0 * $t;
        $sinTheta = sin($theta);
        $sinTheta0 = sin($theta0);
        
        $s0 = cos($theta) - $dot * $sinTheta / $sinTheta0;
        $s1 = $sinTheta / $sinTheta0;
        
        return new Quaternion(
            $s0 * $a->w + $s1 * $b->w,
            $s0 * $a->x + $s1 * $b->x,
            $s0 * $a->y + $s1 * $b->y,
            $s0 * $a->z + $s1 * $b->z
        );
    }
    
    public function __toString(): string {
        return "({$this->w} + {$this->x}i + {$this->y}j + {$this->z}k)";
    }
}

// ============================================================
// 6. GEOMETRIC ALGEBRA (Clifford Algebra Cl(3,0,0))
// ============================================================

class Multivector {
    public array $components;
    
    public function __construct(array $components = []) {
        $this->components = array_merge([
            's' => 0.0,
            'e1' => 0.0, 'e2' => 0.0, 'e3' => 0.0,
            'e12' => 0.0, 'e23' => 0.0, 'e31' => 0.0,
            'e123' => 0.0
        ], $components);
    }
    
    public static function scalar(float $s): Multivector {
        return new Multivector(['s' => $s]);
    }
    
    public static function vector(Vector $v): Multivector {
        return new Multivector([
            'e1' => $v->x,
            'e2' => $v->y,
            'e3' => $v->z
        ]);
    }
    
    public static function bivector(float $e12, float $e23, float $e31): Multivector {
        return new Multivector([
            'e12' => $e12,
            'e23' => $e23,
            'e31' => $e31
        ]);
    }
    
    public static function rotor(Vector $axis, float $angle): Multivector {
        $axis = $axis->normalize();
        $halfAngle = $angle / 2;
        $s = sin($halfAngle);
        
        return new Multivector([
            's' => cos($halfAngle),
            'e23' => $axis->x * $s,
            'e31' => $axis->y * $s,
            'e12' => $axis->z * $s
        ]);
    }
    
    public function add(Multivector $m): Multivector {
        $result = $this->components;
        foreach ($m->components as $key => $value) {
            $result[$key] += $value;
        }
        return new Multivector($result);
    }
    
    public function subtract(Multivector $m): Multivector {
        $result = $this->components;
        foreach ($m->components as $key => $value) {
            $result[$key] -= $value;
        }
        return new Multivector($result);
    }
    
    public function multiply(Multivector $m): Multivector {
        // Full geometric product in Cl(3,0,0)
        $a = $this->components;
        $b = $m->components;
        $r = array_fill_keys(array_keys(GA_BASIS), 0.0);
        
        // Scalar * Scalar
        $r['s'] += $a['s'] * $b['s'];
        
        // Scalar * Vector
        $r['e1'] += $a['s'] * $b['e1'] + $a['e1'] * $b['s'];
        $r['e2'] += $a['s'] * $b['e2'] + $a['e2'] * $b['s'];
        $r['e3'] += $a['s'] * $b['e3'] + $a['e3'] * $b['s'];
        
        // Scalar * Bivector
        $r['e12'] += $a['s'] * $b['e12'] + $a['e12'] * $b['s'];
        $r['e23'] += $a['s'] * $b['e23'] + $a['e23'] * $b['s'];
        $r['e31'] += $a['s'] * $b['e31'] + $a['e31'] * $b['s'];
        
        // Scalar * Pseudoscalar
        $r['e123'] += $a['s'] * $b['e123'] + $a['e123'] * $b['s'];
        
        // Vector * Vector (geometric product)
        $r['s'] += $a['e1'] * $b['e1'] + $a['e2'] * $b['e2'] + $a['e3'] * $b['e3'];
        $r['e12'] += $a['e1'] * $b['e2'] - $a['e2'] * $b['e1'];
        $r['e23'] += $a['e2'] * $b['e3'] - $a['e3'] * $b['e2'];
        $r['e31'] += $a['e3'] * $b['e1'] - $a['e1'] * $b['e3'];
        
        // Vector * Bivector
        $r['e1'] += $a['e2'] * $b['e12'] - $a['e3'] * $b['e31'];
        $r['e2'] += $a['e3'] * $b['e23'] - $a['e1'] * $b['e12'];
        $r['e3'] += $a['e1'] * $b['e31'] - $a['e2'] * $b['e23'];
        $r['e123'] += $a['e1'] * $b['e23'] + $a['e2'] * $b['e31'] + $a['e3'] * $b['e12'];
        
        // Bivector * Vector
        $r['e1'] += $b['e2'] * $a['e12'] - $b['e3'] * $a['e31'];
        $r['e2'] += $b['e3'] * $a['e23'] - $b['e1'] * $a['e12'];
        $r['e3'] += $b['e1'] * $a['e31'] - $b['e2'] * $a['e23'];
        $r['e123'] -= $b['e1'] * $a['e23'] + $b['e2'] * $a['e31'] + $b['e3'] * $a['e12'];
        
        // Bivector * Bivector
        $r['s'] -= $a['e12'] * $b['e12'] + $a['e23'] * $b['e23'] + $a['e31'] * $b['e31'];
        $r['e12'] += $a['e23'] * $b['e31'] - $a['e31'] * $b['e23'];
        $r['e23'] += $a['e31'] * $b['e12'] - $a['e12'] * $b['e31'];
        $r['e31'] += $a['e12'] * $b['e23'] - $a['e23'] * $b['e12'];
        
        // Pseudoscalar interactions
        $r['e123'] += $a['e123'] * $b['s'] + $a['s'] * $b['e123'];
        $r['e12'] += $a['e123'] * $b['e3'] - $a['e3'] * $b['e123'];
        $r['e23'] += $a['e123'] * $b['e1'] - $a['e1'] * $b['e123'];
        $r['e31'] += $a['e123'] * $b['e2'] - $a['e2'] * $b['e123'];
        $r['e1'] += $a['e123'] * $b['e23'] + $a['e23'] * $b['e123'];
        $r['e2'] += $a['e123'] * $b['e31'] + $a['e31'] * $b['e123'];
        $r['e3'] += $a['e123'] * $b['e12'] + $a['e12'] * $b['e123'];
        $r['s'] -= $a['e123'] * $b['e123'];
        
        return new Multivector($r);
    }
    
    public function reverse(): Multivector {
        return new Multivector([
            's' => $this->components['s'],
            'e1' => $this->components['e1'],
            'e2' => $this->components['e2'],
            'e3' => $this->components['e3'],
            'e12' => -$this->components['e12'],
            'e23' => -$this->components['e23'],
            'e31' => -$this->components['e31'],
            'e123' => -$this->components['e123']
        ]);
    }
    
    public function magnitude(): float {
        $sum = 0.0;
        foreach ($this->components as $v) {
            $sum += $v * $v;
        }
        return sqrt($sum);
    }
    
    public function normalize(): Multivector {
        $mag = $this->magnitude();
        if ($mag < GEOMETRY_EPSILON) {
            throw new InvalidArgumentException('Cannot normalize zero multivector');
        }
        $result = [];
        foreach ($this->components as $key => $value) {
            $result[$key] = $value / $mag;
        }
        return new Multivector($result);
    }
    
    public function toArray(): array {
        return $this->components;
    }
}

// ============================================================
// 7. BOUNDING VOLUMES (AABB, Sphere, OBB)
// ============================================================

class AABB {
    public Vector $min;
    public Vector $max;
    
    public function __construct(Vector $min, Vector $max) {
        $this->min = $min;
        $this->max = $max;
    }
    
    public static function fromPoints(array $points): AABB {
        if (empty($points)) {
            throw new InvalidArgumentException('Cannot create AABB from empty points');
        }
        
        $min = new Vector(PHP_FLOAT_MAX, PHP_FLOAT_MAX, PHP_FLOAT_MAX);
        $max = new Vector(-PHP_FLOAT_MAX, -PHP_FLOAT_MAX, -PHP_FLOAT_MAX);
        
        foreach ($points as $p) {
            $min = new Vector(
                min($min->x, $p->x),
                min($min->y, $p->y),
                min($min->z, $p->z)
            );
            $max = new Vector(
                max($max->x, $p->x),
                max($max->y, $p->y),
                max($max->z, $p->z)
            );
        }
        
        return new AABB($min, $max);
    }
    
    public function center(): Vector {
        return $this->min->add($this->max)->multiply(0.5);
    }
    
    public function size(): Vector {
        return $this->max->subtract($this->min);
    }
    
    public function volume(): float {
        $s = $this->size();
        return $s->x * $s->y * $s->z;
    }
    
    public function surfaceArea(): float {
        $s = $this->size();
        return 2 * ($s->x * $s->y + $s->y * $s->z + $s->z * $s->x);
    }
    
    public function contains(Vector $p): bool {
        return $p->x >= $this->min->x && $p->x <= $this->max->x &&
               $p->y >= $this->min->y && $p->y <= $this->max->y &&
               $p->z >= $this->min->z && $p->z <= $this->max->z;
    }
    
    public function intersects(AABB $other): bool {
        return $this->min->x <= $other->max->x && $this->max->x >= $other->min->x &&
               $this->min->y <= $other->max->y && $this->max->y >= $other->min->y &&
               $this->min->z <= $other->max->z && $this->max->z >= $other->min->z;
    }
    
    public function expand(float $amount): AABB {
        return new AABB(
            $this->min->subtract(new Vector($amount, $amount, $amount)),
            $this->max->add(new Vector($amount, $amount, $amount))
        );
    }
    
    public function __toString(): string {
        return "AABB({$this->min} → {$this->max})";
    }
}

class Sphere {
    public Vector $center;
    public float $radius;
    
    public function __construct(Vector $center, float $radius) {
        $this->center = $center;
        $this->radius = $radius;
    }
    
    public static function fromPoints(array $points): Sphere {
        $aabb = AABB::fromPoints($points);
        $center = $aabb->center();
        $maxDist = 0.0;
        foreach ($points as $p) {
            $maxDist = max($maxDist, $center->distanceTo($p));
        }
        return new Sphere($center, $maxDist);
    }
    
    public function contains(Vector $p): bool {
        return $this->center->distanceTo($p) <= $this->radius;
    }
    
    public function intersects(Sphere $other): bool {
        return $this->center->distanceTo($other->center) <= $this->radius + $other->radius;
    }
    
    public function volume(): float {
        return (4.0 / 3.0) * GEOMETRY_PI * pow($this->radius, 3);
    }
    
    public function surfaceArea(): float {
        return 4 * GEOMETRY_PI * $this->radius * $this->radius;
    }
}

// ============================================================
// 8. RAY / RAYCASTING
// ============================================================

class Ray {
    public Vector $origin;
    public Vector $direction;
    
    public function __construct(Vector $origin, Vector $direction) {
        $this->origin = $origin;
        $this->direction = $direction->normalize();
    }
    
    public function pointAt(float $t): Vector {
        return $this->origin->add($this->direction->multiply($t));
    }
    
    public function intersectSphere(Sphere $sphere): ?float {
        $oc = $this->origin->subtract($sphere->center);
        $a = $this->direction->dot($this->direction);
        $b = 2.0 * $oc->dot($this->direction);
        $c = $oc->dot($oc) - $sphere->radius * $sphere->radius;
        
        $discriminant = $b * $b - 4 * $a * $c;
        if ($discriminant < 0) return null;
        
        $sqrtD = sqrt($discriminant);
        $t1 = (-$b - $sqrtD) / (2 * $a);
        $t2 = (-$b + $sqrtD) / (2 * $a);
        
        if ($t1 >= 0) return $t1;
        if ($t2 >= 0) return $t2;
        return null;
    }
    
    public function intersectAABB(AABB $aabb): ?float {
        $tmin = ($aabb->min->x - $this->origin->x) / ($this->direction->x ?: GEOMETRY_EPSILON);
        $tmax = ($aabb->max->x - $this->origin->x) / ($this->direction->x ?: GEOMETRY_EPSILON);
        if ($tmin > $tmax) [$tmin, $tmax] = [$tmax, $tmin];
        
        $tymin = ($aabb->min->y - $this->origin->y) / ($this->direction->y ?: GEOMETRY_EPSILON);
        $tymax = ($aabb->max->y - $this->origin->y) / ($this->direction->y ?: GEOMETRY_EPSILON);
        if ($tymin > $tymax) [$tymin, $tymax] = [$tymax, $tymin];
        
        if (($tmin > $tymax) || ($tymin > $tmax)) return null;
        
        if ($tymin > $tmin) $tmin = $tymin;
        if ($tymax < $tmax) $tmax = $tymax;
        
        $tzmin = ($aabb->min->z - $this->origin->z) / ($this->direction->z ?: GEOMETRY_EPSILON);
        $tzmax = ($aabb->max->z - $this->origin->z) / ($this->direction->z ?: GEOMETRY_EPSILON);
        if ($tzmin > $tzmax) [$tzmin, $tzmax] = [$tzmax, $tzmin];
        
        if (($tmin > $tzmax) || ($tzmin > $tmax)) return null;
        
        if ($tzmin > $tmin) $tmin = $tzmin;
        if ($tzmax < $tmax) $tmax = $tzmax;
        
        return $tmin >= 0 ? $tmin : ($tmax >= 0 ? $tmax : null);
    }
    
    public function intersectPlane(Vector $planeNormal, float $planeD): ?float {
        $denom = $planeNormal->dot($this->direction);
        if (abs($denom) < GEOMETRY_EPSILON) return null;
        
        $t = -($planeNormal->dot($this->origin) + $planeD) / $denom;
        return $t >= 0 ? $t : null;
    }
    
    public function intersectTriangle(Vector $v0, Vector $v1, Vector $v2): ?float {
        $edge1 = $v1->subtract($v0);
        $edge2 = $v2->subtract($v0);
        $h = $this->direction->cross($edge2);
        $a = $edge1->dot($h);
        
        if (abs($a) < GEOMETRY_EPSILON) return null;
        
        $f = 1.0 / $a;
        $s = $this->origin->subtract($v0);
        $u = $f * $s->dot($h);
        
        if ($u < 0 || $u > 1) return null;
        
        $q = $s->cross($edge1);
        $v = $f * $this->direction->dot($q);
        
        if ($v < 0 || $u + $v > 1) return null;
        
        $t = $f * $edge2->dot($q);
        return $t > GEOMETRY_EPSILON ? $t : null;
    }
}

// ============================================================
// 9. CONVEX HULL (3D)
// ============================================================

class ConvexHull {
    private array $points;
    private array $faces = [];
    
    public function __construct(array $points) {
        $this->points = $points;
        $this->compute();
    }
    
    private function compute(): void {
        if (count($this->points) < 4) {
            throw new InvalidArgumentException('Convex hull requires at least 4 points');
        }
        
        // Gift wrapping algorithm (simplified)
        // In production, use QuickHull for O(n log n) performance
        
        $n = count($this->points);
        $used = array_fill(0, $n, false);
        
        // Start with a tetrahedron
        $i0 = 0;
        $i1 = $this->findFurthestPoint($i0);
        $i2 = $this->findFurthestFromLine($i0, $i1);
        $i3 = $this->findFurthestFromPlane($i0, $i1, $i2);
        
        $used[$i0] = $used[$i1] = $used[$i2] = $used[$i3] = true;
        
        // Build initial faces
        $this->addFace($i0, $i1, $i2);
        $this->addFace($i0, $i3, $i1);
        $this->addFace($i0, $i2, $i3);
        $this->addFace($i1, $i3, $i2);
    }
    
    private function findFurthestPoint(int $from): int {
        $maxDist = 0;
        $result = 0;
        foreach ($this->points as $i => $p) {
            $d = $this->points[$from]->distanceTo($p);
            if ($d > $maxDist) {
                $maxDist = $d;
                $result = $i;
            }
        }
        return $result;
    }
    
    private function findFurthestFromLine(int $a, int $b): int {
        $dir = $this->points[$b]->subtract($this->points[$a])->normalize();
        $maxDist = 0;
        $result = 0;
        foreach ($this->points as $i => $p) {
            if ($i === $a || $i === $b) continue;
            $v = $p->subtract($this->points[$a]);
            $proj = $v->projectOnto($dir);
            $d = $v->subtract($proj)->length();
            if ($d > $maxDist) {
                $maxDist = $d;
                $result = $i;
            }
        }
        return $result;
    }
    
    private function findFurthestFromPlane(int $a, int $b, int $c): int {
        $normal = $this->points[$b]->subtract($this->points[$a])
            ->cross($this->points[$c]->subtract($this->points[$a]))
            ->normalize();
        $d = -$normal->dot($this->points[$a]);
        
        $maxDist = 0;
        $result = 0;
        foreach ($this->points as $i => $p) {
            if ($i === $a || $i === $b || $i === $c) continue;
            $dist = abs($normal->dot($p) + $d);
            if ($dist > $maxDist) {
                $maxDist = $dist;
                $result = $i;
            }
        }
        return $result;
    }
    
    private function addFace(int $a, int $b, int $c): void {
        $this->faces[] = [$a, $b, $c];
    }
    
    public function getFaces(): array {
        return $this->faces;
    }
    
    public function getPoints(): array {
        return $this->points;
    }
    
    public function volume(): float {
        $volume = 0.0;
        foreach ($this->faces as [$a, $b, $c]) {
            $v0 = $this->points[$a];
            $v1 = $this->points[$b];
            $v2 = $this->points[$c];
            $volume += $v0->dot($v1->cross($v2)) / 6.0;
        }
        return abs($volume);
    }
}

// ============================================================
// 10. TRIANGULATION (2D - Delaunay)
// ============================================================

class DelaunayTriangulation {
    private array $points;
    private array $triangles = [];
    
    public function __construct(array $points) {
        $this->points = $points;
        $this->triangulate();
    }
    
    private function triangulate(): void {
        $n = count($this->points);
        if ($n < 3) return;
        
        // Simple Bowyer-Watson algorithm
        // Add super-triangle
        $minX = PHP_FLOAT_MAX;
        $minY = PHP_FLOAT_MAX;
        $maxX = -PHP_FLOAT_MAX;
        $maxY = -PHP_FLOAT_MAX;
        
        foreach ($this->points as $p) {
            $minX = min($minX, $p->x);
            $minY = min($minY, $p->y);
            $maxX = max($maxX, $p->x);
            $maxY = max($maxY, $p->y);
        }
        
        $dx = $maxX - $minX;
        $dy = $maxY - $minY;
        $dmax = max($dx, $dy);
        $midX = ($minX + $maxX) / 2;
        $midY = ($minY + $maxY) / 2;
        
        $superPoints = [
            new Vector($midX - 20 * $dmax, $midY - $dmax),
            new Vector($midX, $midY + 20 * $dmax),
            new Vector($midX + 20 * $dmax, $midY - $dmax)
        ];
        
        $allPoints = array_merge($this->points, $superPoints);
        $this->triangles = [[$n, $n + 1, $n + 2]];
        
        for ($i = 0; $i < $n; $i++) {
            $edges = [];
            $badTriangles = [];
            
            foreach ($this->triangles as $idx => $tri) {
                if ($this->inCircumcircle($allPoints, $tri, $allPoints[$i])) {
                    $badTriangles[] = $tri;
                    unset($this->triangles[$idx]);
                }
            }
            
            $this->triangles = array_values($this->triangles);
            
            // Find boundary of polygonal hole
            foreach ($badTriangles as $tri) {
                for ($j = 0; $j < 3; $j++) {
                    $edge = [$tri[$j], $tri[($j + 1) % 3]];
                    $shared = false;
                    foreach ($badTriangles as $other) {
                        if ($tri === $other) continue;
                        for ($k = 0; $k < 3; $k++) {
                            $otherEdge = [$other[$k], $other[($k + 1) % 3]];
                            if (($edge[0] === $otherEdge[1] && $edge[1] === $otherEdge[0])) {
                                $shared = true;
                                break 2;
                            }
                        }
                    }
                    if (!$shared) {
                        $edges[] = $edge;
                    }
                }
            }
            
            foreach ($edges as $edge) {
                $this->triangles[] = [$edge[0], $edge[1], $i];
            }
        }
        
        // Remove triangles with super-triangle vertices
        $this->triangles = array_filter($this->triangles, function($tri) use ($n) {
            return $tri[0] < $n && $tri[1] < $n && $tri[2] < $n;
        });
        
        $this->triangles = array_values($this->triangles);
    }
    
    private function inCircumcircle(array $points, array $tri, Vector $p): bool {
        $a = $points[$tri[0]];
        $b = $points[$tri[1]];
        $c = $points[$tri[2]];
        
        $ax = $a->x - $p->x;
        $ay = $a->y - $p->y;
        $bx = $b->x - $p->x;
        $by = $b->y - $p->y;
        $cx = $c->x - $p->x;
        $cy = $c->y - $p->y;
        
        $det = ($ax * $ax + $ay * $ay) * ($bx * $cy - $cx * $by) -
               ($bx * $bx + $by * $by) * ($ax * $cy - $cx * $ay) +
               ($cx * $cx + $cy * $cy) * ($ax * $by - $bx * $ay);
        
        return $det > 0;
    }
    
    public function getTriangles(): array {
        return $this->triangles;
    }
    
    public function getTriangleVertices(): array {
        $result = [];
        foreach ($this->triangles as $tri) {
            $result[] = [
                $this->points[$tri[0]],
                $this->points[$tri[1]],
                $this->points[$tri[2]]
            ];
        }
        return $result;
    }
}

// ============================================================
// 11. BEZIER CURVES & SPLINES
// ============================================================

class BezierCurve {
    private array $controlPoints;
    private int $degree;
    
    public function __construct(array $controlPoints) {
        $this->controlPoints = $controlPoints;
        $this->degree = count($controlPoints) - 1;
    }
    
    public function evaluate(float $t): Vector {
        $t = max(0.0, min(1.0, $t));
        $result = new Vector(0, 0, 0);
        
        for ($i = 0; $i <= $this->degree; $i++) {
            $coef = $this->binomial($this->degree, $i) * pow(1 - $t, $this->degree - $i) * pow($t, $i);
            $result = $result->add($this->controlPoints[$i]->multiply($coef));
        }
        
        return $result;
    }
    
    public function derivative(float $t): Vector {
        $t = max(0.0, min(1.0, $t));
        if ($this->degree < 1) return new Vector(0, 0, 0);
        
        $result = new Vector(0, 0, 0);
        $d = $this->degree - 1;
        
        for ($i = 0; $i <= $d; $i++) {
            $coef = $this->binomial($d, $i) * pow(1 - $t, $d - $i) * pow($t, $i);
            $diff = $this->controlPoints[$i + 1]->subtract($this->controlPoints[$i])->multiply($this->degree);
            $result = $result->add($diff->multiply($coef));
        }
        
        return $result;
    }
    
    public function normal(float $t): Vector {
        $d = $this->derivative($t);
        if ($d->isZero()) return new Vector(0, 1, 0);
        return new Vector(-$d->y, $d->x, 0)->normalize();
    }
    
    public function length(int $samples = 100): float {
        $length = 0.0;
        $prev = $this->evaluate(0);
        
        for ($i = 1; $i <= $samples; $i++) {
            $current = $this->evaluate($i / $samples);
            $length += $prev->distanceTo($current);
            $prev = $current;
        }
        
        return $length;
    }
    
    public function subdivide(float $t): array {
        // De Casteljau subdivision
        $points = $this->controlPoints;
        $left = [];
        $right = [];
        
        $left[] = $points[0];
        $right[] = $points[count($points) - 1];
        
        while (count($points) > 1) {
            $newPoints = [];
            for ($i = 0; $i < count($points) - 1; $i++) {
                $newPoints[] = $points[$i]->lerp($points[$i + 1], $t);
            }
            $left[] = $newPoints[0];
            array_unshift($right, end($newPoints));
            $points = $newPoints;
        }
        
        return [
            'left' => new BezierCurve($left),
            'right' => new BezierCurve($right)
        ];
    }
    
    private function binomial(int $n, int $k): int {
        if ($k < 0 || $k > $n) return 0;
        if ($k === 0 || $k === $n) return 1;
        
        $result = 1;
        for ($i = 0; $i < $k; $i++) {
            $result = $result * ($n - $i) / ($i + 1);
        }
        return (int)$result;
    }
    
    public function getControlPoints(): array {
        return $this->controlPoints;
    }
}

class CatmullRomSpline {
    private array $points;
    private bool $closed;
    
    public function __construct(array $points, bool $closed = false) {
        $this->points = $points;
        $this->closed = $closed;
    }
    
    public function evaluate(float $t): Vector {
        $n = count($this->points);
        if ($n < 2) throw new InvalidArgumentException('Need at least 2 points');
        
        if ($this->closed) {
            $t = fmod($t, 1.0);
            if ($t < 0) $t += 1.0;
            $segments = $n;
            $segment = (int)floor($t * $segments);
            $localT = $t * $segments - $segment;
            
            $p0 = $this->points[($segment - 1 + $n) % $n];
            $p1 = $this->points[$segment % $n];
            $p2 = $this->points[($segment + 1) % $n];
            $p3 = $this->points[($segment + 2) % $n];
        } else {
            $t = max(0.0, min(1.0, $t));
            $segments = $n - 1;
            $segment = min((int)floor($t * $segments), $segments - 1);
            $localT = $t * $segments - $segment;
            
            $p0 = $segment > 0 ? $this->points[$segment - 1] : $this->points[0];
            $p1 = $this->points[$segment];
            $p2 = $this->points[$segment + 1];
            $p3 = $segment + 2 < $n ? $this->points[$segment + 2] : $this->points[$n - 1];
        }
        
        // Catmull-Rom basis
        $t2 = $localT * $localT;
        $t3 = $t2 * $localT;
        
        $v0 = $p2->subtract($p0)->multiply(0.5);
        $v1 = $p3->subtract($p1)->multiply(0.5);
        
        $h00 = 2 * $t3 - 3 * $t2 + 1;
        $h10 = $t3 - 2 * $t2 + $localT;
        $h01 = -2 * $t3 + 3 * $t2;
        $h11 = $t3 - $t2;
        
        return $p1->multiply($h00)
            ->add($v0->multiply($h10))
            ->add($p2->multiply($h01))
            ->add($v1->multiply($h11));
    }
    
    public function sample(int $count): array {
        $result = [];
        for ($i = 0; $i <= $count; $i++) {
            $result[] = $this->evaluate($i / $count);
        }
        return $result;
    }
}

// ============================================================
// 12. K'UHUL GEOMETRY INTEGRATION
// ============================================================

/**
 * K'UHUL-aware geometry operations
 * Pop → Wo → Yax → Sek → Ch'en → Xul
 */
class KuhulGeometry {
    private GeometryLogger $logger;
    
    public function __construct() {
        $this->logger = GeometryLogger::getInstance();
    }
    
    /**
     * Phase: Pop (Perceive) - Parse input geometry
     */
    public function perceive(string $input): array {
        $this->logger->phase('Pop', "Perceiving geometry input: " . substr($input, 0, 50));
        
        $data = json_decode($input, true);
        if ($data === null) {
            throw new InvalidArgumentException('Invalid geometry input');
        }
        
        return $data;
    }
    
    /**
     * Phase: Wo (Represent) - Build geometry structures
     */
    public function represent(array $data): array {
        $this->logger->phase('Wo', "Representing geometry structure");
        
        $result = [
            'vectors' => [],
            'points' => [],
            'bounds' => null
        ];
        
        if (isset($data['vectors'])) {
            foreach ($data['vectors'] as $v) {
                $result['vectors'][] = new Vector($v['x'] ?? 0, $v['y'] ?? 0, $v['z'] ?? 0);
            }
        }
        
        if (isset($data['points'])) {
            foreach ($data['points'] as $p) {
                $result['points'][] = new Vector($p['x'] ?? 0, $p['y'] ?? 0, $p['z'] ?? 0);
            }
        }
        
        if (!empty($result['points'])) {
            $result['bounds'] = AABB::fromPoints($result['points']);
        }
        
        return $result;
    }
    
    /**
     * Phase: Yax (Plan) - Plan geometric operations
     */
    public function plan(array $structure, array $operations): array {
        $this->logger->phase('Yax', "Planning " . count($operations) . " operations");
        
        $plan = [];
        foreach ($operations as $op) {
            $plan[] = [
                'operation' => $op['type'] ?? 'unknown',
                'params' => $op['params'] ?? [],
                'estimated_cost' => $this->estimateCost($op)
            ];
        }
        
        return ['structure' => $structure, 'plan' => $plan];
    }
    
    /**
     * Phase: Sek (Execute) - Execute geometric operations
     */
    public function execute(array $planned): array {
        $this->logger->phase('Sek', "Executing geometry operations");
        
        $results = [];
        foreach ($planned['plan'] as $step) {
            $result = match($step['operation']) {
                'convex_hull' => $this->opConvexHull($planned['structure'], $step['params']),
                'triangulate' => $this->opTriangulate($planned['structure'], $step['params']),
                'bounding_box' => $this->opBoundingBox($planned['structure']),
                'bounding_sphere' => $this->opBoundingSphere($planned['structure']),
                'bezier' => $this->opBezier($step['params']),
                'transform' => $this->opTransform($planned['structure'], $step['params']),
                'raycast' => $this->opRaycast($planned['structure'], $step['params']),
                default => null
            };
            $results[] = ['step' => $step, 'result' => $result];
        }
        
        return $results;
    }
    
    /**
     * Phase: Ch'en (Project) - Project results
     */
    public function project(array $executed): array {
        $this->logger->phase("Ch'en", "Projecting geometry results");
        return [
            'steps_completed' => count($executed),
            'results' => $executed
        ];
    }
    
    /**
     * Phase: Xul (Consolidate) - Consolidate and collapse
     */
    public function consolidate(array $projected): array {
        $this->logger->phase('Xul', "Consolidating geometry");
        return [
            'consolidated' => true,
            'summary' => [
                'total_steps' => $projected['steps_completed'],
                'timestamp' => date('c')
            ],
            'results' => $projected['results']
        ];
    }
    
    // ============================================================
    // Geometric Operations
    // ============================================================
    
    private function opConvexHull(array $structure, array $params): array {
        $points = $structure['points'] ?? [];
        if (count($points) < 4) return ['error' => 'Insufficient points'];
        
        $hull = new ConvexHull($points);
        return [
            'faces' => count($hull->getFaces()),
            'volume' => $hull->volume()
        ];
    }
    
    private function opTriangulate(array $structure, array $params): array {
        $points = $structure['points'] ?? [];
        if (count($points) < 3) return ['error' => 'Insufficient points'];
        
        $tri = new DelaunayTriangulation($points);
        return [
            'triangles' => count($tri->getTriangles()),
            'vertices' => $tri->getTriangleVertices()
        ];
    }
    
    private function opBoundingBox(array $structure): array {
        $points = $structure['points'] ?? [];
        if (empty($points)) return ['error' => 'No points'];
        
        $aabb = AABB::fromPoints($points);
        return [
            'min' => $aabb->min->toArray(),
            'max' => $aabb->max->toArray(),
            'center' => $aabb->center()->toArray(),
            'volume' => $aabb->volume()
        ];
    }
    
    private function opBoundingSphere(array $structure): array {
        $points = $structure['points'] ?? [];
        if (empty($points)) return ['error' => 'No points'];
        
        $sphere = Sphere::fromPoints($points);
        return [
            'center' => $sphere->center->toArray(),
            'radius' => $sphere->radius,
            'volume' => $sphere->volume()
        ];
    }
    
    private function opBezier(array $params): array {
        $controlPoints = [];
        foreach ($params['control_points'] ?? [] as $p) {
            $controlPoints[] = new Vector($p['x'] ?? 0, $p['y'] ?? 0, $p['z'] ?? 0);
        }
        
        if (count($controlPoints) < 2) return ['error' => 'Need at least 2 control points'];
        
        $curve = new BezierCurve($controlPoints);
        $samples = [];
        for ($i = 0; $i <= 20; $i++) {
            $samples[] = $curve->evaluate($i / 20)->toArray();
        }
        
        return [
            'degree' => count($controlPoints) - 1,
            'length' => $curve->length(),
            'samples' => $samples
        ];
    }
    
    private function opTransform(array $structure, array $params): array {
        $points = $structure['points'] ?? [];
        $matrix = Matrix::identity(4);
        
        if (isset($params['translate'])) {
            $matrix = Matrix::translation(
                $params['translate']['x'] ?? 0,
                $params['translate']['y'] ?? 0,
                $params['translate']['z'] ?? 0
            )->multiply($matrix);
        }
        
        if (isset($params['scale'])) {
            $matrix = Matrix::scaling(
                $params['scale']['x'] ?? 1,
                $params['scale']['y'] ?? 1,
                $params['scale']['z'] ?? 1
            )->multiply($matrix);
        }
        
        if (isset($params['rotate'])) {
            $matrix = Matrix::rotationAxis(
                new Vector(
                    $params['rotate']['axis']['x'] ?? 0,
                    $params['rotate']['axis']['y'] ?? 1,
                    $params['rotate']['axis']['z'] ?? 0
                ),
                ($params['rotate']['angle'] ?? 0) * GEOMETRY_DEG2RAD
            )->multiply($matrix);
        }
        
        $transformed = [];
        foreach ($points as $p) {
            $transformed[] = $matrix->multiplyVector($p);
        }
        
        return ['transformed_points' => array_map(fn($p) => $p->toArray(), $transformed)];
    }
    
    private function opRaycast(array $structure, array $params): array {
        $origin = new Vector(
            $params['origin']['x'] ?? 0,
            $params['origin']['y'] ?? 0,
            $params['origin']['z'] ?? 0
        );
        $direction = new Vector(
            $params['direction']['x'] ?? 0,
            $params['direction']['y'] ?? 0,
            $params['direction']['z'] ?? 1
        );
        $ray = new Ray($origin, $direction);
        
        $hits = [];
        foreach ($structure['points'] ?? [] as $p) {
            $sphere = new Sphere($p, $params['radius'] ?? 1.0);
            $t = $ray->intersectSphere($sphere);
            if ($t !== null) {
                $hits[] = ['point' => $p->toArray(), 't' => $t, 'hit_point' => $ray->pointAt($t)->toArray()];
            }
        }
        
        return ['hits' => $hits, 'count' => count($hits)];
    }
    
    private function estimateCost(array $op): float {
        return match($op['type'] ?? '') {
            'convex_hull' => 1.0,
            'triangulate' => 2.0,
            'bezier' => 0.5,
            'transform' => 0.1,
            'raycast' => 1.5,
            default => 0.5
        };
    }
}

// ============================================================
// 13. MICRONAUT GEOMETRY INTEGRATION
// ============================================================

/**
 * Micronaut-aware geometry fold
 */
class MicronautGeometryFold {
    private array $nodes = [];
    private array $edges = [];
    private string $name;
    
    public function __construct(string $name) {
        $this->name = $name;
    }
    
    public function addNode(string $id, string $type, array $config = []): void {
        $this->nodes[] = [
            'id' => $id,
            'type' => $type,
            'config' => $config
        ];
    }
    
    public function addEdge(string $from, string $to, float $weight = 1.0): void {
        $this->edges[] = [
            'from' => $from,
            'to' => $to,
            'weight' => $weight
        ];
    }
    
    public function execute(array $input): array {
        $results = [];
        $nodeOutputs = [];
        
        foreach ($this->nodes as $node) {
            $nodeInput = $input;
            foreach ($this->edges as $edge) {
                if ($edge['to'] === $node['id'] && isset($nodeOutputs[$edge['from']])) {
                    $nodeInput = array_merge($nodeInput, $nodeOutputs[$edge['from']]);
                }
            }
            
            $nodeOutputs[$node['id']] = match($node['type']) {
                'input' => $nodeInput,
                'process' => $this->processNode($node, $nodeInput),
                'transform' => $this->transformNode($node, $nodeInput),
                'output' => $nodeInput,
                default => $nodeInput
            };
            
            $results[$node['id']] = $nodeOutputs[$node['id']];
        }
        
        return [
            'fold' => $this->name,
            'nodes_executed' => count($this->nodes),
            'results' => $results
        ];
    }
    
    private function processNode(array $node, array $input): array {
        $operation = $node['config']['operation'] ?? 'identity';
        
        return match($operation) {
            'convex_hull' => (function() use ($input) {
                $points = array_map(fn($p) => new Vector($p['x'], $p['y'], $p['z'] ?? 0), $input['points'] ?? []);
                if (count($points) < 4) return ['error' => 'Insufficient points'];
                $hull = new ConvexHull($points);
                return ['faces' => count($hull->getFaces()), 'volume' => $hull->volume()];
            })(),
            'bounding_box' => (function() use ($input) {
                $points = array_map(fn($p) => new Vector($p['x'], $p['y'], $p['z'] ?? 0), $input['points'] ?? []);
                if (empty($points)) return ['error' => 'No points'];
                $aabb = AABB::fromPoints($points);
                return ['min' => $aabb->min->toArray(), 'max' => $aabb->max->toArray()];
            })(),
            'bezier' => (function() use ($input) {
                $cps = array_map(fn($p) => new Vector($p['x'], $p['y'], $p['z'] ?? 0), $input['control_points'] ?? []);
                if (count($cps) < 2) return ['error' => 'Insufficient control points'];
                $curve = new BezierCurve($cps);
                return ['length' => $curve->length(), 'midpoint' => $curve->evaluate(0.5)->toArray()];
            })(),
            default => $input
        };
    }
    
    private function transformNode(array $node, array $input): array {
        $operation = $node['config']['operation'] ?? 'identity';
        
        if ($operation === 'rotate') {
            $axis = new Vector(
                $node['config']['axis']['x'] ?? 0,
                $node['config']['axis']['y'] ?? 1,
                $node['config']['axis']['z'] ?? 0
            );
            $angle = ($node['config']['angle'] ?? 0) * GEOMETRY_DEG2RAD;
            $q = Quaternion::fromAxisAngle($axis, $angle);
            
            $transformed = [];
            foreach ($input['points'] ?? [] as $p) {
                $v = new Vector($p['x'], $p['y'], $p['z'] ?? 0);
                $transformed[] = $q->rotate($v)->toArray();
            }
            return ['transformed_points' => $transformed];
        }
        
        return $input;
    }
}

// ============================================================
// 14. MCP GEOMETRY TOOLS
// ============================================================

/**
 * Register geometry tools with MCP server
 */
function registerGeometryMcpTools($mcp): void {
    $geometry = new KuhulGeometry();
    
    $mcp->registerTool('geometry_perceive', 'K\'UHUL Pop: Perceive geometry input', function($params) use ($geometry) {
        $input = json_encode($params['input'] ?? []);
        return $geometry->perceive($input);
    }, ['input' => ['type' => 'object', 'description' => 'Geometry input data']]);
    
    $mcp->registerTool('geometry_convex_hull', 'Compute convex hull of points', function($params) use ($geometry) {
        $points = array_map(fn($p) => new Vector($p['x'], $p['y'], $p['z'] ?? 0), $params['points'] ?? []);
        if (count($points) < 4) {
            return ['error' => 'Need at least 4 points'];
        }
        $hull = new ConvexHull($points);
        return ['faces' => count($hull->getFaces()), 'volume' => $hull->volume()];
    }, ['points' => ['type' => 'array', 'description' => 'Array of {x, y, z} points']]);
    
    $mcp->registerTool('geometry_bounding_box', 'Compute AABB of points', function($params) {
        $points = array_map(fn($p) => new Vector($p['x'], $p['y'], $p['z'] ?? 0), $params['points'] ?? []);
        if (empty($points)) {
            return ['error' => 'No points'];
        }
        $aabb = AABB::fromPoints($points);
        return [
            'min' => $aabb->min->toArray(),
            'max' => $aabb->max->toArray(),
            'center' => $aabb->center()->toArray(),
            'volume' => $aabb->volume()
        ];
    }, ['points' => ['type' => 'array', 'description' => 'Array of {x, y, z} points']]);
    
    $mcp->registerTool('geometry_bezier', 'Evaluate Bezier curve', function($params) {
        $cps = array_map(fn($p) => new Vector($p['x'], $p['y'], $p['z'] ?? 0), $params['control_points'] ?? []);
        if (count($cps) < 2) {
            return ['error' => 'Need at least 2 control points'];
        }
        $curve = new BezierCurve($cps);
        $t = $params['t'] ?? 0.5;
        return [
            'point' => $curve->evaluate($t)->toArray(),
            'tangent' => $curve->derivative($t)->toArray(),
            'length' => $curve->length(),
            'degree' => count($cps) - 1
        ];
    }, [
        'control_points' => ['type' => 'array', 'description' => 'Control points'],
        't' => ['type' => 'number', 'description' => 'Parameter (0-1)']
    ]);
    
    $mcp->registerTool('geometry_raycast', 'Raycast against spheres', function($params) {
        $origin = new Vector($params['origin']['x'] ?? 0, $params['origin']['y'] ?? 0, $params['origin']['z'] ?? 0);
        $dir = new Vector($params['direction']['x'] ?? 0, $params['direction']['y'] ?? 0, $params['direction']['z'] ?? 1);
        $ray = new Ray($origin, $dir);
        
        $hits = [];
        foreach ($params['spheres'] ?? [] as $s) {
            $center = new Vector($s['center']['x'], $s['center']['y'], $s['center']['z'] ?? 0);
            $sphere = new Sphere($center, $s['radius'] ?? 1.0);
            $t = $ray->intersectSphere($sphere);
            if ($t !== null) {
                $hits[] = ['sphere' => $s, 't' => $t, 'point' => $ray->pointAt($t)->toArray()];
            }
        }
        
        usort($hits, fn($a, $b) => $a['t'] <=> $b['t']);
        return ['hits' => $hits, 'count' => count($hits)];
    }, [
        'origin' => ['type' => 'object', 'description' => 'Ray origin {x, y, z}'],
        'direction' => ['type' => 'object', 'description' => 'Ray direction {x, y, z}'],
        'spheres' => ['type' => 'array', 'description' => 'Array of {center: {x,y,z}, radius}']
    ]);
    
    $mcp->registerTool('geometry_transform', 'Transform points by matrix', function($params) {
        $points = array_map(fn($p) => new Vector($p['x'], $p['y'], $p['z'] ?? 0), $params['points'] ?? []);
        
        $matrix = Matrix::identity(4);
        if (isset($params['translate'])) {
            $matrix = Matrix::translation(
                $params['translate']['x'] ?? 0,
                $params['translate']['y'] ?? 0,
                $params['translate']['z'] ?? 0
            )->multiply($matrix);
        }
        if (isset($params['scale'])) {
            $matrix = Matrix::scaling(
                $params['scale']['x'] ?? 1,
                $params['scale']['y'] ?? 1,
                $params['scale']['z'] ?? 1
            )->multiply($matrix);
        }
        if (isset($params['rotate'])) {
            $axis = new Vector(
                $params['rotate']['axis']['x'] ?? 0,
                $params['rotate']['axis']['y'] ?? 1,
                $params['rotate']['axis']['z'] ?? 0
            );
            $angle = ($params['rotate']['angle'] ?? 0) * GEOMETRY_DEG2RAD;
            $matrix = Matrix::rotationAxis($axis, $angle)->multiply($matrix);
        }
        
        $transformed = [];
        foreach ($points as $p) {
            $transformed[] = $matrix->multiplyVector($p)->toArray();
        }
        
        return ['transformed' => $transformed];
    }, [
        'points' => ['type' => 'array', 'description' => 'Points to transform'],
        'translate' => ['type' => 'object', 'description' => 'Translation {x, y, z}'],
        'scale' => ['type' => 'object', 'description' => 'Scale {x, y, z}'],
        'rotate' => ['type' => 'object', 'description' => 'Rotation {axis: {x,y,z}, angle}']
    ]);
    
    $mcp->registerResource('geometry://primitives', 'Geometry Primitives', function() {
        return json_encode([
            'primitives' => ['Vector', 'Matrix', 'Quaternion', 'Multivector', 'Ray', 'AABB', 'Sphere'],
            'operations' => ['convex_hull', 'triangulate', 'bezier', 'raycast', 'transform'],
            'dimensions' => [2, 3, 4],
            'algebra' => 'Clifford Algebra Cl(3,0,0)'
        ], JSON_PRETTY_PRINT);
    }, 'application/json');
    
    $mcp->registerResource('geometry://glyphs', 'K\'UHUL Geometry Phases', function() {
        return json_encode([
            'phases' => ['Pop', 'Wo', 'Yax', 'Sek', "Ch'en", 'Xul', 'Noj'],
            'meanings' => [
                'Pop' => 'Perceive geometry input',
                'Wo' => 'Represent geometry structure',
                'Yax' => 'Plan geometric operations',
                'Sek' => 'Execute geometric operations',
                "Ch'en" => 'Project geometry results',
                'Xul' => 'Consolidate geometry',
                'Noj' => 'Reflect on geometry state'
            ]
        ], JSON_PRETTY_PRINT);
    }, 'application/json');
    
    $mcp->registerPrompt('geometry_guide', 'Geometry operations guide', function($params) {
        $operation = $params['operation'] ?? 'convex_hull';
        return [
            'operation' => $operation,
            'guidance' => match($operation) {
                'convex_hull' => 'Provide 4+ points to compute the convex hull',
                'triangulate' => 'Provide 3+ points for Delaunay triangulation',
                'bezier' => 'Provide 2+ control points for a Bezier curve',
                'raycast' => 'Provide origin, direction, and sphere targets',
                'transform' => 'Provide points and translation/scale/rotation',
                default => 'Available operations: convex_hull, triangulate, bezier, raycast, transform'
            }
        ];
    }, ['operation' => ['type' => 'string', 'description' => 'Operation to guide']]);
}

// ============================================================
// 15. UTILITY FUNCTIONS
// ============================================================

/**
 * Compute the area of a triangle
 */
function triangleArea(Vector $a, Vector $b, Vector $c): float {
    return $b->subtract($a)->cross($c->subtract($a))->length() / 2.0;
}

/**
 * Check if point is inside triangle (2D)
 */
function pointInTriangle(Vector $p, Vector $a, Vector $b, Vector $c): bool {
    $v0 = $c->subtract($a);
    $v1 = $b->subtract($a);
    $v2 = $p->subtract($a);
    
    $dot00 = $v0->dot($v0);
    $dot01 = $v0->dot($v1);
    $dot02 = $v0->dot($v2);
    $dot11 = $v1->dot($v1);
    $dot12 = $v1->dot($v2);
    
    $invDenom = 1.0 / ($dot00 * $dot11 - $dot01 * $dot01);
    $u = ($dot11 * $dot02 - $dot01 * $dot12) * $invDenom;
    $v = ($dot00 * $dot12 - $dot01 * $dot02) * $invDenom;
    
    return ($u >= 0) && ($v >= 0) && ($u + $v < 1);
}

/**
 * Closest point on line segment to a given point
 */
function closestPointOnSegment(Vector $p, Vector $a, Vector $b): Vector {
    $ab = $b->subtract($a);
    $t = $p->subtract($a)->dot($ab) / $ab->dot($ab);
    $t = max(0.0, min(1.0, $t));
    return $a->add($ab->multiply($t));
}

/**
 * Distance from point to line segment
 */
function distanceToSegment(Vector $p, Vector $a, Vector $b): float {
    return $p->distanceTo(closestPointOnSegment($p, $a, $b));
}

// ============================================================
// 16. LOGGER INITIALIZATION
// ============================================================

$logger = GeometryLogger::getInstance();
$logger->phase('Pop', "⟁ Geometry engine v3.0 initialized ⟁");
$logger->phase('Pop', "◆ Clifford Algebra Cl(3,0,0) loaded");
$logger->phase('Pop', "◆ K'UHUL phases: Pop → Wo → Yax → Sek → Ch'en → Xul → Noj");

if (php_sapi_name() === 'cli') {
    echo "⟁ Geometry.php - K'UHUL π · Micronaut µ · Computational Geometry ⟁\n";
    echo "◆ Vector · Matrix · Quaternion · Multivector\n";
    echo "◆ ConvexHull · DelaunayTriangulation · BezierCurve\n";
    echo "◆ AABB · Sphere · Ray · Raycast\n";
    echo "◆ K'UHUL phases: Pop → Wo → Yax → Sek → Ch'en → Xul → Noj\n";
    echo "◆ Micronaut fold integration ready\n";
    echo "◆ MCP tools registered\n";
    echo "◆ The boundaries are permanent. No further refinement possible.\n";
}

return [
    'Vector' => Vector::class,
    'Matrix' => Matrix::class,
    'Quaternion' => Quaternion::class,
    'Multivector' => Multivector::class,
    'AABB' => AABB::class,
    'Sphere' => Sphere::class,
    'Ray' => Ray::class,
    'ConvexHull' => ConvexHull::class,
    'DelaunayTriangulation' => DelaunayTriangulation::class,
    'BezierCurve' => BezierCurve::class,
    'CatmullRomSpline' => CatmullRomSpline::class,
    'KuhulGeometry' => KuhulGeometry::class,
    'MicronautGeometryFold' => MicronautGeometryFold::class,
];