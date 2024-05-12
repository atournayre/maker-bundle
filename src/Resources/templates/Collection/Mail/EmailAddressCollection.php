<?php
declare(strict_types=1);

namespace App\Collection\Mail;

use Atournayre\Collection\TypedCollection;
use Atournayre\Types\EmailAddress;

final class EmailAddressCollection extends TypedCollection
{
	protected static string $type = EmailAddress::class;

    public static function fromArray(array $emails): self
    {
        $map = self::createAsList($emails)
            ->toMap()
            ->map(static fn(string $email): EmailAddress => EmailAddress::fromString($email));

        return self::createAsList($map->toArray());
    }
}
