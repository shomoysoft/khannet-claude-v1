<?php

namespace KhanNet\Controllers\Api;

use App\Database\DB;

class QuoteApiController
{
    public function submit(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        if (!empty($_POST['botcheck'])) {
            echo json_encode(['success' => true]);
            exit;
        }

        $name    = trim(strip_tags($_POST['name']    ?? ''));
        $mobile  = trim(strip_tags($_POST['mobile']  ?? ''));
        $service = trim(strip_tags($_POST['service'] ?? ''));
        $budget  = trim(strip_tags($_POST['budget']  ?? ''));
        $details = trim(strip_tags($_POST['details'] ?? ''));
        $ip      = substr(trim($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);

        if ($name === '' || $mobile === '') {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Name and mobile are required.']);
            exit;
        }

        try {
            $id = DB::table('shomoysoft_quotes')->insert(compact(
                'name', 'mobile', 'service', 'budget', 'details', 'ip'
            ));
            echo json_encode(['success' => true, 'id' => (int) $id]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Could not save your request. Please try WhatsApp.']);
        }
    }
}
