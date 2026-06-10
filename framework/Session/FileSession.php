<?php
namespace App\Session;

use App\Contracts\SessionInterface;

class FileSession implements SessionInterface {
    
    private $started = false;
    private $config;
    
    public function __construct(array $config = []) {
        $this->config = $config;
    }
    
    public function start(): void {
        if ($this->started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        
        $this->configure();
        session_start();
        $this->started = true;
        $this->ageFlashData();
    }
    
    private function configure(): void {
        if (isset($this->config['name'])) {
            session_name($this->config['name']);
        }
        
        if (!empty($this->config['save_path'])) {
            session_save_path($this->config['save_path']);
        }
        
        if (isset($this->config['lifetime'])) {
            ini_set('session.gc_maxlifetime', $this->config['lifetime']);
        }
        
        session_set_cookie_params([
            'lifetime' => $this->config['lifetime'] ?? 0,
            'path' => $this->config['path'] ?? '/',
            'domain' => $this->config['domain'] ?? '',
            'secure' => $this->config['secure'] ?? false,
            'httponly' => $this->config['httponly'] ?? true,
            'samesite' => $this->config['samesite'] ?? 'Lax'
        ]);
    }
    
    public function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }
    
    public function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public function has(string $key): bool {
        return isset($_SESSION[$key]);
    }
    
    public function forget(string $key): void {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    public function all(): array {
        return $_SESSION ?? [];
    }
    
    public function flash(string $key, $value): void {
        $_SESSION['_flash']['new'][$key] = $value;
    }
    
    public function getFlash(string $key, $default = null) {
        if (isset($_SESSION['_flash']['new'][$key])) {
            return $_SESSION['_flash']['new'][$key];
        }
        
        if (isset($_SESSION['_flash']['old'][$key])) {
            return $_SESSION['_flash']['old'][$key];
        }
        
        return $default;
    }
    
    private function ageFlashData(): void {
        if (isset($_SESSION['_flash']['old'])) {
            unset($_SESSION['_flash']['old']);
        }
        
        if (isset($_SESSION['_flash']['new'])) {
            $_SESSION['_flash']['old'] = $_SESSION['_flash']['new'];
            unset($_SESSION['_flash']['new']);
        }
    }
    
    public function regenerate(bool $deleteOld = true): void {
        session_regenerate_id($deleteOld);
    }
    
    public function destroy(): void {
        $_SESSION = [];
        
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
        $this->started = false;
    }
}