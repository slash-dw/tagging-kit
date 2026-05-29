<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests\Feature;

use Illuminate\Support\Facades\DB;
use SlashDw\TaggingKit\Contracts\TenantContextContract;
use SlashDw\TaggingKit\Models\Tag;
use SlashDw\TaggingKit\Tests\Fixtures\TestTagType;
use SlashDw\TaggingKit\Tests\Fixtures\TestTenantContext;
use SlashDw\TaggingKit\Tests\TestCase;

class ObserverTest extends TestCase
{
    public function test_tag_type_syncs_to_spatie_type_slug_on_create(): void
    {
        $tag = Tag::create([
            'name' => ['en' => 'White'],
            'tag_type' => TestTagType::Alpha,
        ]);

        $this->assertSame('alpha', $tag->type);
        $this->assertSame(TestTagType::Alpha, $tag->tag_type);
    }

    public function test_tag_type_resyncs_type_on_update(): void
    {
        $tag = Tag::create([
            'name' => ['en' => 'White'],
            'tag_type' => TestTagType::Alpha,
        ]);

        $tag->update(['tag_type' => TestTagType::Beta]);

        $this->assertSame('beta', $tag->fresh()?->type);
    }

    public function test_tenant_id_and_created_by_autoset_from_context(): void
    {
        $this->seedTenantAndUser(5, 99);
        app()->bind(
            TenantContextContract::class,
            fn (): TestTenantContext => new TestTenantContext(tenantId: 5, actorId: 99),
        );

        $tag = Tag::create([
            'name' => ['en' => 'Custom'],
            'tag_type' => TestTagType::Alpha,
        ]);

        $this->assertEquals(5, $tag->getAttribute('tenant_id'));
        $this->assertEquals(99, $tag->getAttribute('created_by'));
    }

    public function test_explicit_tenant_id_not_overwritten(): void
    {
        $this->seedTenantAndUser(7, 1);
        app()->bind(
            TenantContextContract::class,
            fn (): TestTenantContext => new TestTenantContext(tenantId: 5, actorId: 1),
        );

        $tag = Tag::create([
            'name' => ['en' => 'System'],
            'tag_type' => TestTagType::Alpha,
            'tenant_id' => 7,
        ]);

        $this->assertEquals(7, $tag->getAttribute('tenant_id'));
    }

    private function seedTenantAndUser(int $tenantId, int $userId): void
    {
        DB::table('tenants')->insertOrIgnore([
            'id' => $tenantId,
            'name' => "T{$tenantId}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insertOrIgnore([
            'id' => $userId,
            'name' => "U{$userId}",
            'email' => "u{$userId}@test.com",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
