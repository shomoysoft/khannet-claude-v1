<?php

namespace KhanNet\Controllers;

abstract class BaseController
{
    protected function requireAuth(): void
    {
        require_once APP . '/auth.php';
    }

    protected function view(string $name, array $data = []): void
    {
        require_once APP . '/Views/layout.php';
        extract($data);
        require APP . '/Views/' . $name . '.php';
    }
}
