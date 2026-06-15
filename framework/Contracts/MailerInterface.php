<?php
namespace Framework\Contracts;
interface MailerInterface
{

    /**
     * Send an email.
     *
     * @param string $to
     * @param string $subject
     * @param string $body
     * @param bool   $isHtml
     * @return bool
     */
    public function send(string $to, string $subject, string $body, bool $isHtml = true): bool;


    /**
     * Render a view.
     *
     * @param string $view
     * @param array  $data
     * @return string
     */
    public function renderView(string $view, array $data = []): string;
}
