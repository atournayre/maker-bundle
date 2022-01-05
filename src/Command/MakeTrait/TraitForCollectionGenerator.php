<?php

namespace Atournayre\Bundle\MakerBundle\Command\MakeTrait;

use Doctrine\Common\Collections\Collection;
use Exception;
use JetBrains\PhpStorm\Pure;
use PhpParser\Builder\Method;
use PhpParser\Builder\Param;
use PhpParser\Builder\Use_;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Bundle\MakerBundle\Util\ClassSourceManipulator;

class TraitForCollectionGenerator extends TraitGenerator
{
    /**
     * @param string $objectNamespace
     * @param bool   $regenerate
     *
     * @return ClassNameDetails
     * @throws Exception
     */
    public function generate(string $objectNamespace, bool $regenerate = false): ClassNameDetails
    {
        $name = Str::getShortClassName($objectNamespace);

        $classNameDetails = $this->generator
            ->createClassNameDetails($this->setName($name), 'Traits\\');

        if ($regenerate) {
            $this->removeTraitFile($classNameDetails->getFullName());
        }

        $entityPath = $this->generator->generateClass($classNameDetails->getFullName(), 'Class.tpl.php');
        $this->generator->writeChanges();

        $manipulator = $this->createClassSourceManipulator($entityPath);

        $manipulator->addUseStatementIfNecessary($objectNamespace);
        $manipulator->addUseStatementIfNecessary(Collection::class);

        $property = Str::asLowerCamelCase($name);
        $propertyCollection = $property . 's';
        $type = Str::asCamelCase(Str::asLowerCamelCase($name));

        $manipulator->addProperty($propertyCollection);
        $this->addGetter($manipulator, $propertyCollection, $type);
        $this->addSetter($manipulator, $propertyCollection, $type, $name);
        $this->addAdder($manipulator, $property, $propertyCollection, $type, $name);
        $this->addRemover($manipulator, $property, $propertyCollection, $type);
        $this->addAllRemover($manipulator, $propertyCollection, $type);
        $this->addHaser($manipulator, $propertyCollection, $type);
        $this->addCounter($manipulator, $propertyCollection, $type);

        $sourceCode = $this->adjustSourceCode($manipulator, $name, $propertyCollection, $type);
        $this->saveFile($entityPath, $sourceCode);

        return $classNameDetails;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function setName(string $name): string
    {
        return sprintf('%sCollectionTrait', $name);
    }

    /**
     * @param ClassSourceManipulator $manipulator
     * @param string                 $property
     * @param string                 $type
     *
     * @return void
     */
    protected function addGetter(ClassSourceManipulator $manipulator, string $property, string $type): void
    {
        $manipulator
            ->addGetter($property, 'Collection', false, [
                sprintf('@return %s', 'Collection'),
            ]);
    }

    /**
     * @param ClassSourceManipulator $manipulator
     * @param string                 $property
     * @param string                 $type
     * @param string                 $name
     *
     * @return void
     */
    protected function addSetter(ClassSourceManipulator $manipulator, string $property, string $type, string $name): void
    {
        $manipulator
            ->addSetter($property, 'Collection', false, [
                sprintf('@param %s $%s', 'Collection', $property),
                '',
                sprintf('@return %s|%s', $this->setName($name), $type),
            ]);
    }

    /**
     * @param ClassSourceManipulator $manipulator
     * @param string                 $property
     * @param string                 $propertyCollection
     * @param string                 $type
     * @param string                 $name
     *
     * @return void
     */
    private function addAdder(
        ClassSourceManipulator $manipulator,
        string                 $property,
        string                 $propertyCollection,
        string                 $type,
        string                 $name
    ): void
    {
        $methodBuilder = $this->createPublicMethod('add' . $type, 'self');
        $methodBuilder->addParam($this->createParam($type, $property));

        $methodBuilder->setDocComment(
            $this->createDocBlock([
                                      sprintf('@param %s $%s', $type, $property),
                                      sprintf('@return %s|%s', $this->setName($name), $type),
                                  ])
        );


        $ifNode = new If_(
            $this->callMethodOnProperty($propertyCollection, 'contains', [
                new Arg(new Variable($property)),
            ])
        );
        $methodBuilder->addStmt($ifNode);

        $ifNode->stmts = [
            $this->returnThis(),
        ];

        $methodBuilder->addStmt($this->createEmptyLine());

        $addToProperty = $this->callMethodOnProperty($propertyCollection, 'add', [
            new Arg(new Variable($property)),
        ]);

        $methodBuilder->addStmt($addToProperty);

        $this->makeMethodFluent($methodBuilder);

        $manipulator->addMethodBuilder($methodBuilder);
    }

    /**
     * @param string $methodName
     * @param string $returnType
     *
     * @return Method
     */
    private function createPublicMethod(string $methodName, string $returnType): Method
    {
        $methodBuilder = new Method($methodName);
        $methodBuilder->makePublic();
        $methodBuilder->setReturnType($returnType);
        return $methodBuilder;
    }

    /**
     * @param array $commentLines
     *
     * @return string
     */
    private function createDocBlock(array $commentLines): string
    {
        $docBlock = "/**\n";
        foreach ($commentLines as $commentLine) {
            if ($commentLine) {
                $docBlock .= " * $commentLine\n";
            } else {
                // avoid the empty, extra space on blank lines
                $docBlock .= " *\n";
            }
        }
        $docBlock .= "\n */";

        return $docBlock;
    }

    /**
     * @param string $property
     * @param string $method
     * @param array  $attributes
     *
     * @return Expr\MethodCall
     */
    private function callMethodOnProperty(string $property, string $method, array $attributes = []): Expr\MethodCall
    {
        return new Expr\MethodCall(
            new Expr\PropertyFetch(new Variable('this'), $property),
            $method,
            $attributes
        );
    }

    /**
     * @return Return_
     */
    #[Pure]
    private function returnThis(): Return_
    {
        return new Return_(new Variable('this'));
    }

    /**
     * @return Node|Node\Stmt\Use_
     */
    private function createEmptyLine(): Node\Stmt\Use_|Node
    {
        return (new Use_('__EXTRA__LINE', Node\Stmt\Use_::TYPE_NORMAL))
            ->getNode();
    }

    /**
     * @param Method $methodBuilder
     *
     * @return void
     */
    private function makeMethodFluent(Method $methodBuilder): void
    {
        $methodBuilder
            ->addStmt($this->createEmptyLine())
            ->addStmt($this->returnThis())
            ->setReturnType('self');
    }

    /**
     * @param ClassSourceManipulator $manipulator
     * @param string                 $property
     * @param string                 $propertyCollection
     * @param string                 $type
     *
     * @return void
     */
    private function addRemover(
        ClassSourceManipulator $manipulator,
        string                 $property,
        string                 $propertyCollection,
        string                 $type
    ): void
    {
        $methodBuilder = $this->createPublicMethod('remove' . $type, 'void');
        $methodBuilder->setDocComment(
            $this->createDocBlock([
                                      sprintf('@param %s $%s', $type, $property),
                                  ])
        );

        $methodBuilder->addParam($this->createParam($type, $property));

        $removeElementFormProperty = $this->callMethodOnProperty($propertyCollection, 'removeElement', [
            new Arg(new Variable($property)),
        ]);
        $methodBuilder->addStmt($removeElementFormProperty);

        $manipulator->addMethodBuilder($methodBuilder);
    }

    /**
     * @param ClassSourceManipulator $manipulator
     * @param string                 $propertyCollection
     * @param string                 $type
     *
     * @return void
     */
    private function addAllRemover(
        ClassSourceManipulator $manipulator,
        string                 $propertyCollection,
        string                 $type
    ): void
    {
        $methodBuilder = $this->createPublicMethod('removeAll' . $type . 's', 'void');
        $methodBuilder->setDocComment(
            $this->createDocBlock([
                                      '@return void',
                                  ])
        );

        $removeElementFormProperty = $this->callMethodOnProperty($propertyCollection, 'clear');
        $methodBuilder->addStmt($removeElementFormProperty);

        $manipulator->addMethodBuilder($methodBuilder);
    }

    /**
     * @param ClassSourceManipulator $manipulator
     * @param string                 $propertyCollection
     * @param string                 $type
     *
     * @return void
     */
    private function addHaser(
        ClassSourceManipulator $manipulator,
        string                 $propertyCollection,
        string                 $type
    ): void
    {
        $methodBuilder = $this->createPublicMethod('has' . $type, 'bool');
        $methodBuilder->setDocComment(
            $this->createDocBlock([
                                      '@return bool',
                                  ])
        );

        $has = new Return_(
            new Expr\BooleanNot(
                $this->callMethodOnProperty($propertyCollection, 'isEmpty')
            )
        );

        $methodBuilder->addStmt($has);

        $manipulator->addMethodBuilder($methodBuilder);
    }

    /**
     * @param ClassSourceManipulator $manipulator
     * @param string                 $propertyCollection
     * @param string                 $type
     *
     * @return void
     */
    private function addCounter(
        ClassSourceManipulator $manipulator,
        string                 $propertyCollection,
        string                 $type
    ): void
    {
        $methodBuilder = $this->createPublicMethod('count' . $type . 's', 'int');
        $methodBuilder->setDocComment(
            $this->createDocBlock([
                                      '@return int',
                                  ])
        );

        $has = new Return_($this->callMethodOnProperty($propertyCollection, 'count'));

        $methodBuilder->addStmt($has);

        $manipulator->addMethodBuilder($methodBuilder);
    }

    /**
     * @param ClassSourceManipulator $manipulator
     * @param string                 $className
     * @param string                 $property
     * @param string                 $type
     *
     * @return string
     */
    protected function adjustSourceCode(
        ClassSourceManipulator $manipulator,
        string                 $className,
        string                 $property,
        string                 $type
    ): string
    {
        $sourceCode = str_replace(
            sprintf('class %s', $className),
            sprintf('trait %s', $className),
            $manipulator->getSourceCode()
        );
        return str_replace(
            sprintf('private $%s', $property),
            sprintf('private Collection $%s', $property),
            $sourceCode
        );
    }

    /**
     * @param string $type
     * @param string $property
     *
     * @return Param
     */
    private function createParam(string $type, string $property): Param
    {
        return (new Param($property))
            ->setType($type);
    }
}
