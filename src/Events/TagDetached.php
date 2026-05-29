<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Spatie\Tags\Tag;

/**
 * Fired when a tag is detached from a taggable entity (e.g. user clicks
 * the chip × in TagAutocomplete or host calls `detachTags`).
 *
 * The tag itself remains in the database; only the pivot link is removed.
 */
class TagDetached
{
    use Dispatchable;

    public function __construct(
        public readonly Tag $tag,
        public readonly Model $taggable,
    ) {}
}
