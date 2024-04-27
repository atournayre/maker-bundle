<?php
declare(strict_types=1);

namespace App\Trait\Mail;

use App\Type\Primitive\IntegerType;
use Atournayre\Types\EmailAddress;

trait MailerConfigurationTrait
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


	public function replyToAddress(): ?EmailAddress
	{
		return $this->replyToAddress;
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
}
