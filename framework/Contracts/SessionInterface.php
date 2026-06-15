<?php
namespace Framework\Contracts;

interface SessionInterface {
    public function start(): void;
    public function set(string $key, $value): void;
    public function get(string $key, $default = null);
    public function has(string $key): bool;
    public function forget(string $key): void;
    public function all(): array;
    public function flash(string $key, $value): void;
    public function getFlash(string $key, $default = null);
    public function regenerate(bool $deleteOld = true): void;
    public function destroy(): void;
}