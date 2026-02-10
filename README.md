# PhpSoftBox Events

## About
`phpsoftbox/events` — компонент событий и слушателей на базе PSR-14. Позволяет регистрировать слушателей, диспатчить события, использовать атрибуты для описания связей и откладывать события через очередь.

Ключевые свойства:
- `EventDispatcher` с PSR-14 API
- слушатели на основе классов или замыканий
- `ListenTo` атрибут для описания слушателей
- отложенные события через `Queue`
- отправка событий в сокет через `Broadcaster`
- CLI-команды для просмотра и генерации событий/слушателей
- загрузка конфигураций слушателей из `config/events`

## Quick Start
```php
use PhpSoftBox\Events\EventDispatcher;

$events = new EventDispatcher();

$events->listen(\App\Events\UserRegistered::class, function ($event): void {
    // обработка
});

$events->dispatch(new \App\Events\UserRegistered(10));
```

## Оглавление
- [Документация](docs/index.md)
- [About](docs/01-about.md)
- [Quick Start](docs/02-quick-start.md)
- [Listeners](docs/03-listeners.md)
- [Очередь](docs/04-queue.md)
- [CLI](docs/05-cli.md)
- [DI](docs/06-di.md)
- [Broadcast](docs/07-broadcast.md)
