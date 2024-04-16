<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
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
final class SimpleController extends AbstractControllerSimple
{
    protected function successTemplate(): string
    {
        return 'template/success.html.twig';
    }

    protected function errorTemplate(): string
    {
        return 'template/error.html.twig';
    }
}
