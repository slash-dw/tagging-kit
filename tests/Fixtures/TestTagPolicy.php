<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests\Fixtures;

use Illuminate\Contracts\Auth\Authenticatable;
use SlashDw\TaggingKit\Models\Tag;
use SlashDw\TaggingKit\Policies\AbstractAdminTagPolicy;
use SlashDw\TaggingKit\Policies\AbstractUserTagPolicy;

/**
 * Test fixture composing both kit policies (admin extends + user trait) —
 * mirrors the host concrete policy pattern and exercises AbstractUserTagPolicy
 * so PHPStan analyses the trait.
 */
class TestTagPolicy extends AbstractAdminTagPolicy
{
    use AbstractUserTagPolicy;

    public function __construct(
        private readonly bool $canManage = true,
        private readonly string|int|null $ownerTenantId = null,
    ) {}

    protected function canManageSystemTags(Authenticatable $user, string $ability): bool
    {
        return $this->canManage;
    }

    protected function actorOwnsTag(Authenticatable $user, Tag $tag): bool
    {
        return $tag->getAttribute('tenant_id') === $this->ownerTenantId;
    }
}
