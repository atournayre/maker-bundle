<?php
declare(strict_types=1);

namespace App\Configuration;

use App\Collection\Mail\EmailAddressCollection;
use App\Type\Primitive\IntegerType;
use Atournayre\Types\EmailAddress;

final class MailerConfiguration
{
    private ?EmailAddress $fromAddress = null;
    private ?string $fromName = null;
    private ?EmailAddress $replyToAddress = null;
    private ?string $replyToName = null;
    private ?IntegerType $attachmentsMaxSize = null;

    public function fromAddress(): ?EmailAddress
    {
        return $this->fromAddress;
    }

    public function fromName(): ?string
    {
        return $this->fromName;
    }

    public function replyToAddresses(): ?EmailAddressCollection
    {
        return EmailAddressCollection::createAsList(array_filter([$this->replyToAddress]));
    }

    public function replyToName(): ?string
    {
        return $this->replyToName;
    }

    public function attachmentsMaxSize(): ?IntegerType
    {
        return $this->attachmentsMaxSize;
    }

    public function withFromAddress(?EmailAddress $fromAddress): self
    {
        $clone = clone $this;
        $clone->fromAddress = $fromAddress;
        return $clone;
    }

    public function withFromName(?string $fromName): self
    {
        $clone = clone $this;
        $clone->fromName = $fromName;
        return $clone;
    }

    public function withReplyToAddress(?EmailAddress $replyToAddress): self
    {
        $clone = clone $this;
        $clone->replyToAddress = $replyToAddress;
        return $clone;
    }

    public function withReplyToName(?string $replyToName): self
    {
        $clone = clone $this;
        $clone->replyToName = $replyToName;
        return $clone;
    }

    public function withAttachmentsMaxSize(?IntegerType $attachmentsMaxSize): self
    {
        $clone = clone $this;
        $clone->attachmentsMaxSize = $attachmentsMaxSize;
        return $clone;
    }

    public function setFromAddress(?string $fromAddress): void
    {
        $this->fromAddress = $fromAddress ? EmailAddress::fromString($fromAddress) : null;
    }

    public function setFromName(?string $fromName): void
    {
        $this->fromName = $fromName;
    }

    public function setReplyToAddress(?string $replyToAddress): void
    {
        $this->replyToAddress = $replyToAddress ? EmailAddress::fromString($replyToAddress) : null;
    }

    public function setReplyToName(?string $replyToName): void
    {
        $this->replyToName = $replyToName;
    }

    public function setAttachmentsMaxSize(?int $attachmentsMaxSize): void
    {
        $this->attachmentsMaxSize = $attachmentsMaxSize ? IntegerType::fromInt($attachmentsMaxSize) : null;
    }
}
