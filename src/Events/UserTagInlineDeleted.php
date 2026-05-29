<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Spatie\Tags\Tag;

/**
 * Fired when a user deletes their own tag via the autocomplete dropdown
 * 🗑 affordance (DELETE /api/tagging/tags/{id}).
 *
 * The `$tag` instance is a snapshot — the underlying DB row no longer exists.
 * `TagDeleted` also fires; this event distinguishes user-initiated inline deletion.
 */
class UserTagInlineDeleted
{
    use Dispatchable;

    public function __construct(
        public readonly Tag $tag,
        public readonly string|int|null $actorId,
    ) {}
}
