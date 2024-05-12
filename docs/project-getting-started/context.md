# Context

## Files
- Contracts
  - HasContextInterface.php
  - VO
    - ContextInterface.php
    - DateTimeInterface.php
- Factory
  - ContextFactory.php
- Trait
  - ContextTrait.php
- VO
  - Context.php
  - DateTime.php
  - Null
    - NullContext.php
    - NullDateTime.php
    - NullUser.php


#### Context
Context should always be injected (in controllers, commands, queries, services, etc.).

A Context (value object) is available as an argument in controllers thanks to Symfony's ArgumentValueResolver.

Anywhere else, you can use the ContextFactory to create a Context.

A Context is a collection of values:
- user (UserInterface)
- createdAt (DateTime - a value object with specifics methods)


#### DateTime

A DateTime is a value object with specifics methods.

Do not confuse with the PHP DateTime class.

DateTime has a method toDateTime() to get the PHP DateTime object.
