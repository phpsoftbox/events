<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Cli;

use PhpSoftBox\CliApp\Command\ArgumentDefinition;
use PhpSoftBox\CliApp\Command\Command;
use PhpSoftBox\CliApp\Command\CommandRegistryInterface;
use PhpSoftBox\CliApp\Command\OptionDefinition;
use PhpSoftBox\CliApp\Loader\CommandProviderInterface;

final class EventCommandProvider implements CommandProviderInterface
{
    public function register(CommandRegistryInterface $registry): void
    {
        $registry->register(Command::define(
            name: 'event:list',
            description: 'Показать зарегистрированные события и слушатели',
            signature: [],
            handler: EventListHandler::class,
        ));

        $registry->register(Command::define(
            name: 'make:event',
            description: 'Создать класс события',
            signature: [
                new ArgumentDefinition(
                    name: 'name',
                    description: 'Namespace или путь (например, App\\Events\\UserRegistered)',
                    required: true,
                    type: 'string',
                ),
                new OptionDefinition(
                    name: 'path',
                    short: 'p',
                    description: 'Базовая директория для namespace (по умолчанию src)',
                    required: false,
                    default: 'src',
                    type: 'string',
                ),
                new OptionDefinition(
                    name: 'namespace',
                    short: 'n',
                    description: 'Базовый namespace (по умолчанию App)',
                    required: false,
                    default: 'App',
                    type: 'string',
                ),
            ],
            handler: MakeEventHandler::class,
        ));

        $registry->register(Command::define(
            name: 'make:listener',
            description: 'Создать слушатель события',
            signature: [
                new ArgumentDefinition(
                    name: 'name',
                    description: 'Namespace или путь (например, App\\Listeners\\SendWelcomeEmail)',
                    required: true,
                    type: 'string',
                ),
                new OptionDefinition(
                    name: 'event',
                    short: 'e',
                    description: 'Класс события для атрибута ListenTo',
                    required: false,
                    default: null,
                    type: 'string',
                ),
                new OptionDefinition(
                    name: 'path',
                    short: 'p',
                    description: 'Базовая директория для namespace (по умолчанию src)',
                    required: false,
                    default: 'src',
                    type: 'string',
                ),
                new OptionDefinition(
                    name: 'namespace',
                    short: 'n',
                    description: 'Базовый namespace (по умолчанию App)',
                    required: false,
                    default: 'App',
                    type: 'string',
                ),
            ],
            handler: MakeListenerHandler::class,
        ));
    }
}
