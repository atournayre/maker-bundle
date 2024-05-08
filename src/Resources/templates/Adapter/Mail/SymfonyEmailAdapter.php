<?php
declare(strict_types=1);

namespace App\Adapter\Mail;

use App\Collection\Mail\EmailAddressCollection;
use App\VO\Mail\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as SymfonyEmail;
use Webmozart\Assert\Assert;

class SymfonyEmailAdapter
{
    /**
     * @throws \InvalidArgumentException
     */
    public static function fromMessage(Email $email): SymfonyEmail
    {
        Assert::true($email->isValid(), 'Email is not valid.');

        $from = $email->from()->toString();

        $tos = self::collectionToAddresses($email->to());
        $ccs = self::collectionToAddresses($email->cc());
        $bccs = self::collectionToAddresses($email->bcc());
        $replyTos = self::collectionToAddresses($email->replyTo());

        $symfonyEmail = new SymfonyEmail();
        $symfonyEmail->from($from);
        $symfonyEmail->subject($email->subject());
        $symfonyEmail->text($email->text());
        $symfonyEmail->html($email->html());
        $symfonyEmail->to(...$tos);
        $symfonyEmail->cc(...$ccs);
        $symfonyEmail->bcc(...$bccs);
        $symfonyEmail->replyTo(...$replyTos);

        foreach ($email->attachments() as $attachment) {
            $symfonyEmail->attachFromPath($attachment->getPathname());
        }

        $headers = $symfonyEmail->getHeaders();
        foreach ($email->tags()->values() as $tagName => $tagValue) {
            $headers->addTextHeader($tagName, $tagValue);
        }

        return $symfonyEmail;
    }

    /**
     * @return array|Address[]
     */
    private static function collectionToAddresses(EmailAddressCollection $emailAddressCollection): array
    {
        if ($emailAddressCollection->hasNoElement()) {
            return [];
        }

        return $emailAddressCollection
            ->toMap()
            ->map(fn($email) => new Address($email->toString()))
            ->toArray();
    }
}
