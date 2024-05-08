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
    private ?EmailAddressCollection $to = null;
    private ?EmailAddressCollection $cc = null;
    private ?EmailAddressCollection $bcc = null;
    private ?EmailAddressCollection $replyTo = null;
    private ?SplFileInfoCollection $attachments = null;
    private ?TagCollection $tags = null;
    private ?string $text = null;
    private ?string $html = null;

    private function __construct(
        private string $subject,
        private EmailAddress $from,
    ) {
    }

    public static function create(string $subject, EmailAddress $from): static
    {
        Assert::notEmpty($subject, 'Email subject cannot be empty.');

        return new static($subject, $from);
    }

    public static function fromConfiguration(
        MailerConfiguration $configuration,
        string $subject,
        EmailAddress $from,
    ): static
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

        if (!$this->to instanceof EmailAddressCollection || $this->to->hasNoElement()) {
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

    public function from(): EmailAddress
    {
        return $this->from;
    }

    public function to(): EmailAddressCollection
    {
        return $this->to;
    }

    public function cc(): EmailAddressCollection
    {
        return $this->cc ?? EmailAddressCollection::createAsList([]);
    }

    public function bcc(): EmailAddressCollection
    {
        return $this->bcc ?? EmailAddressCollection::createAsList([]);
    }

    public function replyTo(): EmailAddressCollection
    {
        return $this->replyTo ?? EmailAddressCollection::createAsList([]);
    }

    public function attachments(): SplFileInfoCollection
    {
        return $this->attachments ?? SplFileInfoCollection::createAsList([]);
    }

    public function text(): ?string
    {
        return $this->text;
    }

    public function html(): ?string
    {
        return $this->html;
    }

    public function tags(): TagCollection
    {
        return $this->tags ?? TagCollection::createAsMap([]);
    }

    public function withSubject(string $subject): static
    {
        $clone = clone $this;
        $clone->subject = $subject;
        return $clone;
    }

    public function withFrom(EmailAddress $from): static
    {
        $clone = clone $this;
        $clone->from = $from;
        return $clone;
    }

    public function withTo(EmailAddressCollection $to): static
    {
        $clone = clone $this;
        $clone->to = $to;
        return $clone;
    }

    public function withCc(EmailAddressCollection $cc): static
    {
        $clone = clone $this;
        $clone->cc = $cc;
        return $clone;
    }

    public function withBcc(EmailAddressCollection $bcc): static
    {
        $clone = clone $this;
        $clone->bcc = $bcc;
        return $clone;
    }

    public function withReplyTo(EmailAddressCollection $replyTo): static
    {
        $clone = clone $this;
        $clone->replyTo = $replyTo;
        return $clone;
    }

    public function withAttachments(SplFileInfoCollection $attachments): static
    {
        $clone = clone $this;
        $clone->attachments = $attachments;
        return $clone;
    }

    public function withText(string $text): static
    {
        Assert::notEmpty($text, 'Email text cannot be empty.');

        $clone = clone $this;
        $clone->text = $text;
        return $clone;
    }

    public function withHtml(string $html): static
    {
        Assert::notEmpty($html, 'Email html cannot be empty.');

        $clone = clone $this;
        $clone->html = $html;
        return $clone;
    }

    public function withTags(TagCollection $tags): static
    {
        $clone = clone $this;
        $clone->tags = $tags;
        return $clone;
    }

    public function withConfiguration(MailerConfiguration $configuration): static
    {
        $clone = clone $this;
        $clone->configuration = $configuration;
        return $clone;
    }
}
