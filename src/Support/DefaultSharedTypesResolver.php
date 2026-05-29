<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Support;

use SlashDw\TaggingKit\Contracts\SharedTypesResolverContract;
use SlashDw\TaggingKit\Contracts\TagTypeContract;

/**
 * No-op resolver — returns only the requested type without expansion.
 *
 * Hosts override by binding their own {@see SharedTypesResolverContract}
 * implementation in their service provider.
 */
class DefaultSharedTypesResolver implements SharedTypesResolverContract
{
    public function expand(TagTypeContract $type): array
    {
        return [(int) $type->value];
    }
}
