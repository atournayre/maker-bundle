<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\DTO\Config;

final class Namespaces
{
    public readonly ?string $adapter;
    public readonly ?string $argumentValueResolver;
    public readonly ?string $attribute;
    public readonly ?string $collection;
    public readonly ?string $command;
    public readonly ?string $configuration;
    public readonly ?string $contracts;
    public readonly ?string $controller;
    public readonly ?string $dispatcher;
    public readonly ?string $dto;
    public readonly ?string $entity;
    public readonly ?string $event;
    public readonly ?string $eventListener;
    public readonly ?string $exception;
    public readonly ?string $factory;
    public readonly ?string $helper;
    public readonly ?string $logger;
    public readonly ?string $manager;
    public readonly ?string $serviceCommand;
    public readonly ?string $serviceQuery;
    public readonly ?string $trait;
    public readonly ?string $traitEntity;
    public readonly ?string $type;
    public readonly ?string $vo;

    public static function fromArray(array $namespaces): self
    {
        $instance = new self();
        $instance->adapter = $namespaces['adapter'] ?? null;
        $instance->argumentValueResolver = $namespaces['argument_value_resolver'] ?? null;
        $instance->attribute = $namespaces['attribute'] ?? null;
        $instance->collection = $namespaces['collection'] ?? null;
        $instance->command = $namespaces['command'] ?? null;
        $instance->configuration = $namespaces['configuration'] ?? null;
        $instance->contracts = $namespaces['contracts'] ?? null;
        $instance->controller = $namespaces['controller'] ?? null;
        $instance->dispatcher = $namespaces['dispatcher'] ?? null;
        $instance->dto = $namespaces['dto'] ?? null;
        $instance->entity = $namespaces['entity'] ?? null;
        $instance->event = $namespaces['event'] ?? null;
        $instance->eventListener = $namespaces['event_listener'] ?? null;
        $instance->exception = $namespaces['exception'] ?? null;
        $instance->factory = $namespaces['factory'] ?? null;
        $instance->helper = $namespaces['helper'] ?? null;
        $instance->logger = $namespaces['logger'] ?? null;
        $instance->manager = $namespaces['manager'] ?? null;
        $instance->serviceCommand = $namespaces['service_command'] ?? null;
        $instance->serviceQuery = $namespaces['service_query'] ?? null;
        $instance->trait = $namespaces['trait'] ?? null;
        $instance->traitEntity = $namespaces['trait_entity'] ?? null;
        $instance->type = $namespaces['type'] ?? null;
        $instance->vo = $namespaces['vo'] ?? null;

        return $instance;
    }
}
