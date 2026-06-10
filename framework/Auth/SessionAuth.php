<?php

namespace App\Auth;

use App\Contracts\AuthInterface;
use App\Contracts\SessionInterface;
use App\Models\User;

class SessionAuth implements AuthInterface
{

    private $session;

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
        return $this->session->get('user_id');
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

        $this->session->set('user_id', $user->id);
        $this->session->set('user_name', $user->name);
        $this->session->set('user_email', $user->email);

        return true;
    }

    public function logout(): void
    {
        $this->session->destroy();
    }

    public function attempt(array $credentials, bool $remember = false): bool
    {
        if(!$field = $this->check_username_or_email($credentials['username'])){
            return false;
        }

        $username = $credentials['username'] ?? null;
        $password = $credentials['password'] ?? null;

        if (!$username || !$password) {
            return false;
        }

        $user = User::first('username', $username);

        if (!$user || !password_verify($password, $user->password)) {
            return false;
        }

        return $this->login($user, $remember);
    }

    /**
     * Check if a string is a valid email
     */
    public function is_valid_email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Check if a string is a valid username
     * Rules: 3-20 chars, letters, numbers, underscore
     */
    public function is_valid_username($value)
    {
        return preg_match('/^[A-Za-z0-9_]{3,20}$/', $value);
    }

    /**
     * Check whether input is username or email
     * Returns 'email', 'username', or false if invalid
     */
    public function check_username_or_email($value)
    {
        if ($this->is_valid_email($value)) {
            return 'email';
        } elseif ($this->is_valid_username($value)) {
            return 'username';
        } else {
            return false;
        }
    }
}
