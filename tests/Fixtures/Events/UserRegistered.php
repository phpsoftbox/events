<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Tests\Fixtures\Events;

final readonly class UserRegistered implements DomainEventInterface
{
    public function __construct(
        public int $userId,
    ) {
    }
}
