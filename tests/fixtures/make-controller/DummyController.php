<?php

/**
 * This file has been auto-generated
 */

declare(strict_types=1);

namespace App\Controller;

use App\Contracts\VO\ContextInterface;
use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureEntity;
use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureFormType;
use Atournayre\Bundle\MakerBundle\Tests\fixtures\FixtureVo;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route(
    path: '/template/{id}',
    name: 'template',
    requirements: ['id' => '\d+'],
    methods: [Request::METHOD_GET],
)]
final class DummyController extends AbstractControllerWithForm
{
    /**
     * @param FixtureVo|null $data
     */
    protected function createForm($data = null): FormInterface
    {
        return $this->formFactory->create(FixtureFormType::class, $data, []);
    }

    /**
     * @param FixtureEntity $entity
     */
    protected function createVo($entity, ContextInterface $context): FixtureVo
    {
        return FixtureVo::create($entity);
    }

    protected function errorTemplate(): string
    {
        return 'template/error.html.twig';
    }

    protected function redirectOnSuccess($data, ContextInterface $context): Response
    {
        throw new \RuntimeException('You must implement the method redirectOnSuccess');
        // return $this->response->redirectToRoute('', [
            // Add parameters
        // ]);
    }

    protected function successTemplate(): string
    {
        return 'template/success.html.twig';
    }
}
