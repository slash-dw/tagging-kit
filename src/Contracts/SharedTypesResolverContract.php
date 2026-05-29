<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Contracts;

use SlashDw\TaggingKit\Support\DefaultSharedTypesResolver;

/**
 * Expands a single tag type into an array of related tag types that should
 * be included in suggest query results.
 *
 * Use case: cross-domain "shared" tags. For example, a host may have multiple
 * domain-specific tag types (kitchen, bathroom) and a universal "shared" type
 * (colors, materials) that should appear in suggestions for any domain.
 *
 * Default implementation ({@see DefaultSharedTypesResolver})
 * returns only the requested type — hosts override for cross-domain expansion.
 */
interface SharedTypesResolverContract
{
    /**
     * @return array<int, int> List of tag_type integer values to include in suggest.
     */
    public function expand(TagTypeContract $type): array;
}
