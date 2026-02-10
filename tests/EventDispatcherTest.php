<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Tests;

use PhpSoftBox\Events\EventDispatcher;
use PhpSoftBox\Events\Tests\Fixtures\Events\DomainEventInterface;
use PhpSoftBox\Events\Tests\Fixtures\Events\StoppableEvent;
use PhpSoftBox\Events\Tests\Fixtures\Events\UserRegistered;
use PhpSoftBox\Events\Tests\Fixtures\Listeners\WelcomeEmailListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(EventDispatcher::class)]
final class EventDispatcherTest extends TestCase
{
    /**
     * Проверяет порядок выполнения слушателей.
     */
    #[Test]
    public function testDispatchCallsListenersInOrder(): void
    {
        $dispatcher = new EventDispatcher();
        $calls      = [];

        $dispatcher->listen(UserRegistered::class, function () use (&$calls): void {
            $calls[] = 'first';
        });
        $dispatcher->listen(UserRegistered::class, function () use (&$calls): void {
            $calls[] = 'second';
        });

        $dispatcher->dispatch(new UserRegistered(10));

        $this->assertSame(['first', 'second'], $calls);
    }

    /**
     * Проверяет остановку распространения события.
     */
    #[Test]
    public function testDispatchStopsPropagation(): void
    {
        $dispatcher = new EventDispatcher();
        $calls      = [];

        $dispatcher->listen(StoppableEvent::class, function (StoppableEvent $event) use (&$calls): void {
            $calls[] = 'first';
            $event->stop();
        });
        $dispatcher->listen(StoppableEvent::class, function () use (&$calls): void {
            $calls[] = 'second';
        });

        $dispatcher->dispatch(new StoppableEvent());

        $this->assertSame(['first'], $calls);
    }

    /**
     * Проверяет, что слушатель интерфейса получает событие.
     */
    #[Test]
    public function testListenerForInterfaceIsResolved(): void
    {
        $dispatcher = new EventDispatcher();
        $calls      = 0;

        $dispatcher->listen(DomainEventInterface::class, function () use (&$calls): void {
            $calls++;
        });

        $dispatcher->dispatch(new UserRegistered(5));

        $this->assertSame(1, $calls);
    }

    /**
     * Проверяет, что класс-слушатель с handle() резолвится корректно.
     */
    #[Test]
    public function testListenerClassIsResolved(): void
    {
        $dispatcher                 = new EventDispatcher();
        WelcomeEmailListener::$hits = 0;

        $dispatcher->listen(UserRegistered::class, WelcomeEmailListener::class);
        $dispatcher->dispatch(new UserRegistered(7));

        $this->assertSame(1, WelcomeEmailListener::$hits);
    }
}
