<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests\Unit;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use SlashDw\TaggingKit\Models\Tag;
use SlashDw\TaggingKit\Tests\Fixtures\TestTagPolicy;
use SlashDw\TaggingKit\Tests\Fixtures\TestTagType;
use SlashDw\TaggingKit\Tests\TestCase;

class TagPolicyTest extends TestCase
{
    public function test_admin_view_create_gated_by_permission(): void
    {
        $user = $this->user();

        $this->assertTrue((new TestTagPolicy(canManage: true))->viewAny($user));
        $this->assertFalse((new TestTagPolicy(canManage: false))->viewAny($user));
        $this->assertTrue((new TestTagPolicy(canManage: true))->create($user));
        $this->assertFalse((new TestTagPolicy(canManage: false))->create($user));
    }

    public function test_update_rejects_user_tag(): void
    {
        $user = $this->user();
        $policy = new TestTagPolicy(canManage: true);

        $userTag = (new Tag)->forceFill(['tenant_id' => 5]);
        $systemTag = (new Tag)->forceFill(['tenant_id' => null]);

        $this->assertFalse($policy->update($user, $userTag));
        $this->assertTrue($policy->update($user, $systemTag));
    }

    public function test_delete_rejects_in_use_system_tag(): void
    {
        $user = $this->user();
        $policy = new TestTagPolicy(canManage: true);

        $tag = Tag::create(['name' => ['en' => 'InUse'], 'tag_type' => TestTagType::Alpha]);
        DB::table('taggables')->insert([
            'tag_id' => $tag->getKey(),
            'taggable_id' => 1,
            'taggable_type' => 'thing',
        ]);

        $this->assertFalse($policy->delete($user, $tag));
    }

    public function test_delete_allows_unused_system_tag(): void
    {
        $user = $this->user();
        $policy = new TestTagPolicy(canManage: true);

        $tag = Tag::create(['name' => ['en' => 'Unused'], 'tag_type' => TestTagType::Alpha]);

        $this->assertTrue($policy->delete($user, $tag));
    }

    public function test_delete_user_tag_ownership(): void
    {
        $user = $this->user();
        $policy = new TestTagPolicy(ownerTenantId: 5);

        $ownTag = (new Tag)->forceFill(['tenant_id' => 5]);
        $otherTag = (new Tag)->forceFill(['tenant_id' => 9]);
        $systemTag = (new Tag)->forceFill(['tenant_id' => null]);

        $this->assertTrue($policy->deleteUserTag($user, $ownTag));
        $this->assertFalse($policy->deleteUserTag($user, $otherTag));
        $this->assertFalse($policy->deleteUserTag($user, $systemTag));
    }

    private function user(): Authenticatable
    {
        // Stub (not mock) — the policy fixtures don't call methods on the user,
        // so no expectations are configured.
        return $this->createStub(Authenticatable::class);
    }
}
