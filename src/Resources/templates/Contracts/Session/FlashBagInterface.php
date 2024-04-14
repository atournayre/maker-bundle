<?php
declare(strict_types=1);

namespace App\Contracts\Session;

interface FlashBagInterface
{
    public const SUCCESS = 'success';
    public const WARNING = 'warning';
    public const ERROR = 'danger';
    public const INFO = 'info';

    public function success(string|array $message): void;

    public function warning(string|array $message): void;

    public function error(string|array $message): void;

    public function info(string|array $message): void;

    /**
     * Use with caution, this method is used to display error messages from exceptions.
     * @param \Exception $exception
     * @return void
     */
    public function fromException(\Exception $exception): void;
}
