<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Queue;

use PhpSoftBox\Events\EventDispatcher;
use PhpSoftBox\Events\EventException;
use PhpSoftBox\Queue\QueueJob;
use PhpSoftBox\Queue\QueueJobHandlerInterface;

use function is_a;
use function is_array;
use function is_string;

final readonly class EventQueueJobHandler implements QueueJobHandlerInterface
{
    public function __construct(
        private EventDispatcher $dispatcher,
    ) {
    }

    public function handle(mixed $payload, QueueJob $job): void
    {
        if (!is_array($payload)) {
            throw new EventException('Event payload must be an array.');
        }

        $eventClass   = $payload['event'] ?? null;
        $eventPayload = $payload['payload'] ?? null;

        if (!is_string($eventClass) || $eventClass === '') {
            throw new EventException('Event payload does not contain event class.');
        }

        if (!is_array($eventPayload)) {
            throw new EventException('Event payload is invalid.');
        }

        if (!is_a($eventClass, QueueableEventInterface::class, true)) {
            throw new EventException('Event class does not implement QueueableEventInterface.');
        }

        $event = $eventClass::fromPayload($eventPayload);

        $this->dispatcher->dispatch($event);
    }
}
