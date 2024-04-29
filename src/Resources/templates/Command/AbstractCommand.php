<?php
declare(strict_types=1);

namespace App\Command;

use App\Contracts\Command\CommandInterface;
use App\Contracts\Logger\LoggerInterface;
use Psr\Clock\ClockInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

abstract class AbstractCommand extends Command implements CommandInterface
{
    private static string $STOPWATCH_EXECUTION = 'execution';
    private static string $STOPWATCH_EXECUTION_PRE_CONDITIONS = 'pre_conditions';
    private static string $STOPWATCH_EXECUTION_POST_CONDITIONS = 'post_conditions';
    private static string $STOPWATCH_EXECUTION_FAIL_FAST = 'fail_fast';
    private static string $STOPWATCH_EXECUTION_EXECUTE = 'execute';
    private SymfonyStyle $io;
    protected bool $force;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ClockInterface  $clock,
        ?string                          $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption('force', 'f', null, 'Force execution');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->force = true === $input->getOption('force');
    }

    /**
     * @return void
     */
    public function interact(InputInterface $input, OutputInterface $output)
    {
    }

    abstract public function title(): string;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $currentDateTime = $this->clock->now();
        $stopwatch = new Stopwatch();
        $stopwatch->start(self::$STOPWATCH_EXECUTION);

        $this->io->section($this->title());

        $this->io->writeln([
            sprintf('<comment>>  </comment>Started at <info>%s</info>', $currentDateTime->format('Y-m-d H:i:s')),
        ]);

        $this->logger->start();
        try {
            $stopwatch->start(self::$STOPWATCH_EXECUTION_PRE_CONDITIONS);
            $this->preConditionsChecks($input, $this->io);
            $stopwatch->stop(self::$STOPWATCH_EXECUTION_PRE_CONDITIONS);

            $stopwatch->start(self::$STOPWATCH_EXECUTION_FAIL_FAST);
            $this->failFast($input, $this->io);
            $stopwatch->stop(self::$STOPWATCH_EXECUTION_FAIL_FAST);

            $stopwatch->start(self::$STOPWATCH_EXECUTION_EXECUTE);
            $this->doExecute($input, $this->io);
            $stopwatch->stop(self::$STOPWATCH_EXECUTION_EXECUTE);

            $stopwatch->start(self::$STOPWATCH_EXECUTION_POST_CONDITIONS);
            $this->postConditionsChecks($input, $this->io);
            $stopwatch->stop(self::$STOPWATCH_EXECUTION_POST_CONDITIONS);

            $this->logger->success();
            $this->writeSuccessMessage($this->io);
            $this->logger->end();
            $stopwatch->stop(self::$STOPWATCH_EXECUTION);
            $this->writeLogs($input, $stopwatch);
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->logger->exception($e);
            $this->logger->end();
            $stopwatch->stop(self::$STOPWATCH_EXECUTION);
            $this->writeLogs($input, $stopwatch);
            $this->io->error($e->getMessage());
            return Command::FAILURE;
        }
    }

    private function writeLogs(InputInterface $input, Stopwatch $stopwatch): void
    {
        $this->io->section($this->title().' (logs)');

        if ($input->getOption('force')) {
            $this->io->warning('Execution forced!');
        }

        $sections = [
            self::$STOPWATCH_EXECUTION,
        ];

        if ($input->getOption('verbose')) {
            $sections = [
                self::$STOPWATCH_EXECUTION,
                self::$STOPWATCH_EXECUTION_PRE_CONDITIONS,
                self::$STOPWATCH_EXECUTION_FAIL_FAST,
                self::$STOPWATCH_EXECUTION_EXECUTE,
                self::$STOPWATCH_EXECUTION_POST_CONDITIONS,
            ];
        }

        $this->io->table(['Section', 'Started', 'Ended', 'Duration', 'Memory'], array_map(function ($section) use ($stopwatch) {
            $eventsNames = array_keys(current($stopwatch->getSections())->getEvents());

            if (!in_array($section, $eventsNames)) {
                return [$section, 'N/A', 'N/A', 'N/A', 'N/A'];
            }

            $event = $stopwatch->getEvent($section);
            $dateTime = new \DateTime();
            $originInSec = (int)($event->getOrigin() / 1000);
            $durationInSec = (int)($event->getDuration() / 1000);
            $endInSec = $originInSec + $durationInSec;

            return [
                $section,
                (clone $dateTime)->setTimestamp($originInSec)->format('Y-m-d H:i:s'),
                (clone $dateTime)->setTimestamp($endInSec)->format('Y-m-d H:i:s'),
                sprintf('%d ms', $event->getDuration()),
                sprintf('%.2F MiB', $event->getMemory() / 1024 / 1024),
            ];
        }, $sections));
    }

    protected function writeSuccessMessage(SymfonyStyle $io): void
    {
        $io->newLine();
        $io->writeln(' <bg=green;fg=white>          </>');
        $io->writeln(' <bg=green;fg=white> Success! </>');
        $io->writeln(' <bg=green;fg=white>          </>');
        $io->newLine();
    }

    protected function preConditionsChecks(InputInterface $input, SymfonyStyle $io): void
    {
    }

    protected function failFast(InputInterface $input, SymfonyStyle $io): void
    {
    }

    protected function postConditionsChecks(InputInterface $input, SymfonyStyle $io): void
    {
    }
}
