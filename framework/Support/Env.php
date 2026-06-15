<?php
namespace Framework\Support;

class Env {
    
    private static $loaded = false;
    
    /**
     * Load environment variables from .env file
     * 
     * @param string $path Path to .env file
     * @return void
     */
    public static function load($path) {
        if (self::$loaded) {
            return;
        }
        
        if (!file_exists($path)) {
            throw new \Exception(".env file not found at: {$path}");
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Skip empty lines
            if (empty(trim($line))) {
                continue;
            }
            
            // Parse line
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes from value
                $value = self::stripQuotes($value);
                
                // Set environment variable
                if (!getenv($key)) {
                    putenv("{$key}={$value}");
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }
        
        self::$loaded = true;
    }
    
    /**
     * Strip quotes from value
     * 
     * @param string $value
     * @return string
     */
    private static function stripQuotes($value) {
        // Remove double quotes
        if (strlen($value) > 1 && $value[0] === '"' && $value[strlen($value) - 1] === '"') {
            return substr($value, 1, -1);
        }
        
        // Remove single quotes
        if (strlen($value) > 1 && $value[0] === "'" && $value[strlen($value) - 1] === "'") {
            return substr($value, 1, -1);
        }
        
        return $value;
    }
    
    /**
     * Get environment variable
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null) {
        $value = getenv($key);
        
        if ($value === false) {
            return $default;
        }
        
        return self::parseValue($value);
    }
    
    /**
     * Parse environment value (convert strings to proper types)
     * 
     * @param string $value
     * @return mixed
     */
    private static function parseValue($value) {
        $lower = strtolower($value);
        
        switch ($lower) {
            case 'true':
            case '(true)':
                return true;
            
            case 'false':
            case '(false)':
                return false;
            
            case 'null':
            case '(null)':
                return null;
            
            case 'empty':
            case '(empty)':
                return '';
        }
        
        // Return as-is
        return $value;
    }
}
