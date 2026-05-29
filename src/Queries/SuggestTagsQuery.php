<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Queries;

use Illuminate\Support\Collection;
use SlashDw\TaggingKit\Contracts\TagTypeContract;
use SlashDw\TaggingKit\Models\Tag;

/**
 * Suggest tags matching a query string within a given tag type.
 *
 * Faz 0 scaffolding only — Faz 1A will add:
 *  - Multi-locale JSON LIKE expression via `Support\JsonLocaleQuery`
 *  - Cross-domain shared type expansion via `SharedTypesResolverContract`
 *  - Sort strategy (use_count_desc / alphabetic / recent / mixed / custom)
 *  - Visibility scope (inherited from Tag model global scope)
 *  - Limit clamping (max_limit from config)
 */
class SuggestTagsQuery
{
    /**
     * @return Collection<int, Tag>
     */
    public function execute(
        string $q,
        TagTypeContract $tagType,
        int $limit = 10,
        ?string $locale = null,
    ): Collection {
        // Faz 1A: implementation
        return new Collection;
    }
}
