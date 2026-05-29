<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests\Unit;

use InvalidArgumentException;
use SlashDw\TaggingKit\Support\JsonLocaleQuery;
use SlashDw\TaggingKit\Tests\TestCase;

class JsonLocaleQueryTest extends TestCase
{
    public function test_locale_column_builds_json_arrow_path(): void
    {
        $this->assertSame('name->en', JsonLocaleQuery::localeColumn('name', 'en'));
        $this->assertSame('name->tr', JsonLocaleQuery::localeColumn('name', 'tr'));
    }

    public function test_locale_column_rejects_unsafe_identifiers(): void
    {
        $this->expectException(InvalidArgumentException::class);

        JsonLocaleQuery::localeColumn('name', "en'; DROP TABLE tags;--");
    }

    public function test_like_operator_is_driver_aware(): void
    {
        config()->set('tagging-kit.database.driver', 'pgsql');
        $this->assertSame('ilike', JsonLocaleQuery::likeOperator());

        config()->set('tagging-kit.database.driver', 'mysql');
        $this->assertSame('like', JsonLocaleQuery::likeOperator());

        config()->set('tagging-kit.database.driver', 'sqlite');
        $this->assertSame('like', JsonLocaleQuery::likeOperator());
    }

    public function test_driver_falls_back_to_connection_when_unconfigured(): void
    {
        config()->set('tagging-kit.database.driver', null);

        // Testbench default connection is sqlite.
        $this->assertSame('sqlite', JsonLocaleQuery::driver());
    }
}
