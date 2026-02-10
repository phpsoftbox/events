<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Tests\Fixtures\Events;

use PhpSoftBox\Events\Queue\QueueableEventInterface;

final readonly class QueuedUserRegistered implements QueueableEventInterface
{
    public function __construct(
        public int $userId,
    ) {
    }

    public function toPayload(): array
    {
        return ['userId' => $this->userId];
    }

    public static function fromPayload(array $payload): static
    {
        return new self((int) ($payload['userId'] ?? 0));
    }
}
