<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Tests\Fixtures\Events;

use PhpSoftBox\Events\Broadcast\BroadcastableEventInterface;

final readonly class BroadcastUserRegistered implements BroadcastableEventInterface
{
    public function __construct(
        public int $userId,
    ) {
    }

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
