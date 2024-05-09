<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use App\Contracts\Null\NullableInterface;
use App\Trait\NotNullableTrait;
use App\Trait\NullableTrait;
use Atournayre\Bundle\MakerBundle\Config\DtoMakerConfiguration;
use Atournayre\Bundle\MakerBundle\DTO\PropertyDefinition;
use Atournayre\Bundle\MakerBundle\Helper\Str;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Property;
use Webmozart\Assert\Assert;

final class DtoBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === DtoMakerConfiguration::class;
    }

    /**
     * @param DtoMakerConfiguration $makerConfiguration
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $properties = array_map(
            fn (PropertyDefinition $propertyDefinition): Property => $this->property($propertyDefinition, $makerConfiguration),
            $makerConfiguration->properties()
        );

        $nullableTrait = $this->nullableTrait($makerConfiguration);

        return parent::createPhpFileDefinition($makerConfiguration)
            ->setProperties($properties)
            ->setMethods([
                $this->namedConstructorFromArray($makerConfiguration),
                $this->methodValidate($makerConfiguration),
            ])
            ->setImplements([NullableInterface::class])
            ->setTraits([$nullableTrait])
            ;
    }

    private function property(PropertyDefinition $propertyDefinition, DtoMakerConfiguration $dtoMakerConfiguration): Property
    {
        $type = $propertyDefinition->type;
        Assert::inArray(
            $type,
            array_keys($this->correspondingTypes($dtoMakerConfiguration)),
            Str::sprintf('Property "%s" should be of type %s; %s given', $propertyDefinition->fieldName, Str::implode(', ', array_keys($this->correspondingTypes($dtoMakerConfiguration))), $type)
        );

        $property = new Property($propertyDefinition->fieldName);
        $property->setVisibility('public')->setType($this->correspondingTypes($dtoMakerConfiguration)[$type]);

        $defaultValue = match ($type) {
            'string' => '',
            'integer' => 0,
            'float' => 0.0,
            'bool' => false,
            default => null,
        };

        $property->setValue($defaultValue);

        if (null === $defaultValue) {
            $property->setNullable();
        }

        if ($propertyDefinition->nullable) {
            $property->setValue(null)->setNullable();
        }

        return $property;
    }

    private function namedConstructorFromArray(DtoMakerConfiguration $dtoMakerConfiguration): Method
    {
        $dtoProperties = $dtoMakerConfiguration->properties();

        $bodyParts = [];
        $bodyParts[] = '$dto = new self();';
        foreach ($dtoProperties as $dtoProperty) {
            $bodyParts[] = Str::sprintf('$dto->%s = $data[\'%s\'];', $dtoProperty->fieldName, $dtoProperty->fieldName);
        }
        $bodyParts[] = '';
        $bodyParts[] = 'return $dto;';

        $method = new Method('fromArray');
        $method->setStatic()->setPublic()->setReturnType('self');
        $method->addParameter('data')->setType('array');

        foreach ($bodyParts as $bodyPart) {
            $method->addBody($bodyPart);
        }

        return $method;
    }

    private function methodValidate(DtoMakerConfiguration $dtoMakerConfiguration): Method
    {
        $dtoProperties = $dtoMakerConfiguration->properties();
        $className = $dtoMakerConfiguration->classname();

        $validationErrors = [];
        foreach ($dtoProperties as $dtoProperty) {
            $if = 'if (%s) {'.PHP_EOL.'    $errors[\'%s\'] = \'validation.%s.%s.empty\';'.PHP_EOL.'}';
            $ifTest = match ($dtoProperty->type) {
                'datetime' => "null === \$this->{$dtoProperty->fieldName}",
                default => "'' == \$this->{$dtoProperty->fieldName}",
            };
            $fieldName = Str::property($dtoProperty->fieldName);
            $dtoName = Str::asCamelCase($className);

            $validationErrors[] = Str::sprintf($if, $ifTest, $fieldName, $dtoName, $fieldName);
        }

        $errors = '$errors = [];

%s

// Add more validation rules here

return $errors;';

        $body = Str::sprintf($errors, Str::implode("\n", $validationErrors));

        return (new Method('validate'))
            ->setPublic()
            ->setReturnType('array')
            ->setBody($body);
    }

    private function nullableTrait(DtoMakerConfiguration $dtoMakerConfiguration): string
    {
        if (Str::startsWith($dtoMakerConfiguration->classname(), 'Null')) {
            return NullableTrait::class;
        }

        return NotNullableTrait::class;
    }
}
