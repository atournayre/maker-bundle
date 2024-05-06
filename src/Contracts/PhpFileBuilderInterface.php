<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Contracts;

use Atournayre\Bundle\MakerBundle\Config\MakerConfiguration;
use Nette\PhpGenerator\PhpFile;

interface PhpFileBuilderInterface
{
    public function create(MakerConfiguration $makerConfiguration): void;

    public function setStrictTypes(MakerConfiguration $makerConfiguration): void;

    public function setComments(MakerConfiguration $makerConfiguration): void;

    public function setNamespace(MakerConfiguration $makerConfiguration): void;

    public function addUses(MakerConfiguration $makerConfiguration): void;

    public function addAttributes(MakerConfiguration $makerConfiguration): void;

    public function setInterface(MakerConfiguration $makerConfiguration): void;

    public function setTrait(MakerConfiguration $makerConfiguration): void;

    public function setReadonly(MakerConfiguration $makerConfiguration): void;

    public function setFinal(MakerConfiguration $makerConfiguration): void;

    public function setClassName(MakerConfiguration $makerConfiguration): void;

    public function setExtends(MakerConfiguration $makerConfiguration): void;

    public function addImplements(MakerConfiguration $makerConfiguration): void;

    public function addConstants(MakerConfiguration $makerConfiguration): void;

    public function addTraits(MakerConfiguration $makerConfiguration): void;

    public function addProperties(MakerConfiguration $makerConfiguration): void;

    public function addMethods(MakerConfiguration $makerConfiguration): void;

    public function phpFile(): PhpFile;

    public function printPhpFile(?MakerConfiguration $makerConfiguration = null): string;
}
