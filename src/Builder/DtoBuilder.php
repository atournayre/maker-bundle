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
     * @return PhpFileDefinition
     */
    public function createPhpFileDefinition($makerConfiguration): PhpFileDefinition
    {
        $properties = array_map(
            fn (PropertyDefinition $propertyDatas): Property => $this->property($propertyDatas, $makerConfiguration),
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

    /**
     * @param PropertyDefinition $propertyDatas
     * @param DtoMakerConfiguration $configuration
     * @return Property
     */
    private function property(PropertyDefinition $propertyDatas, DtoMakerConfiguration $configuration): Property
    {
        $type = $propertyDatas->type;
        Assert::inArray(
            $type,
            array_keys($this->correspondingTypes($configuration)),
            Str::sprintf('Property "%s" should be of type %s; %s given', $propertyDatas->fieldName, Str::implode(', ', array_keys($this->correspondingTypes($configuration))), $type)
        );

        $property = new Property($propertyDatas->fieldName);
        $property->setVisibility('public')->setType($this->correspondingTypes($configuration)[$type]);

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

        if ($propertyDatas->nullable) {
            $property->setValue(null)->setNullable();
        }

        return $property;
    }

    private function namedConstructorFromArray(DtoMakerConfiguration $makerConfiguration): Method
    {
        $dtoProperties = $makerConfiguration->properties();

        $bodyParts = [];
        $bodyParts[] = '$dto = new self();';
        foreach ($dtoProperties as $property) {
            $bodyParts[] = Str::sprintf('$dto->%s = $data[\'%s\'];', $property->fieldName, $property->fieldName);
        }
        $bodyParts[] = '';
        $bodyParts[] = 'return $dto;';

        $method = new Method('fromArray');
        $method->setStatic()->setPublic()->setReturnType('self');
        $method->addParameter('data')->setType('array');

        foreach ($bodyParts as $line) {
            $method->addBody($line);
        }

        return $method;
    }

    private function methodValidate(DtoMakerConfiguration $makerConfiguration): Method
    {
        $dtoProperties = $makerConfiguration->properties();
        $className = $makerConfiguration->classname();

        $validationErrors = [];
        foreach ($dtoProperties as $property) {
            $if = 'if (%s) {'.PHP_EOL.'    $errors[\'%s\'] = \'validation.%s.%s.empty\';'.PHP_EOL.'}';
            $ifTest = match ($property->type) {
                'datetime' => "null === \$this->{$property->fieldName}",
                default => "'' == \$this->{$property->fieldName}",
            };
            $fieldName = Str::property($property->fieldName);
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

    private function nullableTrait(DtoMakerConfiguration $makerConfiguration): string
    {
        if (Str::startsWith($makerConfiguration->classname(), 'Null')) {
            return NullableTrait::class;
        }

        return NotNullableTrait::class;
    }
}
