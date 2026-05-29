<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Spatie\Tags\Tag;

/**
 * Fired when a tag is attached to a taggable entity (typically via
 * Spatie's `attachTags` macro on the host model).
 *
 * Useful for search index updates, denormalized aggregates, broadcast, etc.
 */
class TagAttached
{
    use Dispatchable;

    public function __construct(
        public readonly Tag $tag,
        public readonly Model $taggable,
    ) {}
}
