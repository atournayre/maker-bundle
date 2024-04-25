# Domain events

## Why?

Domain events are a way to decouple the domain logic from the application logic. They are a way to notify other parts of the application that something has happened. This is useful for logging, sending emails, updating caches, etc.

## Files
- Collection
  - EventCollection.php
- Contracts
  - Event
    - EntityEventDispatcherInterface.php
    - HasEventsInterface.php
- Dispatcher
  - EntityEventDispatcher.php
- Trait
  - EventsTrait.php

## Usage

Entity should :
- implement HasEventsInterface
- use EventsTrait
- initialize the EventCollection in the constructor

This could be done for all the entities using the command `make:add:events-to-entities`.


Use EntityEventDispatcherInterface to dispatch events.

```php
// Entity
class Entity implements HasEventsInterface
{
    use EventsTrait;

    public function __construct()
    {
        $this->events = EventCollection::createAsList([]);
    }

    public function doSomething(): void
    {
        // ...
        $this->addEvent(new SomethingHappened($this));
        $this->addEvent(new AnotherthingHappened($this));
        // ...
    }
}
```

```php
// Service
use App\Contracts\VO\ContextInterface;use App\Factory\ContextFactory;

class Service
{
    public function __construct(
        private EntityEventDispatcherInterface $dispatcher,
        private ContextFactory $contextFactory,
    ) {}
    
    // Example with context
    public function exampleWithContext(Entity $entity, ContextInterface $context): void
    {
        // ...
        $this->dispatcher->dispatch($entity->events(), $context);
        // ...
    }
    
    // Example with specific user
    public function exampleWithUser(Entity $entity, UserInterface $user): void
    {
        // ...
        $this->dispatcher->dispatch($entity->events(), $this->contextFactory->create($user));
        // ...
    }
    
    // Example with default context
    public function exampleWithDefaultContext(Entity $entity): void
    {
        // ...
        $this->dispatcher->dispatch($entity->events(), $this->contextFactory->create());
        // ...
    }
    
    // Example with specific event and default context
    public function exampleSpecificEventWithDefaultContext(Entity $entity): void
    {
        // ...
        $this->dispatcher->dispatch($entity->events(SomethingHappened::class));
        // ...
    }
}
```



