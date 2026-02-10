<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Tests;

use PhpSoftBox\Events\EventDispatcher;
use PhpSoftBox\Events\Queue\EventQueueJobHandler;
use PhpSoftBox\Events\Tests\Fixtures\Events\QueuedUserRegistered;
use PhpSoftBox\Queue\Drivers\InMemoryDriver;
use PhpSoftBox\Queue\QueueJob;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function class_exists;

#[CoversClass(EventDispatcher::class)]
#[CoversClass(EventQueueJobHandler::class)]
final class EventQueueTest extends TestCase
{
    /**
     * Проверяет отложенное событие через очередь.
     */
    #[Test]
    public function testDispatchLaterPushesJobAndHandles(): void
    {
        if (!class_exists(InMemoryDriver::class)) {
            $this->markTestSkipped('Queue package is not installed.');
        }

        $queue = new InMemoryDriver();

        $dispatcher = new EventDispatcher(queue: $queue);

        $handler = new EventQueueJobHandler($dispatcher);

        $hits = 0;
        $dispatcher->listen(QueuedUserRegistered::class, function () use (&$hits): void {
            $hits++;
        });

        $dispatcher->dispatchLater(new QueuedUserRegistered(10));

        $job = $queue->pop();
        $this->assertInstanceOf(QueueJob::class, $job);

        $handler->handle($job->payload(), $job);

        $this->assertSame(1, $hits);
    }
}
