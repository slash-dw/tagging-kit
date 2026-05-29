<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Spatie\Tags\Tag;

/**
 * Fired when a tag is created (both system tags by admin and user inline tags).
 * Use `UserTagInlineCreated` if you need to distinguish inline user tag creation.
 */
class TagCreated
{
    use Dispatchable;

    public function __construct(public readonly Tag $tag) {}
}
