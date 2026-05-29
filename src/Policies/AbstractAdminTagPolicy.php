<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use SlashDw\TaggingKit\Models\Tag;

/**
 * Admin-side system tag CRUD policy (SRP: admin guard concerns only).
 *
 * Hosts extend this and implement {@see self::canManageSystemTags()} to bind
 * their own permission model (e.g. Spatie permission, role hierarchy).
 *
 * All operations are scoped to system tags (tenant_id IS NULL) — user tags
 * are never manageable through admin policy (see {@see AbstractUserTagPolicy}).
 */
abstract class AbstractAdminTagPolicy
{
    /**
     * Host-implemented permission gate for system tag management.
     *
     * @param  string  $ability  One of: view, create, update, delete
     */
    abstract protected function canManageSystemTags(Authenticatable $user, string $ability): bool;

    public function viewAny(Authenticatable $user): bool
    {
        return $this->canManageSystemTags($user, 'view');
    }

    public function create(Authenticatable $user): bool
    {
        return $this->canManageSystemTags($user, 'create');
    }

    public function update(Authenticatable $user, Tag $tag): bool
    {
        if ($tag->getAttribute('tenant_id') !== null) {
            return false;
        }

        return $this->canManageSystemTags($user, 'update');
    }

    public function delete(Authenticatable $user, Tag $tag): bool
    {
        if ($tag->getAttribute('tenant_id') !== null) {
            return false;
        }

        if (! $this->canManageSystemTags($user, 'delete')) {
            return false;
        }

        return ! $this->tagIsInUse($tag);
    }

    protected function tagIsInUse(Tag $tag): bool
    {
        return DB::table('taggables')
            ->where('tag_id', $tag->getKey())
            ->exists();
    }
}
