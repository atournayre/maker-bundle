<?php
declare(strict_types=1);

namespace App\Service\Mail;

use App\Contracts\Mail\MailerConfigurationInterface;
use App\Contracts\Mail\SendMailInterface;

final class MailService
{
    public function __construct(
        private readonly SendMailInterface            $mailer,
        private readonly MailerConfigurationInterface $configuration,
    )
    {
    }

    /**
     * @throws \Throwable
     */
    public function send($message, $envelope = null): void
    {
        // TODO Décorer le message
        // TODO ::fromMessageWithConfiguration($message, $configuration);
        // TODO withConfiguration($configuration);

        $this->mailer->send($message, $envelope);
    }

    public function getConfiguration(): MailerConfigurationInterface
    {
        return $this->configuration;
    }
}
