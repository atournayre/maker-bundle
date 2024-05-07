<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Contracts;

use Nette\PhpGenerator\PhpFile;

interface PhpFileBuilderInterface
{
    public function create(MakerConfigurationInterface $makerConfiguration): void;

    public function setStrictTypes(MakerConfigurationInterface $makerConfiguration): void;

    public function setComments(MakerConfigurationInterface $makerConfiguration): void;

    public function setNamespace(MakerConfigurationInterface $makerConfiguration): void;

    public function addUses(MakerConfigurationInterface $makerConfiguration): void;

    public function addAttributes(MakerConfigurationInterface $makerConfiguration): void;

    public function setInterface(MakerConfigurationInterface $makerConfiguration): void;

    public function setTrait(MakerConfigurationInterface $makerConfiguration): void;

    public function setReadonly(MakerConfigurationInterface $makerConfiguration): void;

    public function setFinal(MakerConfigurationInterface $makerConfiguration): void;

    public function setClassName(MakerConfigurationInterface $makerConfiguration): void;

    public function setExtends(MakerConfigurationInterface $makerConfiguration): void;

    public function addImplements(MakerConfigurationInterface $makerConfiguration): void;

    public function addConstants(MakerConfigurationInterface $makerConfiguration): void;

    public function addTraits(MakerConfigurationInterface $makerConfiguration): void;

    public function addProperties(MakerConfigurationInterface $makerConfiguration): void;

    public function addMethods(MakerConfigurationInterface $makerConfiguration): void;
}
