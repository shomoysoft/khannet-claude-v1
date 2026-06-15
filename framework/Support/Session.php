<?php
namespace Framework\Support;

class Session {
    
    private static $started = false;
    
    /**
     * Start session with configuration
     */
    public static function start(array $config = []) {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        
        // Apply configuration
        if (!empty($config)) {
            self::configure($config);
        }
        
        session_start();
        self::$started = true;
        
        // Clean up old flash messages
        self::ageFlashData();
    }
    
    /**
     * Configure session settings
     */
    private static function configure(array $config) {
        // Set session name
        if (isset($config['name'])) {
            session_name($config['name']);
        }
        
        // Set save path
        if (!empty($config['save_path'])) {
            session_save_path($config['save_path']);
        }
        
        // Set garbage collection lifetime
        if (isset($config['lifetime'])) {
            ini_set('session.gc_maxlifetime', $config['lifetime']);
        }
        
        // Set cookie parameters
        session_set_cookie_params([
            'lifetime' => $config['lifetime'] ?? 0,
            'path' => $config['path'] ?? '/',
            'domain' => $config['domain'] ?? '',
            'secure' => $config['secure'] ?? false,
            'httponly' => $config['httponly'] ?? true,
            'samesite' => $config['samesite'] ?? 'Lax'
        ]);
    }
    
    /**
     * Set a session value
     */
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get a session value
     */
    public static function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Check if session has a key
     */
    public static function has($key) {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Remove a session key
     */
    public static function forget($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Get all session data
     */
    public static function all() {
        return $_SESSION;
    }
    
    /**
     * Flash data (available for next request only)
     */
    public static function flash($key, $value) {
        $_SESSION['_flash']['new'][$key] = $value;
    }
    
    /**
     * Get flash data
     */
    public static function getFlash($key, $default = null) {
        // Check new flash first
        if (isset($_SESSION['_flash']['new'][$key])) {
            return $_SESSION['_flash']['new'][$key];
        }
        
        // Check old flash
        if (isset($_SESSION['_flash']['old'][$key])) {
            return $_SESSION['_flash']['old'][$key];
        }
        
        return $default;
    }
    
    /**
     * Keep flash data for another request
     */
    public static function reflash() {
        if (isset($_SESSION['_flash']['old'])) {
            $_SESSION['_flash']['new'] = array_merge(
                $_SESSION['_flash']['new'] ?? [],
                $_SESSION['_flash']['old']
            );
        }
    }
    
    /**
     * Age flash data (move new to old, remove old)
     */
    private static function ageFlashData() {
        // Remove old flash data
        if (isset($_SESSION['_flash']['old'])) {
            unset($_SESSION['_flash']['old']);
        }
        
        // Move new to old
        if (isset($_SESSION['_flash']['new'])) {
            $_SESSION['_flash']['old'] = $_SESSION['_flash']['new'];
            unset($_SESSION['_flash']['new']);
        }
    }
    
    /**
     * Regenerate session ID (security)
     */
    public static function regenerate($deleteOld = true) {
        session_regenerate_id($deleteOld);
    }
    
    /**
     * Destroy session completely
     */
    public static function destroy() {
        $_SESSION = [];
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
        self::$started = false;
    }
    
    /**
     * Generate and store CSRF token
     */
    public static function csrfToken() {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCsrf($token) {
        if (!isset($_SESSION['_csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'], $token);
    }
    
    /**
     * Store old input data
     */
    public static function flashInput(array $data) {
        self::flash('_old_input', $data);
    }
    
    /**
     * Get old input value
     */
    public static function old($key, $default = '') {
        $oldInput = self::getFlash('_old_input', []);
        return $oldInput[$key] ?? $default;
    }
}