<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Tests\Fixtures\Events;

use Psr\EventDispatcher\StoppableEventInterface;

final class StoppableEvent implements StoppableEventInterface
{
    private bool $stopped = false;

    public function stop(): void
    {
        $this->stopped = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }
}
