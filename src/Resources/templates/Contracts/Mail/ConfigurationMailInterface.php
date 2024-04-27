<?php
declare(strict_types=1);

namespace App\Contracts\Mail;

interface ConfigurationMailInterface
{
    public function getConfiguration(): MailerConfigurationInterface;
}
