<?php
declare(strict_types=1);

namespace App\Service\Mail;

use App\Configuration\MailerConfiguration;
use App\Contracts\Mail\SendMailInterface;

final class MailService
{
    public function __construct(
        private readonly SendMailInterface $sendMail,
        private readonly MailerConfiguration $mailerConfiguration,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function send($message, $envelope = null): void
    {
        $this->sendMail->send($message, $envelope);
    }

    public function getConfiguration(): MailerConfiguration
    {
        return $this->mailerConfiguration;
    }
}
