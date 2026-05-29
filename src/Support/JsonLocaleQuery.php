<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Support;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Database-engine-aware helpers for querying the `name` translatable column.
 *
 * Spatie's `HasTranslations` stores `name` as a JSON object keyed by locale
 * (e.g. `{"en": "White", "tr": "Beyaz"}`). We use Laravel's native JSON arrow
 * column syntax (`name->en`) which the query grammar translates per driver
 * (pgsql `->>`, mysql `JSON_EXTRACT`, etc.) — no raw SQL needed.
 *
 * Driver resolved from `tagging-kit.database.driver` config (or auto-detect).
 */
final class JsonLocaleQuery
{
    public static function driver(): string
    {
        $configured = config('tagging-kit.database.driver');

        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        return DB::connection()->getDriverName();
    }

    /**
     * Case-insensitive LIKE operator for the active driver.
     * PostgreSQL uses ILIKE; MySQL/MariaDB/SQLite LIKE is case-insensitive
     * by default collation.
     */
    public static function likeOperator(): string
    {
        return self::driver() === 'pgsql' ? 'ilike' : 'like';
    }

    /**
     * Laravel JSON arrow column path for a locale (e.g. `name->en`).
     * Safe for use as a query column — the grammar handles driver syntax.
     */
    public static function localeColumn(string $column, string $locale): string
    {
        self::assertSafeIdentifier($column, 'column');
        self::assertSafeIdentifier($locale, 'locale');

        return "{$column}->{$locale}";
    }

    /**
     * Guard against injection — column and locale form a query column path.
     */
    private static function assertSafeIdentifier(string $value, string $label): void
    {
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $value) !== 1) {
            throw new InvalidArgumentException("Unsafe {$label} for JSON locale query: {$value}");
        }
    }
}
