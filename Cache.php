<?php
// src/Cache.php

class Cache {
    private $cacheDir;
    private $defaultTtl;
    private static $instance = null;
    
    private function __construct() {
        $config = Config::getInstance();
        $this->cacheDir = $config->get('cache_dir');
        $this->defaultTtl = $config->get('cache_ttl');
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Store data in cache
     */
    public function set($key, $data, $ttl = null) {
        $ttl = $ttl ?? $this->defaultTtl;
        $filename = $this->getFilename($key);
        $expires = time() + $ttl;
        
        $cacheData = [
            'expires' => $expires,
            'data' => $data,
            'created' => time()
        ];
        
        // Use atomic write with locking
        $tempFile = $filename . '.tmp';
        file_put_contents($tempFile, serialize($cacheData), LOCK_EX);
        rename($tempFile, $filename);
        
        return true;
    }
    
    /**
     * Retrieve data from cache
     */
    public function get($key) {
        $filename = $this->getFilename($key);
        
        if (!file_exists($filename)) {
            return null;
        }
        
        $cacheData = @unserialize(file_get_contents($filename));
        
        if (!$cacheData || !isset($cacheData['expires']) || !isset($cacheData['data'])) {
            return null;
        }
        
        // Check if expired
        if ($cacheData['expires'] < time()) {
            $this->delete($key);
            return null;
        }
        
        return $cacheData['data'];
    }
    
    /**
     * Check if cache exists and is valid
     */
    public function has($key) {
        return $this->get($key) !== null;
    }
    
    /**
     * Delete cache entry
     */
    public function delete($key) {
        $filename = $this->getFilename($key);
        if (file_exists($filename)) {
            unlink($filename);
            return true;
        }
        return false;
    }
    
    /**
     * Clear all cache
     */
    public function clear() {
        $files = glob($this->cacheDir . '/*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
        return true;
    }
    
    /**
     * Get cache stats
     */
    public function stats() {
        $files = glob($this->cacheDir . '/*.cache');
        $count = count($files);
        $size = 0;
        foreach ($files as $file) {
            $size += filesize($file);
        }
        
        return [
            'entries' => $count,
            'size' => $size,
            'size_human' => $this->formatBytes($size),
            'directory' => $this->cacheDir
        ];
    }
    
    private function getFilename($key) {
        $hash = md5($key);
        return $this->cacheDir . '/' . $hash . '.cache';
    }
    
    private function formatBytes($bytes) {
        if ($bytes === 0) return '0 B';
        $k = 1024;
        $sizes = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}