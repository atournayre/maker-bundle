# Null Object Pattern

## Why?

The Null Object Pattern is a design pattern that simplifies the use of null values. It is used to avoid null checks and to provide a default behavior when a value is null.

## Files
- Contracts
  - Null
    - NullableInterface.php
- Trait
  - NotNullableTrait.php
  - NullableTrait.php

## Usage

### NullableInterface
A DTO, an entity or a value object can implement the NullableInterface.

### NotNullableTrait
A DTO, an entity or a value object can use the NotNullableTrait to implement the NullableInterface.

### NullableTrait
A DTO, an entity or a value object can use the NullableTrait to implement the NullableInterface if the object can be null.

The name of the class should start `Null` (e.g. `NullUser`).
