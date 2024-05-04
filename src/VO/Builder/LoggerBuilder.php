<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use App\Contracts\Logger\LoggerInterface;
use App\Logger\AbstractLogger;

class LoggerBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): static
    {
        return static::create($fileDefinition)
            ->createFile()
            ->extends(AbstractLogger::class)
            ->addImplement(LoggerInterface::class)
            ->withUse(LoggerInterface::class)
            ->addMember(self::methodException())
            ->addMember(self::methodByName('error'))
            ->addMember(self::methodByName('emergency'))
            ->addMember(self::methodByName('alert'))
            ->addMember(self::methodByName('critical'))
            ->addMember(self::methodByName('warning'))
            ->addMember(self::methodByName('notice'))
            ->addMember(self::methodByName('info'))
            ->addMember(self::methodByName('debug'))
            ->addMember(self::methodLog())
            ->addMember(self::methodWithInfoLog('start', 'start'))
            ->addMember(self::methodWithInfoLog('end', 'end'))
            ->addMember(self::methodWithInfoLog('success', 'success'))
            ->addMember(self::methodWithInfoLog('failFast', 'fail fast'))
        ;
    }

    private static function methodException(): Method
    {
        $method = new Method('exception');
        $method->setPublic();
        $method->addParameter('exception')->setType('\Exception');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');

        $methodBody = <<<'PHP'
$context['exception'] = $exception;
$this->logger->error($exception->getMessage(), $context);
PHP;

        $method->setBody($methodBody);

        return $method;
    }

    private static function methodByName(string $name): Method
    {
        $method = new Method($name);
        $method->setPublic()->addParameter('message')->setType('\Stringable|string');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');

        $methodBody = '$this->logger->'.$name.'($this->prefixMessage($this->getLoggerIdentifier(), $message), $context);';

        $method->setBody($methodBody);

        return $method;
    }

    private static function methodLog(): Method
    {
        $method = new Method('log');
        $method->setPublic();
        $method->addParameter('level');
        $method->addParameter('message')->setType('\Stringable|string');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');

        $methodBody = '$this->logger->log($level, $this->prefixMessage($this->getLoggerIdentifier(), $message), $context);';

        $method->setBody($methodBody);

        return $method;
    }

    private static function methodWithInfoLog(string $name, string $message): Method
    {
        $methodBody = '$this->logger->info($this->prefixMessage($this->getLoggerIdentifier(), \''.$message.'\'), $context);';

        $method = new Method($name);
        $method->setPublic();
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');
        $method->setBody($methodBody);

        return $method;
    }
}
