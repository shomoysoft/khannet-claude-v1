<?php
namespace Framework\Contracts;

interface AuthInterface {
    public function check(): bool;
    public function guest(): bool;
    public function id(): ?int;
    public function user(): ?object;
    public function login(object $user, bool $remember = false): bool;
    public function logout(): void;
    public function attempt(array $credentials, bool $remember = false): bool;
}