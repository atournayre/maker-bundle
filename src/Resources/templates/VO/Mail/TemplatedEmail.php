<?php
declare(strict_types=1);

namespace App\VO\Mail;

use Webmozart\Assert\Assert;

/**
 * @object-type VO
 */
class TemplatedEmail extends Email
{
    public ?string $htmlTemplate = null;
    public ?string $textTemplate = null;
    public array $context = [];

	public function htmlTemplate(): ?string
	{
		return $this->htmlTemplate;
	}

	public function textTemplate(): ?string
	{
		return $this->textTemplate;
	}

	public function context(): array
	{
		return $this->context;
	}

	public function withHtmlTemplate(string $htmlTemplate): self
	{
        Assert::stringNotEmpty($htmlTemplate, 'Html template is empty.');

		$clone = clone $this;
		$clone->htmlTemplate = $htmlTemplate;
		return $clone;
	}

	public function withTextTemplate(string $textTemplate): self
	{
        Assert::stringNotEmpty($textTemplate, 'Text template is empty.');

		$clone = clone $this;
		$clone->textTemplate = $textTemplate;
		return $clone;
	}

	public function withContext(array $context): self
	{
        Assert::minCount($context, 1, 'Context is used to render the template. It should not be empty.');

		$clone = clone $this;
		$clone->context = $context;
		return $clone;
	}
}
