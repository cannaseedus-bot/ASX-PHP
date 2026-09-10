<?php
// src/DnsResolver.php

class DnsResolver {
    private $cache;
    private $config;
    private static $instance = null;
    
    private function __construct() {
        $this->cache = Cache::getInstance();
        $this->config = Config::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Resolve a domain to IP addresses with caching
     */
    public function resolve($domain, $type = 'A') {
        // Validate domain
        if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            return ['error' => 'Invalid domain name'];
        }
        
        $cacheKey = "dns_{$domain}_{$type}";
        $cached = $this->cache->get($cacheKey);
        
        if ($cached !== null) {
            return $cached;
        }
        
        // Perform DNS lookup
        $results = $this->lookup($domain, $type);
        
        if ($results) {
            $this->cache->set($cacheKey, $results, $this->config->get('dns_ttl'));
        }
        
        return $results;
    }
    
    /**
     * Perform actual DNS lookup
     */
    private function lookup($domain, $type) {
        $records = [];
        $ttl = $this->config->get('dns_ttl');
        
        // Try using native PHP DNS first
        $nativeResult = @dns_get_record($domain, $this->getDnsType($type));
        
        if ($nativeResult !== false && !empty($nativeResult)) {
            foreach ($nativeResult as $record) {
                $records[] = $this->normalizeRecord($record);
            }
            return $records;
        }
        
        // Fallback to using nameservers directly (more reliable but slower)
        return $this->lookupWithNameservers($domain, $type);
    }
    
    /**
     * Manual DNS lookup using specific nameservers
     */
    private function lookupWithNameservers($domain, $type) {
        $nameservers = $this->config->get('dns_nameservers');
        $records = [];
        
        foreach ($nameservers as $ns) {
            // Use dig command if available
            $output = shell_exec("dig @{$ns} {$domain} {$type} +short 2>/dev/null");
            
            if ($output) {
                $lines = array_filter(explode("\n", $output));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (!empty($line) && strpos($line, ';') !== 0) {
                        $records[] = [
                            'type' => $type,
                            'host' => $domain,
                            'address' => $line,
                            'ttl' => 300
                        ];
                    }
                }
                if (!empty($records)) {
                    break;
                }
            }
        }
        
        return $records;
    }
    
    /**
     * Convert DNS record to consistent format
     */
    private function normalizeRecord($record) {
        $normalized = [
            'host' => $record['host'] ?? null,
            'type' => $record['type'] ?? null,
            'ttl' => $record['ttl'] ?? 300
        ];
        
        // Extract address based on record type
        if (isset($record['ip'])) {
            $normalized['address'] = $record['ip'];
        } elseif (isset($record['ipv6'])) {
            $normalized['address'] = $record['ipv6'];
        } elseif (isset($record['target'])) {
            $normalized['address'] = $record['target'];
        } elseif (isset($record['exchange'])) {
            $normalized['address'] = $record['exchange'];
        } else {
            $normalized['address'] = $record['address'] ?? null;
        }
        
        return $normalized;
    }
    
    /**
     * Map string type to PHP DNS constant
     */
    private function getDnsType($type) {
        $types = [
            'A' => DNS_A,
            'AAAA' => DNS_AAAA,
            'MX' => DNS_MX,
            'CNAME' => DNS_CNAME,
            'NS' => DNS_NS,
            'PTR' => DNS_PTR,
            'SOA' => DNS_SOA,
            'TXT' => DNS_TXT
        ];
        return $types[$type] ?? DNS_A;
    }
    
    /**
     * Bulk resolve multiple domains
     */
    public function resolveBulk($domains) {
        $results = [];
        foreach ($domains as $domain) {
            $results[$domain] = $this->resolve($domain);
        }
        return $results;
    }
    
    /**
     * Clear DNS cache
     */
    public function clearCache() {
        $files = glob($this->cache->getCacheDir() . '/dns_*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
        return true;
    }
}