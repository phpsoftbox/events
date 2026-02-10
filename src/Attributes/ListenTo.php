<?php

declare(strict_types=1);

namespace PhpSoftBox\Events\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class ListenTo
{
    /**
     * @param class-string $event
     */
    public function __construct(
        public string $event,
        public ?string $method = null,
    ) {
    }
}
