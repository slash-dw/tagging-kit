<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests\Unit;

use SlashDw\TaggingKit\Models\Tag;
use SlashDw\TaggingKit\Tests\TestCase;

/**
 * Guards the config-driven primary key behavior (keys.tags). The Spatie
 * migration's `$table->id()` is replaced by the consumer with uuid/ulid when
 * configured; the model must report a matching non-incrementing string key.
 */
class TagKeyTypeTest extends TestCase
{
    public function test_int_key_by_default(): void
    {
        config()->set('tagging-kit.keys.tags', 'int');

        $tag = new Tag;

        $this->assertSame('int', $tag->getKeyType());
        $this->assertTrue($tag->getIncrementing());
    }

    public function test_uuid_key_when_configured(): void
    {
        config()->set('tagging-kit.keys.tags', 'uuid');

        $tag = new Tag;

        $this->assertSame('string', $tag->getKeyType());
        $this->assertFalse($tag->getIncrementing());
    }

    public function test_ulid_key_when_configured(): void
    {
        config()->set('tagging-kit.keys.tags', 'ulid');

        $tag = new Tag;

        $this->assertSame('string', $tag->getKeyType());
        $this->assertFalse($tag->getIncrementing());
    }
}
