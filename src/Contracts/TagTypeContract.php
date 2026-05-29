<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Contracts;

/**
 * Host implements its own concrete enum (e.g. `TagType: int implements TagTypeContract`)
 * to define domain-specific tag types. Kit accepts the contract everywhere a tag type
 * is expected (suggest queries, model casts, etc.).
 *
 * Recommended numeric range discipline:
 *  - 1xx-9xx: per-module ranges (e.g. 1xx = Module A, 2xx = Module B)
 *  - xx9 (e.g. 199, 299): cross-category "shared" types within a module
 */
interface TagTypeContract
{
    /**
     * The integer enum value persisted in the `tag_type` column.
     */
    public function value(): int;

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
