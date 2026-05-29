<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Support;

use BackedEnum;
use InvalidArgumentException;
use RuntimeException;
use SlashDw\TaggingKit\Contracts\TagTypeContract;

/**
 * Resolves the host's configured TagType backed enum from an integer value.
 *
 * The host binds its concrete enum class via `tagging-kit.contracts.tag_type`.
 * This resolver converts a raw int (e.g. from a request) into the contract.
 */
final class TagTypeResolver
{
    public function fromInt(int $value): TagTypeContract
    {
        $enumClass = $this->enumClass();

        $case = $enumClass::tryFrom($value);

        if (! $case instanceof TagTypeContract) {
            throw new InvalidArgumentException("Unknown tag_type value: {$value}");
        }

        return $case;
    }

    /**
     * @return class-string<TagTypeContract>
     */
    private function enumClass(): string
    {
        $configured = config('tagging-kit.contracts.tag_type');

        if (! is_string($configured) || ! enum_exists($configured)) {
            throw new RuntimeException(
                'Config tagging-kit.contracts.tag_type must be set to a BackedEnum class implementing TagTypeContract.'
            );
        }

        // TagTypeContract extends BackedEnum, so this also guarantees tryFrom().
        if (! is_a($configured, TagTypeContract::class, true)) {
            throw new RuntimeException(
                "Configured tag_type [{$configured}] must be a BackedEnum implementing TagTypeContract."
            );
        }

        return $configured;
    }
}
