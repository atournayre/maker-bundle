<?php
declare(strict_types=1);

namespace App\Adapter\Mail;

use App\VO\Mail\Email;
use App\VO\Mail\TemplatedEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail as SymfonyTemplatedEmail;

class SymfonyTemplatedEmailAdapter extends SymfonyEmailAdapter
{
    public static function fromMessage(TemplatedEmail|Email $email): SymfonyTemplatedEmail
    {
        $symfonyEmail = parent::fromMessage($email);

        $templatedEmail = new SymfonyTemplatedEmail();
        $templatedEmail->subject($symfonyEmail->getSubject());
        $templatedEmail->from(...$symfonyEmail->getFrom());
        $templatedEmail->to(...$symfonyEmail->getTo());
        $templatedEmail->cc(...$symfonyEmail->getCc());
        $templatedEmail->bcc(...$symfonyEmail->getBcc());
        $templatedEmail->replyTo(...$symfonyEmail->getReplyTo());

        foreach ($email->attachments() as $attachment) {
            $templatedEmail->attachFromPath($attachment->getPathname());
        }

        $headers = $templatedEmail->getHeaders();
        foreach ($email->tags()->values() as $tagName => $tagValue) {
            $headers->addTextHeader($tagName, $tagValue);
        }

        if ($email instanceof TemplatedEmail) {
            $templatedEmail->htmlTemplate($email->htmlTemplate());
            $templatedEmail->textTemplate($email->textTemplate());
            $templatedEmail->context($email->context());
        }

        return $templatedEmail;
    }
}
