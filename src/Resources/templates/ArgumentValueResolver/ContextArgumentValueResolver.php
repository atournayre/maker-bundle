<?php
declare(strict_types=1);

namespace App\ArgumentValueResolver;

use App\Contracts\ContextInterface;
use App\Factory\ContextFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ContextArgumentValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly ContextFactory $contextFactory,
    )
    {
    }

    /**
     * @throws \Exception
     */
    #[\Override] public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== ContextInterface::class) {
            return;
        }

        yield $this->contextFactory->create();
    }
}
