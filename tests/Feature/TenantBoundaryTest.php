<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests\Feature;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use SlashDw\TaggingKit\Contracts\TenantContextContract;
use SlashDw\TaggingKit\Models\Tag;
use SlashDw\TaggingKit\Tests\Fixtures\TestTagType;
use SlashDw\TaggingKit\Tests\Fixtures\TestTenantContext;
use SlashDw\TaggingKit\Tests\TestCase;

class TenantBoundaryTest extends TestCase
{
    public function test_visibility_scope_hides_other_tenant_user_tags(): void
    {
        $this->seedTenants([1, 2]);

        // Create tags without context (system tag) and with tenant context.
        $this->bindTenant(1);
        Tag::create(['name' => ['en' => 'Tenant1 Tag'], 'tag_type' => TestTagType::Alpha, 'tenant_id' => 1]);

        $this->bindTenant(2);
        Tag::create(['name' => ['en' => 'Tenant2 Tag'], 'tag_type' => TestTagType::Alpha, 'tenant_id' => 2]);

        // System tag (no tenant) — saveQuietly bypasses the tenant autoset observer.
        (new Tag)
            ->forceFill(['name' => ['en' => 'System Tag'], 'slug' => ['en' => 'system-tag'], 'tag_type' => TestTagType::Alpha->value, 'type' => 'alpha', 'tenant_id' => null])
            ->saveQuietly();

        // As tenant 1: sees own + system, not tenant 2.
        $this->bindTenant(1);
        $visibleNames = Tag::query()->get()->map(fn (Tag $t): string => $t->name)->all();

        $this->assertContains('Tenant1 Tag', $visibleNames);
        $this->assertContains('System Tag', $visibleNames);
        $this->assertNotContains('Tenant2 Tag', $visibleNames);
    }

    public function test_system_only_scope_returns_null_tenant_tags(): void
    {
        $this->seedTenants([1]);
        $this->bindTenant(1);

        Tag::create(['name' => ['en' => 'User Tag'], 'tag_type' => TestTagType::Alpha, 'tenant_id' => 1]);
        (new Tag)
            ->forceFill(['name' => ['en' => 'Sys'], 'slug' => ['en' => 'sys'], 'tag_type' => TestTagType::Alpha->value, 'type' => 'alpha', 'tenant_id' => null])
            ->saveQuietly();

        /** @var Collection<int, Tag> $systemTags */
        $systemTags = Tag::query()->systemOnly()->get();

        $this->assertCount(1, $systemTags);
        $this->assertSame('Sys', $systemTags->first()?->name);
    }

    private function bindTenant(int $tenantId): void
    {
        app()->bind(
            TenantContextContract::class,
            fn (): TestTenantContext => new TestTenantContext(tenantId: $tenantId),
        );
    }

    /**
     * @param  array<int, int>  $ids
     */
    private function seedTenants(array $ids): void
    {
        foreach ($ids as $id) {
            DB::table('tenants')->insertOrIgnore([
                'id' => $id,
                'name' => "T{$id}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
