<?php

/**
 * This file has been auto-generated
 */

declare(strict_types=1);

namespace App\Service\Command;

use App\Contracts\Service\CommandServiceInterface;
use App\Contracts\Service\FailFastInterface;
use App\Contracts\Service\PostConditionsChecksInterface;
use App\Contracts\Service\PreConditionsChecksInterface;
use App\Contracts\Service\TagCommandServiceInterface;
use App\Contracts\VO\ContextInterface;
use App\Exception\FailFast;
use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureVo;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(TagCommandServiceInterface::class)]
final readonly class DummyCommandService implements CommandServiceInterface, FailFastInterface, PostConditionsChecksInterface, PreConditionsChecksInterface
{
    /**
     * This service is not meant to be used directly
     * @throws \RuntimeException
     */
    public function __invoke()
    {
        throw new \RuntimeException('This service is not meant to be used directly');
    }

    /**
     * @param FixtureVo $object
     */
    public function execute($object, ContextInterface $context, ?string $service = null): void
    {
    }

    /**
     * Implement logic here, or remove method and interface from the class if not needed.
     * @throws FailFast
     * @param FixtureVo $object
     */
    public function failFast($object, ContextInterface $context): void
    {
    }

    /**
     * Use assertions, or remove method and interface from the class if not needed.
     * @throws \Exception
     * @param FixtureVo $object
     */
    public function postConditionsChecks($object, ContextInterface $context): void
    {
    }

    /**
     * Use assertions, or remove method and interface from the class if not needed.
     * @throws \Exception
     * @param FixtureVo $object
     */
    public function preConditionsChecks($object, ContextInterface $context): void
    {
    }
}
