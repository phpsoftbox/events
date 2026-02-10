# Quick Start

```php
use PhpSoftBox\Events\EventDispatcher;

$events = new EventDispatcher();

$events->listen(\App\Events\UserRegistered::class, function ($event): void {
    // обработка
});

$events->dispatch(new \App\Events\UserRegistered(10));
```

Если событие реализует `StoppableEventInterface`, распространение можно остановить
внутри слушателя.
