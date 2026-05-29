<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Contracts;

/**
 * Resolves the current tenant scope for tag visibility filtering and tenant_id
 * assignment. Tenant-aware hosts implement this contract; the kit visibility
 * global scope on the Tag model uses `currentTenantId()` to filter:
 *
 *     WHERE tags.tenant_id IS NULL                      -- system tags
 *        OR tags.tenant_id = currentTenantId()          -- own tenant tags
 *
 * When `tagging-kit.tenant.enabled = false`, the kit does not resolve this
 * contract and tenant filtering is skipped entirely (tenant-less host).
 *
 * Inherits `currentActorId()` from {@see ActorContextContract} (ISP):
 * tenant-aware hosts need both methods; tenant-less hosts implement only
 * `ActorContextContract`.
 */
interface TenantContextContract extends ActorContextContract
{
    public function currentTenantId(): string|int|null;
}
