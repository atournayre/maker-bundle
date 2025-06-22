<?php
// Template for generating a collection class
?>
<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use Atournayre\Common\Assert\Assert;
use Atournayre\Contracts\Collection\AsListInterface;
use Atournayre\Contracts\Collection\AsMapInterface;
use Atournayre\Contracts\Exception\ThrowableInterface;
use Atournayre\Primitives\Collection;
use Atournayre\Primitives\Traits\Collection as Collection_;

final class <?= $collection_name ?> implements <?= implode(', ', $interfaces) ?>
{
    use Collection_;

<?php if (in_array('AsListInterface', $interfaces)): ?>
    public static function asList(array $collection): self
    {
        Assert::isListOf($collection, <?= $class_name ?>::class);

        return new self(Collection::of($collection));
    }
<?php endif ?>

<?php if (in_array('AsMapInterface', $interfaces)): ?>
    public static function asMap(array $collection): self
    {
        Assert::isMapOf($collection, <?= $class_name ?>::class);

        return new self(Collection::of($collection));
    }
<?php endif ?>
}
