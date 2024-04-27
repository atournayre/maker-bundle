<?php
declare(strict_types=1);

namespace App\Configuration;

use App\Contracts\Mail\MailerConfigurationInterface;
use App\Trait\Mail\MailerConfigurationTrait;
use App\Type\Primitive\IntegerType;
use Atournayre\Types\EmailAddress;

final class MailerConfiguration implements MailerConfigurationInterface
{
    use MailerConfigurationTrait;

    public function __construct(
        // Add autowire
        ?string $fromAddress = null,
        // Add autowire
        ?string $fromName = null,
        // Add autowire
        ?string $replyToAddress = null,
        // Add autowire
        ?string $replyToName = null,
        // Add autowire
        ?int $attachmentsMaxSize = null
    )
    {
        $this->fromAddress = $fromAddress ? EmailAddress::fromString($fromAddress) : null;
        $this->fromName = $fromName;
        $this->replyToAddress = $replyToAddress ? EmailAddress::fromString($replyToAddress) : null;
        $this->replyToName = $replyToName;
        $this->attachmentsMaxSize = $attachmentsMaxSize ? IntegerType::fromInt($attachmentsMaxSize) : null;
    }
}
