<?php
declare(strict_types=1);

namespace App\VO\Mail;

use App\Collection\Mail\EmailAddressCollection;
use App\Collection\SplFileInfoCollection;
use App\Collection\TagCollection;
use App\Configuration\MailerConfiguration;
use App\Contracts\Null\NullableInterface;
use App\Trait\IsTrait;
use App\Trait\NotNullableTrait;
use Atournayre\Types\EmailAddress;
use Webmozart\Assert\Assert;

/**
 * @object-type VO
 */
class Email implements NullableInterface
{
	use NotNullableTrait;
	use IsTrait;

    private ?MailerConfiguration $configuration = null;
    private array|EmailAddressCollection $to = [];
    private array|EmailAddressCollection $cc = [];
    private array|EmailAddressCollection $bcc = [];
    private array|EmailAddressCollection $replyTo = [];
    private array|SplFileInfoCollection $attachments = [];
    private array|TagCollection $tags = [];
    private ?string $text = null;
    private ?string $html = null;

	private function __construct(
		private string $subject,
        private string|EmailAddress $from,
	) {
	}

    public static function create(
        string $subject,
        string|EmailAddress $from,
    ): self
    {
        Assert::notEmpty($subject);
        if (is_string($from)) {
            $from = EmailAddress::fromString($from);
        }

        return new self($subject, $from);
    }

    public static function fromConfiguration(
        MailerConfiguration $configuration,
        string              $subject,
        string|EmailAddress $from
    ): self
    {
        return self::create($subject, $from)
            ->withFrom($configuration->fromAddress() ?? $from)
            ->withReplyTo($configuration->replyToAddresses())
            ->withConfiguration($configuration)
        ;
    }

    public function validate(): array
    {
        $errors = [];

        if ($this->to->hasNoElement()) {
            $errors['to'] = 'validation.email.to.empty';
        }

        return $errors;
    }

    public function isValid(): bool
    {
        return [] === $this->validate();
    }

    public function subject(): string
	{
		return $this->subject;
	}

	public function from(): string|EmailAddress
	{
        return is_string($this->from) ? EmailAddress::fromString($this->from) : $this->from;
	}

	public function to(): array|EmailAddressCollection
	{
		return is_array($this->to) ? EmailAddressCollection::createAsList($this->to) : $this->to;
	}


	public function cc(): array|EmailAddressCollection
	{
		return is_array($this->cc) ? EmailAddressCollection::createAsList($this->cc) : $this->cc;
	}

	public function bcc(): array|EmailAddressCollection
	{
		return is_array($this->bcc) ? EmailAddressCollection::createAsList($this->bcc) : $this->bcc;
	}

	public function replyTo(): array|EmailAddressCollection
	{
		return is_array($this->replyTo) ? EmailAddressCollection::createAsList($this->replyTo) : $this->replyTo;
	}

	public function attachments(): array|SplFileInfoCollection
	{
		return is_array($this->attachments) ? SplFileInfoCollection::createAsList($this->attachments) : $this->attachments;
	}

	public function text(): string
	{
		return $this->text;
	}

	public function html(): string
	{
		return $this->html;
	}

	public function tags(): array|TagCollection
	{
		return is_array($this->tags) ? TagCollection::createAsList($this->tags) : $this->tags;
	}

	public function withSubject(string $subject): self
	{
		$clone = clone $this;
		$clone->subject = $subject;
		return $clone;
	}

	public function withFrom(string|EmailAddress $from): self
	{
		$clone = clone $this;
		$clone->from = $from;
		return $clone;
	}

	public function withTo(array|EmailAddressCollection $to): self
	{
		$clone = clone $this;
		$clone->to = $to;
		return $clone;
	}

	public function withCc(array|EmailAddressCollection $cc): self
	{
		$clone = clone $this;
		$clone->cc = $cc;
		return $clone;
	}

	public function withBcc(array|EmailAddressCollection $bcc): self
	{
		$clone = clone $this;
		$clone->bcc = $bcc;
		return $clone;
	}

	public function withReplyTo(array|EmailAddressCollection $replyTo): self
	{
		$clone = clone $this;
		$clone->replyTo = $replyTo;
		return $clone;
	}

	public function withAttachments(array|SplFileInfoCollection $attachments): self
	{
		$clone = clone $this;
		$clone->attachments = $attachments;
		return $clone;
	}

	public function withText(string $text): self
	{
        Assert::notEmpty($text);

		$clone = clone $this;
		$clone->text = $text;
		return $clone;
	}


	public function withHtml(string $html): self
	{
        Assert::notEmpty($html);

		$clone = clone $this;
		$clone->html = $html;
		return $clone;
	}

	public function withTags(array|TagCollection $tags): self
	{
		$clone = clone $this;
		$clone->tags = $tags;
		return $clone;
	}

    public function withConfiguration(MailerConfiguration $configuration): self
    {
        $clone = clone $this;
        $clone->configuration = $configuration;
        return $clone;
    }
}
