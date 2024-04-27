# Sending emails

## Files
- Configuration
  - MailerConfiguration.php
- Contracts
  - Mail
    - ConfigurationMailInterface.php
    - MailerConfigurationInterface.php
    - SendMailInterface.php
- Service
  - Mail
    - MailService.php
    - SymfonySendMailService.php
- Trait
  - Mail
    - MailerConfigurationTrait.php

## Configuration

1. Create as many configuration definitions as needed.
2. Create as many mails sender as needed.

```yaml
# config/services.yaml
services:
    # Define a configuration (for example, noreply)
    app.mailer.configuration.noreply:
        class: App\Configuration\MailerConfiguration
        calls:
            - [setFromAddress, ['noreply@example.com']]
            - [setFromName, ['My Application']]
            - [setAttachmentsMaxSize, ['10000']]
    # Then, define the related mailer
    app.mailer.send.noreply:
        class: App\Service\Mail\MailService
        arguments:
            $configuration: '@app.mailer.configuration.noreply'
```

## Usage

Use the `MailService` to send emails.

```php
namespace App\Service;

use App\Service\Mail\MailService;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class MyService
{
    public function __construct(
        #[Autowire(service: 'app.mailer.send.noreply')]
        private MailService $mailService,
    )
    {
    }

    public function __invoke()
    {
        $mail;
        $this->mailService->send($mail);
    }
```
