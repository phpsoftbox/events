# Listeners

## Регистрация слушателей

Слушателем может быть:
- замыкание;
- invokable‑класс;
- класс с методом `handle`;
- массив `[ClassName::class, 'method']`.

```php
$events->listen(UserRegistered::class, function (UserRegistered $event): void {
    // ...
});

$events->listen(UserRegistered::class, WelcomeEmailListener::class);
$events->listen(UserRegistered::class, [AuditListener::class, 'onUserRegistered']);
```

## Атрибут ListenTo

```php
use PhpSoftBox\Events\Attributes\ListenTo;

#[ListenTo(UserRegistered::class)]
final class WelcomeEmailListener
{
    public function handle(UserRegistered $event): void {}
}

final class AuditListener
{
    #[ListenTo(UserRegistered::class)]
    public function onUserRegistered(UserRegistered $event): void {}
}
```

## Атрибуты

Атрибут `ListenTo` помогает описывать связь события и слушателя.
Регистрацию в реестр нужно выполнить вручную или через конфигурацию.

## Конфигурация config/events

Можно описать слушателей в нескольких файлах через `EventLoader`.

definitions.php:
```php
<?php

declare(strict_types=1);

use PhpSoftBox\Config\Path\PathInterface;
use PhpSoftBox\Events\EventDispatcher;
use PhpSoftBox\Events\EventLoader;
use Psr\Container\ContainerInterface;

use function DI\factory;
use function is_dir;
use function is_file;

return [
    EventDispatcher::class => factory(static function (ContainerInterface $container): EventDispatcher {
        $dispatcher = new EventDispatcher(container: $container);

        $configDir = dirname(__DIR__) . '/events';

        if ($container->has(PathInterface::class)) {
            $path = $container->get(PathInterface::class);
            $configDir = $path->createPath('config/events');
        }

        if (is_dir($configDir)) {
            new EventLoader($configDir)->load($dispatcher);
        }

        return $dispatcher;
    }),
];

```

```php
use PhpSoftBox\\Events\\EventDispatcher;

return static function (EventDispatcher $events): void {
    $events->listen(App\\Events\\UserRegistered::class, App\\Listeners\\SendWelcomeEmail::class);
};
```

Например:
- `./config/events/app.php` — базовые слушатели приложения;
- `./config/events/users.php` — слушатели событий пользователя;
- `./config/events/email.php` — слушатели, связанные с почтой.
