<?php

namespace App\Mail;

use App\Contracts\MailerInterface;
use SendGrid;
use SendGrid\Mail\Mail;

class SendGridMailer implements MailerInterface
{
    private SendGrid $sendgrid;

    public function __construct()
    {
        $this->sendgrid = new SendGrid(getenv('SENDGRID_API_KEY'));
    }

    public function send(string $to, string $subject, string $body, bool $isHtml = true): bool
    {
        $email = new Mail();
        $email->setFrom(getenv('MAIL_FROM_ADDRESS'), getenv('MAIL_FROM_NAME'));
        $email->setSubject($subject);
        $email->addTo($to);

        $email->addContent($isHtml ? "text/html" : "text/plain", $body);

        try {
            $response = $this->sendgrid->send($email);
            return $response->statusCode() >= 200 && $response->statusCode() < 300;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function renderView(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        include ROOT_PATH . "views/{$view}.php";
        return ob_get_clean();
    }
}
