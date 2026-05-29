<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Spatie\Tags\Tag;

/**
 * Fired after a tag is hard-deleted (both admin system tag delete and
 * user inline tag delete). For soft-deletes, see model-level events.
 *
 * The `$tag` instance is a snapshot — the underlying DB row no longer exists.
 */
class TagDeleted
{
    use Dispatchable;

    public function __construct(public readonly Tag $tag) {}
}
