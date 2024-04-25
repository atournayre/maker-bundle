# Entities


## Files
- EventListener
  - Make
    - MakeEntityListener.php
- Trait
  - Entity
    - BlameableEntityTrait.php
    - CreatedAtTrait.php
    - CreatedByTrait.php
    - EnableTrait.php
    - IdTrait.php
    - IsTrait.php
    - TimestampableEntityTrait.php
    - UpdatedAtTrait.php
    - UpdatedByTrait.php

## MakeEntityListener

This listener is executed when an entity is created or updated using the `make:entity` command.

By default, the listener will add the following traits to the entity:
- `BlameableEntityTrait`
- `TimestampableEntityTrait`
- `EventsTrait`
- `IdTrait`
- `IsTrait`
