<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Cli;

use PhpSoftBox\CliApp\Command\HandlerInterface;
use PhpSoftBox\CliApp\Response;
use PhpSoftBox\CliApp\Runner\RunnerInterface;
use PhpSoftBox\Events\EventDispatcher;

use function count;
use function is_array;
use function is_object;
use function is_string;

final readonly class EventListHandler implements HandlerInterface
{
    public function __construct(
        private EventDispatcher $dispatcher,
    ) {
    }

    public function run(RunnerInterface $runner): int|Response
    {
        $definitions = $this->dispatcher->definitions();
        if ($definitions === []) {
            $runner->io()->writeln('События не зарегистрированы.');

            return Response::SUCCESS;
        }

        $rows = [];
        foreach ($definitions as $event => $listeners) {
            foreach ($listeners as $listener) {
                $rows[] = [$event, $this->describeListener($listener)];
            }
        }

        $runner->io()->table(['Событие', 'Слушатель'], $rows);
        $runner->io()->writeln('Всего: ' . count($rows));

        return Response::SUCCESS;
    }

    private function describeListener(mixed $listener): string
    {
        if (is_string($listener)) {
            return $listener;
        }

        if (is_array($listener) && isset($listener[0], $listener[1])) {
            $target     = $listener[0];
            $method     = $listener[1];
            $targetName = is_object($target) ? $target::class : (string) $target;

            return $targetName . '::' . $method;
        }

        if (is_object($listener)) {
            return $listener::class;
        }

        return 'Closure';
    }
}
