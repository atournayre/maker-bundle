<?php
declare(strict_types=1);

namespace App\Trait\Collection;

use App\Collection\Mail\EmailAddressCollection;
use Atournayre\Types\EmailAddress;

trait CreateEmailAddressCollectionTrait
{
    protected function createEmailCollection(array $emails): EmailAddressCollection
    {
        $emailCollection = EmailAddressCollection::createAsList($emails)
            ->toMap()
            ->map(fn(string $email): EmailAddress => EmailAddress::fromString($email));

        return EmailAddressCollection::createAsList($emailCollection->toArray());
    }

}
