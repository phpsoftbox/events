# Очередь

Отложенные события работают через `phpsoftbox/queue`.

Событие должно реализовывать `QueueableEventInterface`:

```php
use PhpSoftBox\Events\Queue\QueueableEventInterface;

final class UserRegistered implements QueueableEventInterface
{
    public function __construct(public int $userId) {}

    public function toPayload(): array
    {
        return ['userId' => $this->userId];
    }

    public static function fromPayload(array $payload): static
    {
        return new self((int) $payload['userId']);
    }
}
```

Диспатч с задержкой:

```php
$events->dispatchLater(new UserRegistered(10), delaySeconds: 60);
```

Для обработки очереди используйте `EventQueueJobHandler`:

```php
use PhpSoftBox\Events\Queue\EventQueueJobHandler;
use PhpSoftBox\Queue\QueueJobHandlerInterface;

return new EventQueueJobHandler($events);
```
