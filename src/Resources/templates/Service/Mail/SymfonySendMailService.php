<?php
declare(strict_types=1);

namespace App\Service\Mail;

use App\Adapter\Mail\SymfonyEmailAdapter;
use App\Adapter\Mail\SymfonyTemplatedEmailAdapter;
use App\Contracts\Mail\SendMailInterface;
use App\VO\Mail\Email;
use App\VO\Mail\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;

final class SymfonySendMailService implements SendMailInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function send($message, $envelope = null): void
    {
        $message = $this->adaptMessage($message);

        $this->mailer->send($message, $envelope);
    }

    /**
     * @param $message
     * @return RawMessage
     * @throws \InvalidArgumentException
     */
    private function adaptMessage($message): RawMessage
    {
        return match (true) {
            $message instanceof TemplatedEmail => SymfonyTemplatedEmailAdapter::fromMessage($message),
            $message instanceof Email => SymfonyEmailAdapter::fromMessage($message),
            default => throw new \InvalidArgumentException('Invalid message type.'),
        };
    }
}
