<?php
declare(strict_types=1);

namespace App\Adapter\Mail;

use App\Collection\Mail\EmailAddressCollection;
use App\VO\Mail\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as SymfonyEmail;
use Webmozart\Assert\Assert;

final class SymfonyEmailAdapter
{
    /**
     * @param Email $email
     * @return SymfonyEmail
     * @throws \InvalidArgumentException
     */
    public static function fromMessage(Email $email): SymfonyEmail
    {
        Assert::true($email->isValid(), 'Email is not valid.');

        $from = $email->from()->toString();
        /** @var Address $tos */
        $tos = self::collectionToAddresses($email->to());
        /** @var Address $replyTos */
        $replyTos = self::collectionToAddresses($email->replyTo());

        $symfonyEmail = new SymfonyEmail();
        $symfonyEmail->from($from);

        foreach ($tos as $to) {
            $symfonyEmail->addTo($to);
        }
        foreach ($email->cc() as $cc) {
            $symfonyEmail->addCc(new Address($cc->toString()));
        }
        foreach ($email->bcc() as $bcc) {
            $symfonyEmail->addBcc(new Address($bcc->toString()));
        }
        foreach ($replyTos as $replyTo) {
            $symfonyEmail->addReplyTo($replyTo);
        }
        foreach ($email->attachments() as $attachment) {
            $symfonyEmail->attachFromPath($attachment->getPathname());
        }

        $headers = $symfonyEmail->getHeaders();
        foreach ($email->tags()->values() as $tagName => $tagValue) {
            $headers->addTextHeader($tagName, $tagValue);
        }

        $symfonyEmail->subject($email->subject());
        $symfonyEmail->text($email->text());
        $symfonyEmail->html($email->html());

        return $symfonyEmail;
    }

    /**
     * @param EmailAddressCollection $emailAddressCollection
     * @return array|Address[]
     */
    private static function collectionToAddresses(EmailAddressCollection $emailAddressCollection): array
    {
        return $emailAddressCollection
            ->toMap()
            ->map(fn($email) => new Address($email->toString()))
            ->toArray();
    }
}
