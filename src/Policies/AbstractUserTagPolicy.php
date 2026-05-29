<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Policies;

use Illuminate\Contracts\Auth\Authenticatable;
use SlashDw\TaggingKit\Models\Tag;

/**
 * User-side inline tag deletion policy (SRP: user guard / tenant ownership).
 *
 * Designed as a trait so a host's concrete policy can compose both admin
 * (extends {@see AbstractAdminTagPolicy}) and user concerns in a single Gate
 * policy class.
 *
 * System tags (tenant_id IS NULL) can never be deleted by users. Ownership of
 * tenant tags is host-specific (the user model varies — direct tenant_id column,
 * membership pivot, etc.), so {@see self::actorOwnsTag()} is host-implemented.
 */
trait AbstractUserTagPolicy
{
    /**
     * Host-implemented ownership check: does this user's tenant own the tag?
     */
    abstract protected function actorOwnsTag(Authenticatable $user, Tag $tag): bool;

    public function deleteUserTag(Authenticatable $user, Tag $tag): bool
    {
        // System tags are never user-deletable.
        if ($tag->getAttribute('tenant_id') === null) {
            return false;
        }

        return $this->actorOwnsTag($user, $tag);
    }
}
