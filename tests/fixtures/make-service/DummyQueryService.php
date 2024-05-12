<?php

/**
 * This file has been auto-generated
 */

declare(strict_types=1);

namespace App\Service\Query;

use App\Contracts\Service\FailFastInterface;
use App\Contracts\Service\PostConditionsChecksInterface;
use App\Contracts\Service\PreConditionsChecksInterface;
use App\Contracts\Service\QueryServiceInterface;
use App\Contracts\Service\TagQueryServiceInterface;
use App\Contracts\VO\ContextInterface;
use App\Exception\FailFast;
use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureVo;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(TagQueryServiceInterface::class)]
final readonly class DummyQueryService implements FailFastInterface, PostConditionsChecksInterface, PreConditionsChecksInterface, QueryServiceInterface
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
     * Implement logic here, or remove method and interface from the class if not needed.
     * @throws FailFast
     * @param FixtureVo $object
     */
    public function failFast($object, ContextInterface $context): void
    {
    }

    /**
     * @param FixtureVo $object
     */
    public function fetch($object, ContextInterface $context, ?string $service = null)
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
