<?php

namespace   App\Mail;

use App\Contracts\MailerInterface;

class WelcomeMail{
    private $data;
    private MailerInterface $mailer;
    public function __construct(array $data, MailerInterface $mailer)
    {
        $this->data = $data;
        $this->mailer = $mailer;
    }


    public function sendMail(): bool {
        $body = $this->mailer->renderView('emails/welcome-mail', [
            'username' => $this->data['username'],
            'password' => $this->data['password'],
            'loginUrl' => '/login.php',
        ]);

        return $this->mailer->send(
            $this->data['email'],
            $this->data['subject'],
            $body
        );
    }
}