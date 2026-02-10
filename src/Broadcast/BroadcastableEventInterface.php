<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Broadcast;

interface BroadcastableEventInterface
{
    public function broadcastChannel(): string;

    public function broadcastEvent(): string;

    public function broadcastPayload(): mixed;
}
