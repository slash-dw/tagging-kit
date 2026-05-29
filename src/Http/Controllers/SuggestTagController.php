<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Http\Controllers;

use Illuminate\Http\JsonResponse;
use SlashDw\TaggingKit\Http\Requests\SuggestTagsRequest;
use SlashDw\TaggingKit\Queries\SuggestTagsQuery;
use SlashDw\TaggingKit\Support\TagTypeResolver;

/**
 * GET /api/tagging/suggest?q=...&tag_type=...&limit=...
 *
 * Returns tag suggestions for autocomplete. Visibility scope (system + own
 * tenant) is enforced by the Tag model global scope.
 */
class SuggestTagController
{
    public function __invoke(
        SuggestTagsRequest $request,
        SuggestTagsQuery $query,
        TagTypeResolver $tagTypeResolver,
    ): JsonResponse {
        /** @var array{q: string, tag_type: int|string, limit?: int|string} $validated */
        $validated = $request->validated();

        $tagType = $tagTypeResolver->fromInt((int) $validated['tag_type']);
        $limit = (int) ($validated['limit'] ?? config('tagging-kit.suggest.default_limit', 10));

        $tags = $query->execute(
            q: $validated['q'],
            tagType: $tagType,
            limit: $limit,
        );

        return response()->json([
            'data' => $tags->map(static fn ($tag): array => [
                'id' => $tag->getKey(),
                'name' => $tag->name,
                'tag_type' => $tag->getAttribute('tag_type'),
                'tenant_id' => $tag->getAttribute('tenant_id'),
                'use_count' => (int) $tag->getAttribute('taggables_count'),
            ])->all(),
        ]);
    }
}
