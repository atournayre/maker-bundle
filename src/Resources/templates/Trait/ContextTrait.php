<?php
declare(strict_types=1);

namespace App\Trait;

use App\Contracts\VO\ContextInterface;
use App\VO\Null\NullContext;

trait ContextTrait
{
    private ?ContextInterface $context = null;

    /**
     * @throws \Exception
     */
    public function getContext(): ContextInterface
    {
        return $this->context ?? NullContext::create();
    }

    public function withContext(ContextInterface $context): self
    {
        $new = clone $this;
        $new->context = $context;

        return $new;
    }

    public function hasContext(): bool
    {
        return null !== $this->context
            && !$this->context instanceof NullContext;
    }
}
