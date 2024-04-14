<?php
declare(strict_types=1);

namespace App\Service\Session;

use App\Contracts\Service\Session\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;

final class SymfonyFlashBagService implements FlashBagInterface
{
    public function __construct(
        private readonly FlashBag $flashBag,
    )
    {
    }

    public function success(string|array $message): void
    {
        $this->displayMessages(FlashBagInterface::SUCCESS, $message);
    }

    public function warning(string|array $message): void
    {
        $this->displayMessages(FlashBagInterface::WARNING, $message);
    }

    public function error(string|array $message): void
    {
        $this->displayMessages(FlashBagInterface::ERROR, $message);
    }

    public function info(string|array $message): void
    {
        $this->displayMessages(FlashBagInterface::INFO, $message);
    }

    public function fromException(\Exception $exception): void
    {
        $this->error($exception->getMessage());
    }

    private function displayMessages(string $type, string|array $message): void
    {
        $messages = is_string($message) ? [$message] : $message;

        foreach ($messages as $message) {
            $this->flashBag->add($type, $message);
        }
    }
}
