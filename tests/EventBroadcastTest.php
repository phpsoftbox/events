<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Tests;

use PhpSoftBox\Events\EventDispatcher;
use PhpSoftBox\Events\Tests\Fixtures\Events\BroadcastUserRegistered;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(EventDispatcher::class)]
final class EventBroadcastTest extends TestCase
{
    /**
     * Проверяет отправку события в Broadcaster.
     */
    #[Test]
    public function testBroadcastPublishesMessage(): void
    {
        $dispatcher  = new EventDispatcher();
        $broadcaster = new class () {
            public array $calls = [];

            public function publish(string $channel, string $event, mixed $data = null): void
            {
                $this->calls[] = [$channel, $event, $data];
            }
        };

        $dispatcher->setBroadcaster($broadcaster);
        $dispatcher->broadcast(new BroadcastUserRegistered(10));

        $this->assertSame(
            [['users', 'user.registered', ['userId' => 10]]],
            $broadcaster->calls,
        );
    }
}
