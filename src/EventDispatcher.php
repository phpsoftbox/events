<?php

declare(strict_types=1);

namespace PhpSoftBox\Events;

use PhpSoftBox\Events\Broadcast\BroadcastableEventInterface;
use PhpSoftBox\Events\Queue\QueueableEventInterface;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

use function array_merge;
use function array_unique;
use function array_values;
use function class_exists;
use function class_implements;
use function class_parents;
use function interface_exists;
use function is_a;
use function is_array;
use function is_callable;
use function is_object;
use function is_string;
use function method_exists;
use function trim;

final class EventDispatcher implements EventDispatcherInterface, ListenerProviderInterface
{
    /** @var array<class-string, list<callable|array|string>> */
    private array $listeners = [];
    private ?object $queue;
    private ?object $broadcaster;

    public function __construct(
        private readonly ?ContainerInterface $container = null,
        ?object $queue = null,
        ?object $broadcaster = null,
    ) {
        $this->queue       = $queue;
        $this->broadcaster = $broadcaster;
    }

    /**
     * @param class-string $event
     */
    public function listen(string $event, callable|array|string $listener): self
    {
        $event = trim($event);
        if ($event === '') {
            throw new EventException('Event name must be non-empty.');
        }

        $this->listeners[$event][] = $listener;

        return $this;
    }

    /**
     * @param array<class-string, list<callable|array|string>> $map
     */
    public function listenMany(array $map): self
    {
        foreach ($map as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this->listen($event, $listener);
            }
        }

        return $this;
    }

    public function dispatch(object $event): object
    {
        foreach ($this->getListenersForEvent($event) as $listener) {
            $listener($event);

            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }

    /**
     * Отправляет событие в очередь для отложенного выполнения.
     */
    public function dispatchLater(object $event, int $delaySeconds = 0): ?string
    {
        if (!$event instanceof QueueableEventInterface) {
            throw new EventException('Queued events must implement QueueableEventInterface.');
        }

        if ($this->queue === null) {
            throw new EventException('Queue is not configured for events.');
        }

        if (!interface_exists('PhpSoftBox\\Queue\\QueueInterface')) {
            throw new EventException('phpsoftbox/queue is required for queued events.');
        }

        if (!is_object($this->queue) || !is_a($this->queue, 'PhpSoftBox\\Queue\\QueueInterface')) {
            throw new EventException('Queue instance does not implement QueueInterface.');
        }

        if (!class_exists('PhpSoftBox\\Queue\\QueueJob')) {
            throw new EventException('phpsoftbox/queue is required for queued events.');
        }

        $payload = [
            'type'    => 'event',
            'event'   => $event::class,
            'payload' => $event->toPayload(),
        ];

        $jobClass = 'PhpSoftBox\\Queue\\QueueJob';
        /** @var object $job */
        $job = $jobClass::fromPayload($payload);
        if ($delaySeconds > 0 && method_exists($job, 'withDelay')) {
            $job = $job->withDelay($delaySeconds);
        }

        $this->queue->push($job);

        return method_exists($job, 'id') ? $job->id() : null;
    }

    /**
     * Возвращает зарегистрированные слушатели для события.
     *
     * @return iterable<callable>
     */
    public function getListenersForEvent(object $event): iterable
    {
        $types    = $this->eventTypes($event);
        $resolved = [];

        foreach ($types as $type) {
            foreach ($this->listeners[$type] ?? [] as $listener) {
                $resolved[] = $this->resolveListener($listener);
            }
        }

        return $resolved;
    }

    /**
     * Возвращает карту зарегистрированных слушателей (для CLI/отладки).
     *
     * @return array<class-string, list<callable|array|string>>
     */
    public function definitions(): array
    {
        return $this->listeners;
    }

    /**
     * Устанавливает очередь для отложенных событий.
     */
    public function setQueue(?object $queue): void
    {
        $this->queue = $queue;
    }

    /**
     * Устанавливает Broadcaster для отправки событий в сокет.
     */
    public function setBroadcaster(?object $broadcaster): void
    {
        $this->broadcaster = $broadcaster;
    }

    /**
     * Отправляет событие в Broadcaster.
     */
    public function broadcast(object $event): void
    {
        if (!$event instanceof BroadcastableEventInterface) {
            throw new EventException('Broadcast event must implement BroadcastableEventInterface.');
        }

        $channel   = trim($event->broadcastChannel());
        $eventName = trim($event->broadcastEvent());

        if ($channel === '' || $eventName === '') {
            throw new EventException('Broadcast channel and event must be non-empty.');
        }

        if ($this->broadcaster === null) {
            throw new EventException('Broadcaster is not configured for events.');
        }

        if (!method_exists($this->broadcaster, 'publish')) {
            throw new EventException('Broadcaster does not support publish() method.');
        }

        $this->broadcaster->publish($channel, $eventName, $event->broadcastPayload());
    }

    /**
     * @return list<class-string>
     */
    private function eventTypes(object $event): array
    {
        $class      = $event::class;
        $parents    = class_parents($event) ?: [];
        $interfaces = class_implements($event) ?: [];

        return array_values(array_unique(array_merge([$class], $parents, $interfaces)));
    }

    /**
     */
    private function resolveListener(callable|array|string $listener): callable
    {
        if (is_callable($listener)) {
            return $listener;
        }

        if (is_string($listener)) {
            $instance = $this->resolveClass($listener);

            if (is_callable($instance)) {
                return $instance;
            }

            if (method_exists($instance, 'handle')) {
                return [$instance, 'handle'];
            }

            throw new EventException('Listener class is not invokable: ' . $listener);
        }

        if (is_array($listener) && isset($listener[0], $listener[1])) {
            $target = $listener[0];
            $method = $listener[1];

            if (is_string($target)) {
                $target = $this->resolveClass($target);
            }

            if (!is_object($target)) {
                throw new EventException('Invalid listener target.');
            }

            return [$target, $method];
        }

        throw new EventException('Invalid listener definition.');
    }

    /**
     * @param class-string $class
     */
    private function resolveClass(string $class): object
    {
        if ($this->container !== null && $this->container->has($class)) {
            return $this->container->get($class);
        }

        if (!class_exists($class)) {
            throw new EventException('Listener class not found: ' . $class);
        }

        return new $class();
    }
}
