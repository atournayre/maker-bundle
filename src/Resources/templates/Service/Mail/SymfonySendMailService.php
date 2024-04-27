<?php
declare(strict_types=1);

namespace App\Service\Mail;

use App\Contracts\Mail\SendMailInterface;
use Symfony\Component\Mailer\MailerInterface;

final class SymfonySendMailService implements SendMailInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
    )
    {
    }

    /**
     * @throws \Throwable
     */
    // TODO $message doit $etre typé
    public function send($message, $envelope = null): void
    {
        // TODO Convertir le message en format compatible avec Symfony : SymfonyTemplatedMailAdapter
        // TODO Ajouter un pattern Strategy pour gérer les différents types de messages

        $this->mailer->send($message, $envelope);
    }
}
