<?php
declare(strict_types=1);

namespace App\Controller;

use App\VO\Context;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route(
    path: '/template/{id}',
    name: 'template',
    requirements: [
        'id' => '\d+',
    ],
    methods: [Request::METHOD_GET],
)]
final class WithFormController extends AbstractControllerWithForm
{
    protected function redirectOnSuccess($data, Context $context): Response
    {
        throw new \RuntimeException('You must implement the method redirectOnSuccess');
        // return $this->response->redirectToRoute('', [
            // Add parameters
        // ]);
    }

    /**
     * @param EntityNamespace $entity
     * @param Context $context
     * @return mixed
     */
    protected function createVo($entity, Context $context)
    {
        return VoNamespace::create($entity);
    }

    protected function createForm($data = null): FormInterface
    {
        return $this->formFactory->create(FormType::class, $data, []);
    }
    protected function successTemplate(): string
    {
        return 'template/success.html.twig';
    }

    protected function errorTemplate(): string
    {
        return 'template/error.html.twig';
    }
}
