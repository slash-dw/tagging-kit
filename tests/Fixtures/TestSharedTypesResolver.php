<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests\Fixtures;

use SlashDw\TaggingKit\Contracts\SharedTypesResolverContract;
use SlashDw\TaggingKit\Contracts\TagTypeContract;

/**
 * Test fixture: expands domain types (Alpha/Beta) to include the Shared type,
 * mirroring a real host's cross-domain shared-tag behavior.
 */
class TestSharedTypesResolver implements SharedTypesResolverContract
{
    public function expand(TagTypeContract $type): array
    {
        return match ($type) {
            TestTagType::Alpha, TestTagType::Beta => [(int) $type->value, TestTagType::Shared->value],
            default => [(int) $type->value],
        };
    }
}
