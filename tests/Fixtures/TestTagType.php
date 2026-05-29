<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests\Fixtures;

use SlashDw\TaggingKit\Contracts\TagTypeContract;

/**
 * Test fixture enum implementing TagTypeContract.
 *
 * - Alpha/Beta: domain-specific types (1xx)
 * - Shared: cross-domain shared type (xx9)
 */
enum TestTagType: int implements TagTypeContract
{
    case Alpha = 100;
    case Beta = 101;
    case Shared = 199;

    public function slug(): string
    {
        return match ($this) {
            self::Alpha => 'alpha',
            self::Beta => 'beta',
            self::Shared => 'shared',
        };
    }

    public function label(): string
    {
        return $this->name;
    }
}
