<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Spatie\Tags\Tag;

/**
 * Fired when a user creates a tag inline via `findOrCreateFromString` flow
 * (typically during template/entity save with autocomplete input).
 *
 * Distinguishes user-side inline creation from admin-side system tag creation
 * — both trigger `TagCreated` but only this event fires for user inline flow.
 */
class UserTagInlineCreated
{
    use Dispatchable;

    public function __construct(
        public readonly Tag $tag,
        public readonly string|int|null $actorId,
    ) {}
}
