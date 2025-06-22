<?php
// Template for generating a controller class with interfaces
?>
<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use Atournayre\Contracts\Context\ContextInterface;
use Atournayre\Contracts\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
<?php if ($extends_abstract_controller): ?>
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
<?php endif ?>
<?php foreach ($interfaces as $interface): ?>
use <?= $interface ?>;
<?php endforeach ?>

final readonly class <?= $class_name ?>Controller<?php if ($extends_abstract_controller): ?> extends AbstractController<?php endif ?><?php if (!empty($interfaces)): ?> implements <?php 
$interfaceNames = [];
foreach ($interfaces as $interface) {
    $parts = explode('\\', $interface);
    $interfaceNames[] = end($parts);
}
echo implode(', ', $interfaceNames);
?><?php endif ?>
{
    public function __construct(
        private readonly LoggerInterface $logger,
    )
    {
    }

    #[Route(path: '<?= $route_pattern ?>', name: '<?= $route_name ?>', methods: ['GET', 'POST'])]
    #[Template(template: '<?= $template_path ?>')]
    public function __invoke(Request $request, ContextInterface $context)
    {
        return TryCatch::with(function () use ($request) {

        }, $this->logger)
        // implements catch if needed
        ->execute();
    }
}
