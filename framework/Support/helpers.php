<?php

/**
 * Get session instance
 * 
 * @return \App\Contracts\SessionInterface
 */
if (!function_exists('session')) {
    function session() {
        return App\Container\Container::make(App\Contracts\SessionInterface::class);
    }
}

/**
 * Get auth instance
 * 
 * @return \App\Contracts\AuthInterface
 */
if (!function_exists('auth')) {
    function auth() {
        return App\Container\Container::make(App\Contracts\AuthInterface::class);
    }
}

/**
 * Get CSRF instance
 * 
 * @return \App\Security\Csrf
 */
if (!function_exists('csrf')) {
    function csrf() {
        return App\Container\Container::make(App\Security\Csrf::class);
    }
}

/**
 * Flash message - set or get
 * 
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
if (!function_exists('flash')) {
    function flash($key, $value = null) {
        if ($value === null) {
            return session()->getFlash($key);
        }
        session()->flash($key, $value);
    }
}

/**
 * Get old input value
 * 
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
if (!function_exists('old')) {
    function old($key, $default = '') {
        $oldInput = session()->getFlash('_old_input', []);
        return $oldInput[$key] ?? $default;
    }
}

/**
 * Get CSRF token
 * 
 * @return string
 */
if (!function_exists('csrf_token')) {
    function csrf_token() {
        return csrf()->token();
    }
}

/**
 * Generate CSRF hidden input field
 * 
 * @return string
 */
if (!function_exists('csrf_field')) {
    function csrf_field() {
        return csrf()->field();
    }
}

/**
 * Redirect to URL
 * 
 * @param string $url
 * @return void
 */
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

/**
 * Redirect back to previous page
 * 
 * @return void
 */
if (!function_exists('back')) {
    function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        redirect($referer);
    }
}

/**
 * Get current URL
 * 
 * @return string
 */
if (!function_exists('current_url')) {
    function current_url() {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }
}

/**
 * Check if request is POST
 * 
 * @return bool
 */
if (!function_exists('is_post')) {
    function is_post() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}

/**
 * Check if request is GET
 * 
 * @return bool
 */
if (!function_exists('is_get')) {
    function is_get() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}

/**
 * Get request input value
 * 
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
if (!function_exists('input')) {
    function input($key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
}

/**
 * Dump and die (for debugging)
 * 
 * @param mixed ...$vars
 * @return void
 */
if (!function_exists('dd')) {
    function dd(...$vars) {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        exit;
    }
}

/**
 * Simple dump (for debugging)
 * 
 * @param mixed ...$vars
 * @return void
 */
if (!function_exists('dump')) {
    function dump(...$vars) {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
    }
}

/**
 * Escape HTML
 * 
 * @param string $string
 * @return string
 */
if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Get environment variable
 * 
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
if (!function_exists('env')) {
    /**
     * Get environment variable
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env($key, $default = null) {
        return \App\Support\Env::get($key, $default);
    }
}

/**
 * Get config value
 * 
 * @param string $key (e.g., 'database.host')
 * @param mixed $default
 * @return mixed
 */
if (!function_exists('config')) {
    function config($key, $default = null) {
        static $config = [];
        
        // Parse key (e.g., 'database.host')
        $parts = explode('.', $key);
        $file = $parts[0];
        
        // Load config file if not loaded
        if (!isset($config[$file])) {
            $configPath = ROOT_PATH . "config/{$file}.php";
            if (file_exists($configPath)) {
                $config[$file] = require $configPath;
            } else {
                return $default;
            }
        }
        
        // Get nested value
        $value = $config[$file];
        for ($i = 1; $i < count($parts); $i++) {
            if (!isset($value[$parts[$i]])) {
                return $default;
            }
            $value = $value[$parts[$i]];
        }
        
        return $value;
    }
}

/**
 * Generate a URL
 * 
 * @param string $path
 * @return string
 */
if (!function_exists('url')) {
    function url($path = '') {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = ltrim($path, '/');
        
        return "{$protocol}://{$host}/{$path}";
    }
}

/**
 * Generate asset URL
 * 
 * @param string $path
 * @return string
 */
if (!function_exists('asset')) {
    function asset($path) {
        return url('assets/' . ltrim($path, '/'));
    }
}

/**
 * Check if user is authenticated
 * 
 * @return bool
 */
if (!function_exists('is_authenticated')) {
    function is_authenticated() {
        return auth()->check();
    }
}

/**
 * Check if user is guest
 * 
 * @return bool
 */
if (!function_exists('is_guest')) {
    function is_guest() {
        return auth()->guest();
    }
}

/**
 * Get current user
 * 
 * @return object|null
 */
if (!function_exists('user')) {
    function user() {
        return auth()->user();
    }
}

/**
 * Abort with HTTP status code
 * 
 * @param int $code
 * @param string $message
 * @return void
 */
if (!function_exists('abort')) {
    function abort($code = 404, $message = 'Not Found') {
        http_response_code($code);
        echo "<h1>{$code} - {$message}</h1>";
        exit;
    }
}

/**
 * Return JSON response
 * 
 * @param mixed $data
 * @param int $status
 * @return void
 */
if (!function_exists('json')) {
    function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
