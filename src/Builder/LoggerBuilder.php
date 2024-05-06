<?php
declare(strict_types=1);

namespace Atournayre\Bundle\MakerBundle\Builder;

use App\Contracts\Logger\LoggerInterface;
use App\Logger\AbstractLogger;
use Atournayre\Bundle\MakerBundle\Config\LoggerMakerConfiguration;
use Atournayre\Bundle\MakerBundle\Contracts\MakerConfigurationInterface;
use Atournayre\Bundle\MakerBundle\VO\PhpFileDefinition;
use Nette\PhpGenerator\Method;

final class LoggerBuilder extends AbstractBuilder
{
    public function supports(string $makerConfigurationClassName): bool
    {
        return $makerConfigurationClassName === LoggerMakerConfiguration::class;
    }

    public function createInstance(MakerConfigurationInterface|LoggerMakerConfiguration $makerConfiguration): PhpFileDefinition
    {
        return parent::createInstance($makerConfiguration)
            ->setExtends(AbstractLogger::class)
            ->setImplements([
                LoggerInterface::class,
            ])
            ->setUses([
                LoggerInterface::class,
            ])
            ->setMethods([
                $this->methodException(),
                $this->methodByName('error'),
                $this->methodByName('emergency'),
                $this->methodByName('alert'),
                $this->methodByName('critical'),
                $this->methodByName('warning'),
                $this->methodByName('notice'),
                $this->methodByName('info'),
                $this->methodByName('debug'),
                $this->methodLog(),
                $this->methodWithInfoLog('start', 'start'),
                $this->methodWithInfoLog('end', 'end'),
                $this->methodWithInfoLog('success', 'success'),
                $this->methodWithInfoLog('failFast', 'fail fast'),
            ])
        ;
    }

    private function methodException(): Method
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

    private function methodByName(string $name): Method
    {
        $method = new Method($name);
        $method->setPublic()->addParameter('message')->setType('\Stringable|string');
        $method->addParameter('context')->setType('array')->setDefaultValue([]);
        $method->setReturnType('void');

        $methodBody = '$this->logger->'.$name.'($this->prefixMessage($this->getLoggerIdentifier(), $message), $context);';

        $method->setBody($methodBody);

        return $method;
    }

    private function methodLog(): Method
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

    private function methodWithInfoLog(string $name, string $message): Method
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
