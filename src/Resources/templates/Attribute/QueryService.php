<?php
declare(strict_types=1);

namespace App\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class QueryService
{
    public function __construct(
        public readonly string $serviceName
    )
    {
    }
}
