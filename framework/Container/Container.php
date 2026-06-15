<?php
namespace Framework\Container;

class Container {
    
    private static $bindings = [];
    private static $instances = [];
    
    public static function bind(string $abstract, $concrete = null, bool $singleton = false) {
        if ($concrete === null) {
            $concrete = $abstract;
        }
        
        self::$bindings[$abstract] = [
            'concrete' => $concrete,
            'singleton' => $singleton
        ];
    }
    
    public static function singleton(string $abstract, $concrete = null) {
        self::bind($abstract, $concrete, true);
    }
    
    public static function make(string $abstract) {
        if (isset(self::$instances[$abstract])) {
            return self::$instances[$abstract];
        }
        
        if (!isset(self::$bindings[$abstract])) {
            throw new \Exception("No binding found for: $abstract");
        }
        
        $binding = self::$bindings[$abstract];
        $concrete = $binding['concrete'];
        
        if (is_callable($concrete)) {
            $instance = $concrete(new static);
        } else {
            $instance = new $concrete();
        }
        
        if ($binding['singleton']) {
            self::$instances[$abstract] = $instance;
        }
        
        return $instance;
    }
}