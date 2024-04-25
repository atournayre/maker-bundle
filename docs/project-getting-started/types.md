# Types

To be more DDD compliant, primitive types are provided.

## Files

- Contracts
  - Type
    - Primitive
      - ScalarObjectInterface.php
- Type
  - Primitive
    - AbstractCollectionType.php
    - BooleanType.php
    - IntegerType.php
    - ListImmutableType.php
    - ListType.php
    - MapImmutableType.php
    - MapType.php
    - StringType.php

## Description

There is no FloatType on purpose, use IntegerType instead, then use ->toFloat().

Create your own types by extending from the type you need.
