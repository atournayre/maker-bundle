<?php
declare(strict_types=1);

namespace App\Configuration;

use App\Contracts\Mail\MailerConfigurationInterface;
use App\Trait\Mail\MailerConfigurationTrait;

final class MailerConfiguration implements MailerConfigurationInterface
{
    use MailerConfigurationTrait;
}
