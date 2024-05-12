<?php
declare(strict_types=1);

namespace App\Contracts\Mail;

interface SendMailInterface
{
    /**
     * @throws \Throwable
     */
    public function send($message, $envelope = null): void;
}
