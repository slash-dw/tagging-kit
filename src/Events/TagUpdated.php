<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Spatie\Tags\Tag;

/**
 * Fired when a tag's attributes change (typically by admin update).
 *
 * @phpstan-type Changes array<string, mixed>
 */
class TagUpdated
{
    use Dispatchable;

    /**
     * @param  Changes  $changes  Dirty attributes (before save) keyed by column name.
     */
    public function __construct(
        public readonly Tag $tag,
        public readonly array $changes = [],
    ) {}
}
