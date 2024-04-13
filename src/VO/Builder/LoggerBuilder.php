<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\VO\Builder;

use Atournayre\Bundle\MakerBundle\VO\FileDefinition;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\PhpFile;
use App\Contracts\Logger\LoggerInterface;
use App\Logger\AbstractLogger;

class LoggerBuilder extends AbstractBuilder
{
    public static function build(FileDefinition $fileDefinition): self
    {
        $file = new PhpFile;
        $file->addComment('This file has been auto-generated');
        $file->setStrictTypes();
        $file->addClass($fileDefinition->fullName())
            ->setFinal()
            ->setExtends(AbstractLogger::class)
            ->addImplement(LoggerInterface::class)
        ;

        return (new self($fileDefinition))
            ->withFile($file)
            ->withMethodException()
            ->withMethodByName('error')
            ->withMethodByName('emergency')
            ->withMethodByName('alert')
            ->withMethodByName('critical')
            ->withMethodByName('warning')
            ->withMethodByName('notice')
            ->withMethodByName('info')
            ->withMethodByName('debug')
            ->withMethodLog()
            ->withMethodWithInfoLog('start', 'start')
            ->withMethodWithInfoLog('end', 'end')
            ->withMethodWithInfoLog('success', 'success')
            ->withMethodWithInfoLog('failFast', 'fail fast')
        ;
    }

    private function withMethodException(): self
    {
        $clone = clone $this;
        $fullName = $clone->fileDefinition->fullName();
        $classes = $clone->file->getClasses();
        $class = $classes[$fullName];

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

        $class->addMember($method);

        return $clone;
    }

    private function withMethodByName(string $name): self
    {
        $clone = clone $this;
        $fullName = $clone->fileDefinition->fullName();
        $classes = $clone->file->getClasses();
        $class = $classes[$fullName];

        $method = new Method($name);
        $method->setPublic()->addParameter('message')->setType('\Stringable|string');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');

        $methodBody = '$this->logger->'.$name.'($this->prefixMessage($this->getLoggerIdentifier(), $message), $context);';

        $method->setBody($methodBody);

        $class->addMember($method);

        return $clone;
    }

    private function withMethodLog(): self
    {
        $clone = clone $this;
        $fullName = $clone->fileDefinition->fullName();
        $classes = $clone->file->getClasses();
        $class = $classes[$fullName];

        $method = new Method('log');
        $method->setPublic();
        $method->addParameter('level');
        $method->addParameter('message')->setType('\Stringable|string');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');

        $methodBody = '$this->logger->log($level, $this->prefixMessage($this->getLoggerIdentifier(), $message), $context);';

        $method->setBody($methodBody);

        $class->addMember($method);

        return $clone;
    }

    private function withMethodWithInfoLog(string $name, string $message): self
    {
        $clone = clone $this;
        $fullName = $clone->fileDefinition->fullName();
        $classes = $clone->file->getClasses();
        $class = $classes[$fullName];

        $method = new Method($name);
        $method->setPublic();
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');

        $methodBody = '$this->logger->info($this->prefixMessage($this->getLoggerIdentifier(), \''.$message.'\'), $context);';

        $method->setBody($methodBody);

        $class->addMember($method);

        return $clone;
    }
}
