<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Cli;

use PhpSoftBox\CliApp\Runner\RunnerInterface;
use PhpSoftBox\CodeGenerator\Cli\AbstractMakeCommandHandler;
use PhpSoftBox\CodeGenerator\CodeGenerator;
use PhpSoftBox\CodeGenerator\GeneratorTarget;

final class MakeEventHandler extends AbstractMakeCommandHandler
{
    protected function missingNameMessage(): string
    {
        return 'Имя события не задано.';
    }

    protected function successMessage(GeneratorTarget $target): string
    {
        return 'Создано событие: ' . $target->path;
    }

    protected function renderEvent(RunnerInterface $runner, GeneratorTarget $target): string
    {
        $generator = new CodeGenerator();

        return $generator->renderClass(
            className: $target->className,
            namespace: $target->namespace,
            final: true,
        );
    }
}
