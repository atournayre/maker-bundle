<?php
declare(strict_types=1);

namespace App\Contracts\Mail;

interface MailerInterface
{
    /**
     * @throws \Throwable
     */
    public function send($message, $envelope = null): void;
}
