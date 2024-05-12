<?php
declare(strict_types=1);

namespace App\VO\Email;

use App\Contracts\Null\NullableInterface;
use App\Trait\IsTrait;
use App\Trait\NotNullableTrait;
use Webmozart\Assert\Assert;
use function Symfony\Component\String\u;

/**
 * ONLY
 * - primitive types : string, int, float, bool, array, \DateTimeInterface or VO
 *
 * MUST
 * - check validity of the data on creation
 * - be immutable
 * - be final
 *
 * SHOULD
 * - have a named constructor
 * - have withers
 * - have logic
 *
 * MUST NOT
 * - have setters
 *
 * @object-type VO
 */
final class EmailBody implements NullableInterface
{
    use NotNullableTrait;
    use IsTrait;
    private bool $isHtml = true;

    private function __construct(private string $body)
    {
    }

    public static function create(string $body): self
    {
        if (u($body)->containsAny('<body')) {
            return self::fromHtml($body);
        }

        return self::fromText($body);
    }

    private static function fromHtml(string $body): self
    {
        $unicodeBody = u($body)
            ->match('/<body[^>]*>(.*?)<\/body>/is');

        return (new self($unicodeBody[1] ?? ''))
            ->withIsHtml(true);
    }

    private function withIsHtml(bool $isHtml): self
    {
        $clone = clone $this;
        $clone->isHtml = $isHtml;
        return $clone;
    }

    private static function fromText(string $body): self
    {
        return (new self($body))
            ->withIsHtml(false);
    }

    public function isHtml(): bool
    {
        return $this->isHtml;
    }

    public function isEmpty(): bool
    {
        return $this->body === '' || $this->body === '0';
    }


    public function isNotEmpty(): bool
    {
        return $this->body !== '' && $this->body !== '0';
    }

    public function length(): int
    {
        $content = $this->isHtml
            ? $this->html()
            : $this->body;

        return u($content)->length();
    }

    public function html(): string
    {
        Assert::true($this->isHtml, 'This EmailBody is not HTML');
        return $this->body;
    }

    public function text(): string
    {
        return $this->isHtml
            ? strip_tags($this->body)
            : $this->body;
    }
}
