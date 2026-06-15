<?php
namespace Framework\Security;

use Framework\Contracts\SessionInterface;

class Csrf {
    
    private $session;
    
    public function __construct(SessionInterface $session) {
        $this->session = $session;
    }
    
    public function token(): string {
        if (!$this->session->has('_csrf_token')) {
            $this->session->set('_csrf_token', bin2hex(random_bytes(32)));
        }
        return $this->session->get('_csrf_token');
    }
    
    public function verify(string $token): bool {
        if (!$this->session->has('_csrf_token')) {
            return false;
        }
        return hash_equals($this->session->get('_csrf_token'), $token);
    }
    
    public function field(): string {
        $token = $this->token();
        return "<input type='hidden' name='csrf_token' value='{$token}'>";
    }
}