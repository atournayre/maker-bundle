<?php
declare(strict_types=1);

namespace App\Trait;

use App\VO\Context;

trait ContextTrait
{
    private Context $context;

    public function getContext(): Context
    {
        return $this->context;
    }

    public function withContext(Context $context): self
    {
        $new = clone $this;
        $new->context = $context;

        return $new;
    }
}
