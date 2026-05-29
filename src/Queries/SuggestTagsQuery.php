<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use SlashDw\TaggingKit\Contracts\SharedTypesResolverContract;
use SlashDw\TaggingKit\Contracts\TagTypeContract;
use SlashDw\TaggingKit\Models\Tag;
use SlashDw\TaggingKit\Support\JsonLocaleQuery;

/**
 * Suggest tags matching a query string within a tag type.
 *
 * Behavior:
 *  - Expands the requested tag type via SharedTypesResolverContract (cross-domain)
 *  - Multi-locale JSON LIKE (DB-driver-aware via JsonLocaleQuery)
 *  - Visibility scope applied automatically (Tag model global scope:
 *    system tags + own tenant tags)
 *  - use_count via correlated subquery on the taggables pivot
 *  - Sort strategy: use_count_desc | alphabetic | recent | mixed | custom
 *  - Limit clamped to suggest.max_limit
 */
class SuggestTagsQuery
{
    public function __construct(
        private readonly SharedTypesResolverContract $sharedTypesResolver,
    ) {}

    /**
     * @return Collection<int, Tag>
     */
    public function execute(
        string $q,
        TagTypeContract $tagType,
        int $limit = 10,
        ?string $locale = null,
    ): Collection {
        $locale ??= $this->defaultLocale();
        $limit = $this->clampLimit($limit);

        $types = $this->sharedTypesResolver->expand($tagType);
        $localeColumn = JsonLocaleQuery::localeColumn('name', $locale);

        $query = Tag::query()
            ->whereIn('tags.tag_type', $types)
            ->where($localeColumn, JsonLocaleQuery::likeOperator(), '%'.$q.'%')
            ->addSelect('tags.*')
            ->addSelect(['taggables_count' => DB::table('taggables')
                ->selectRaw('count(*)')
                ->whereColumn('taggables.tag_id', 'tags.id'),
            ]);

        $this->applySortStrategy($query, $locale);

        /** @var Collection<int, Tag> $results */
        $results = $query->limit($limit)->get();

        return $results;
    }

    /**
     * @param  Builder<Tag>  $query
     */
    private function applySortStrategy(Builder $query, string $locale): void
    {
        $strategy = config('tagging-kit.suggest.sort_strategy', 'use_count_desc');
        $localeColumn = JsonLocaleQuery::localeColumn('name', $locale);

        match ($strategy) {
            'alphabetic' => $query->orderBy($localeColumn),
            'recent' => $query->orderByDesc('tags.created_at'),
            'mixed' => $query->orderByDesc('taggables_count')->orderBy($localeColumn),
            'custom' => $this->applyCustomSort($query, $locale),
            default => $query->orderByDesc('taggables_count'),
        };
    }

    /**
     * @param  Builder<Tag>  $query
     */
    private function applyCustomSort(Builder $query, string $locale): void
    {
        $callback = config('tagging-kit.suggest.custom_sort_callback');

        if (! is_string($callback) || ! str_contains($callback, '@')) {
            $query->orderByDesc('taggables_count');

            return;
        }

        [$class, $method] = explode('@', $callback, 2);

        app($class)->{$method}($query, $locale);
    }

    private function defaultLocale(): string
    {
        $locale = config('tagging-kit.locale.default');

        return is_string($locale) && $locale !== '' ? $locale : app()->getLocale();
    }

    private function clampLimit(int $limit): int
    {
        $max = config('tagging-kit.suggest.max_limit', 50);
        $max = is_int($max) ? $max : 50;

        return max(1, min($limit, $max));
    }
}
