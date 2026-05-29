<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Contracts;

/**
 * Resolves the actor reference written to `tags.created_by` for audit purposes.
 *
 * Hosts with tenant scoping should implement {@see TenantContextContract} instead
 * (which extends this contract). Tenant-less hosts (e.g. CLI tools, single-user
 * apps) implement only this contract — no tenant resolution required.
 *
 * Host semantics decide what `currentActorId()` returns:
 *  - Pure user-based pattern: `users.id`
 *  - Membership-based pattern: `user_memberships.id` (audit in tenant context)
 *  - System seed / unauthenticated: `null`
 *
 * The matching FK target table is configured via `tagging-kit.audit.actor_table`.
 */
interface ActorContextContract
{
    public function currentActorId(): string|int|null;
}
