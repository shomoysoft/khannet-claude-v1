<?php

namespace App\Mail;

use App\Contracts\MailerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerMailer implements MailerInterface
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure()
    {
        // $this->mailer->isSMTP();
        // $this->mailer->Host = env('MAIL_HOST');
        // $this->mailer->SMTPAuth = true;
        // $this->mailer->Username = env('MAIL_USERNAME');
        // $this->mailer->Password = env('MAIL_PASSWORD');
        // $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        // $this->mailer->Port = env('MAIL_PORT', 587);
        // $this->mailer->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));

        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.sendgrid.net';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'apikey'; // literally the word 'apikey'
        $this->mailer->Password = env('SENDGRID_API_KEY');
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = 587;
        $this->mailer->setFrom('jubaer@ibos.io', 'Duane Cook');
    }

    public function send($to, $subject, $body, $isHtml = true): bool
    {
        try {
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML($isHtml);
            $this->mailer->Body = $body;

            return $this->mailer->send();
        } catch (Exception $e) {
            return false;
        }
    }

    public function renderView($view, $data = []): string
    {
        extract($data);
        ob_start();
        include ROOT_PATH . "views/{$view}.php";
        return ob_get_clean();
    }
}
