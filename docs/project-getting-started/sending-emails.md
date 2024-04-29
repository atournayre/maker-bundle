# Sending emails

## Files
- Adapter
  - Mail
    - SymfonyEmailAdapter.php
- Collection
  - Mail
    - EmailAddressCollection.php
    - TagCollection.php
- Configuration
  - MailerConfiguration.php
- Contracts
  - Mail
    - ConfigurationMailInterface.php
    - SendMailInterface.php
- Service
  - Mail
    - MailService.php
    - SymfonySendMailService.php
- VO
  - Mail
    - Email.php

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

use App\Collection\Mail\EmailAddressCollection;
use App\Collection\TagCollection;
use App\Service\Mail\MailService;
use Atournayre\Types\EmailAddress;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class MyService
{
    public function __construct(
        #[Autowire(service: 'app.mailer.send.noreply')]
        private MailService $mailService,
    ) {}

    public function __invoke(): void
    {
        $mail = $this->createMail();
        $this->mailService->send($mail);
    }

    private function createMail(): Mail
    {
        $from = EmailAddress::fromString('from@example.com');

        $to = EmailAddressCollection::createAsList([
            EmailAddress::fromString('to@example.com'),
        ]);

        $tags = TagCollection::createAsMap([
            'tag1' => 'test_tag',
        ]);

        return Email::fromConfiguration(
            $this->mailService->getConfiguration(),
            'This is a test email',
            $from
        )
            ->withTo($to)
            ->withText('This is a text.')
            ->withHtml('<p>This is an html.</p>')
            ->withTags($tags)
        ;
    }
```
