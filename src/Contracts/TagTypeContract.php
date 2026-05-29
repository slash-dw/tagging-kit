<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Contracts;

use BackedEnum;

/**
 * Host implements its own concrete int-backed enum
 * (e.g. `enum TagType: int implements TagTypeContract`) to define
 * domain-specific tag types.
 *
 * Extends `BackedEnum` so the native `->value` (int) is available everywhere
 * a tag type value is needed — no separate `value()` method (which would
 * collide with the reserved enum `value` property).
 *
 * Recommended numeric range discipline:
 *  - 1xx-9xx: per-module ranges (e.g. 1xx = Module A, 2xx = Module B)
 *  - xx9 (e.g. 199, 299): cross-category "shared" types within a module
 */
interface TagTypeContract extends BackedEnum
{
    /**
     * Slug representation written to Spatie native `type` VARCHAR column
     * (kept in sync by Tag observer for Spatie-internal scope compatibility).
     * Example: `library-kitchen`, `material-supplier`.
     */
    public function slug(): string;

    /**
     * Human-readable label (typically `__('enum/TagType.' . $this->name)`).
     */
    public function label(): string;
}
