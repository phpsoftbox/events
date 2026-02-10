<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Tests\Fixtures\Listeners;

use PhpSoftBox\Events\Attributes\ListenTo;
use PhpSoftBox\Events\Tests\Fixtures\Events\UserRegistered;

#[ListenTo(UserRegistered::class)]
final class WelcomeEmailListener
{
    public static int $hits = 0;

    public function handle(UserRegistered $event): void
    {
        self::$hits++;
    }
}
