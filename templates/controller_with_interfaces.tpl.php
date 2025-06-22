<?php

declare(strict_types=1);

namespace {{ namespace }};

use Atournayre\Contracts\Context\ContextInterface;
use Atournayre\Contracts\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
{% if extends_abstract_controller %}
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
{% endif %}
{% for interface in interfaces %}
use {{ interface }};
{% endfor %}

final class {{ class_name }}Controller{% if extends_abstract_controller %} extends AbstractController{% endif %}{% if interfaces is not empty %} implements {% for interface in interfaces %}{{ interface|split('\\')|last }}{% if not loop.last %}, {% endif %}{% endfor %}{% endif %}
{
    public function __construct(
        private readonly LoggerInterface $logger,
    )
    {
    }
    
    #[Route(path: '{{ route_pattern }}', name: '{{ route_name }}', methods: ['GET', 'POST'])]
    #[Template(template: '{{ template_path }}')]
    public function __invoke(Request $request, ContextInterface $context)
    {
        return TryCatch::with(function () use ($request) {
        
        }, $this->logger)
        // implements catch if needed
        ->execute();
    }
}
