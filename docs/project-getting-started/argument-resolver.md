# Argument resolver

Using Symfony, a type hinted argument can be used to get the context.

## What is a context?

A context is a value object that contains the user and the creation date.

## Files
- ArgumentValueResolver
  - ContextArgumentValueResolver.php

## Usage
```php
<?php
namespace App\Controller;

use App\Contracts\VO\ContextInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AcmeController extends AbstractController
{
    public function index(ContextInterface $context, AcmeService $acme): Response
    {
        // ...
        $user = $context->user();
        $createdAt = $context->createdAt()->toDateTime();
        // ...
        $acme->doSomething($context);
        $data = $acme->fetchSomething($context);
        // ...
    }
}
```

See the Value Object Context to have more information.
