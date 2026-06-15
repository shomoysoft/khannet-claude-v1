<?php

namespace Framework\Http;

class Response
{
    public function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        echo json_encode($data);
        exit;
    }

    public function success(mixed $data = null): never
    {
        $payload = ['success' => true];
        if ($data !== null) {
            $payload['data'] = $data;
        }
        $this->json($payload, 200);
    }

    public function error(string $message, int $status = 400): never
    {
        $this->json(['success' => false, 'message' => $message], $status);
    }
}
