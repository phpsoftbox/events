# Broadcast

Для отправки событий в WebSocket используйте компонент `phpsoftbox/broadcaster`.

Событие должно реализовывать `BroadcastableEventInterface`:

```php
use PhpSoftBox\Events\Broadcast\BroadcastableEventInterface;

final class UserRegistered implements BroadcastableEventInterface
{
    public function __construct(public int $userId) {}

    public function broadcastChannel(): string
    {
        return 'users';
    }

    public function broadcastEvent(): string
    {
        return 'user.registered';
    }

    public function broadcastPayload(): mixed
    {
        return ['userId' => $this->userId];
    }
}
```

Диспатчер должен получить клиент Broadcaster (например, `PushrClient`):

```php
use PhpSoftBox\Broadcaster\Pushr\PushrClient;

$client = new PushrClient('127.0.0.1', 8080, 'app-1', 'secret-1');
$client->connect();

$events->setBroadcaster($client);
$events->broadcast(new UserRegistered(10));
```
