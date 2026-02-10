<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Cli;

use PhpSoftBox\CliApp\Runner\RunnerInterface;
use PhpSoftBox\CodeGenerator\Cli\AbstractMakeCommandHandler;
use PhpSoftBox\CodeGenerator\CodeGenerator;
use PhpSoftBox\CodeGenerator\GeneratorTarget;

use function basename;
use function is_string;
use function ltrim;
use function str_replace;
use function trim;

final class MakeListenerHandler extends AbstractMakeCommandHandler
{
    protected function missingNameMessage(): string
    {
        return 'Имя слушателя не задано.';
    }

    protected function successMessage(GeneratorTarget $target): string
    {
        return 'Создан слушатель: ' . $target->path;
    }

    protected function renderEvent(RunnerInterface $runner, GeneratorTarget $target): string
    {
        $eventClass = $this->normalizeEventClass($runner->request()->option('event'));
        $generator  = new CodeGenerator();

        $uses       = [];
        $attributes = [];
        $paramType  = 'object';

        if ($eventClass !== null) {
            $uses[]       = 'PhpSoftBox\\Events\\Attributes\\ListenTo';
            $uses[]       = $eventClass;
            $shortName    = basename(str_replace('\\', '/', $eventClass));
            $attributes[] = '#[ListenTo(' . $shortName . '::class)]';
            $paramType    = $shortName;
        }

        $bodyLines = [
            'public function handle(' . $paramType . ' $event): void',
            '{',
            '}',
        ];

        return $generator->renderClass(
            className: $target->className,
            namespace: $target->namespace,
            uses: $uses,
            classAttributes: $attributes,
            bodyLines: $bodyLines,
        );
    }

    private function normalizeEventClass(mixed $eventClass): ?string
    {
        if (!is_string($eventClass) || trim($eventClass) === '') {
            return null;
        }

        return ltrim(trim($eventClass), '\\');
    }
}
