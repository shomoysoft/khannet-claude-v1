<?php

namespace Framework\Auth;

use Framework\Contracts\AuthInterface;
use Framework\Contracts\SessionInterface;
use App\Models\User;

class SessionAuth implements AuthInterface
{
    private SessionInterface $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function check(): bool
    {
        return $this->session->has('user_id');
    }

    public function guest(): bool
    {
        return !$this->check();
    }

    public function id(): ?int
    {
        $id = $this->session->get('user_id');
        return $id !== null ? (int) $id : null;
    }

    public function user(): ?object
    {
        if (!$this->check()) {
            return null;
        }
        return User::find($this->id());
    }

    public function login(object $user, bool $remember = false): bool
    {
        $this->session->regenerate();
        $this->session->set('user_id',   $user->id);
        $this->session->set('user_role', $user->role);
        $this->session->set('user_name', $user->name);
        return true;
    }

    public function logout(): void
    {
        $this->session->destroy();
    }

    public function attempt(array $credentials, bool $remember = false): bool
    {
        $username = trim($credentials['username'] ?? '');
        $password = $credentials['password'] ?? '';

        if ($username === '' || $password === '') {
            return false;
        }

        $user = User::findByUsername($username);

        if (!$user || !(bool) $user->is_active || !password_verify($password, $user->password)) {
            return false;
        }

        return $this->login($user, $remember);
    }
}
