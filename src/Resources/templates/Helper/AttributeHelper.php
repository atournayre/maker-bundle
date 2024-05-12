<?php
declare(strict_types=1);

namespace App\Helper;

class AttributeHelper
{
	public static function getNamedArguments($objectOrClass, string $attributeName): array
	{
		$reflectionClass = new \ReflectionClass($objectOrClass::class);
		$attributes = $reflectionClass->getAttributes();

		$attributes = array_filter($attributes, static fn($attribute): bool => $attribute->getName() === $attributeName);

		$namedArguments = [];
		foreach ($attributes as $attribute) {
		    $arguments = $attribute->getArguments();

		    $reflectionAttribute = $attribute->newInstance();
		    $reflectionMethod = new \ReflectionMethod($reflectionAttribute, '__construct');
		    $parameters = $reflectionMethod->getParameters();

		    foreach ($parameters as $parameter) {
		        $paramName = $parameter->getName();
		        $paramPosition = $parameter->getPosition();

		        $namedArguments[$paramName] = $arguments[$paramName] ?? $arguments[$paramPosition];
		    }
		}

		return $namedArguments;
	}


	public static function getParameter($objectOrClass, string $attributeName, string $paramName): ?string
	{
		$params = self::getNamedArguments($objectOrClass, $attributeName);
		return $params[$paramName] ?? null;
	}
}
