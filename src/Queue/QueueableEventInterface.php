<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Queue;

interface QueueableEventInterface
{
    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array;

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromPayload(array $payload): static;
}
