<?php
// Template for generating a class
?>
<?= "<?php\n" ?>

namespace <?= $namespace ?>;

<?php if (!empty($uses)): ?>
<?php foreach ($uses as $use): ?>
use <?= $use ?>;
<?php endforeach ?>
<?php endif ?>

/**
 * <?= $class_name ?> class.
<?php if (!empty($description)): ?>
 * <?= $description ?>
<?php endif ?>
 */
<?php if (!empty($attributes)): ?>
<?php foreach ($attributes as $attribute): ?>
#[<?= $attribute ?>]
<?php endforeach ?>
<?php endif ?>
class <?= $class_name ?><?php if (!empty($extends)): ?> extends <?= $extends ?><?php endif ?><?php if (!empty($implements)): ?> implements <?= implode(', ', $implements) ?><?php endif ?>

{
<?php if (!empty($properties)): ?>
<?php foreach ($properties as $property): ?>
    <?= $property['visibility'] ?> <?= $property['type'] ?> $<?= $property['name'] ?><?php if (isset($property['default'])): ?> = <?= $property['default'] ?><?php endif ?>;
<?php endforeach ?>
<?php endif ?>

<?php if (!empty($constructor)): ?>
    public function __construct(
<?php foreach ($constructor['params'] as $index => $param): ?>
        <?= $param['type'] ?> $<?= $param['name'] ?><?php if ($index < count($constructor['params']) - 1): ?>,<?php endif ?>

<?php endforeach ?>
    ) {
<?php foreach ($constructor['params'] as $param): ?>
        $this-><?= $param['name'] ?> = $<?= $param['name'] ?>;
<?php endforeach ?>
    }
<?php endif ?>

<?php if (!empty($methods)): ?>
<?php foreach ($methods as $method): ?>
    <?= $method['visibility'] ?> function <?= $method['name'] ?>(<?php foreach ($method['params'] as $index => $param): ?><?= $param['type'] ?> $<?= $param['name'] ?><?php if ($index < count($method['params']) - 1): ?>, <?php endif ?><?php endforeach ?>): <?= $method['return_type'] ?>
    {
        <?= $method['body'] ?>
    }
<?php endforeach ?>
<?php endif ?>
}
