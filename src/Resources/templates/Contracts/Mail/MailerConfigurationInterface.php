<?php
declare(strict_types=1);

namespace App\Contracts\Mail;

use App\Type\Primitive\IntegerType;
use Atournayre\Types\EmailAddress;

interface MailerConfigurationInterface
{
    public function fromAddress(): ?EmailAddress;

    public function fromName(): ?string;

    public function replyToAddress(): ?EmailAddress;

    public function replyToName(): ?string;

    public function attachmentsMaxSize(): ?IntegerType;
}
