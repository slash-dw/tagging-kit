<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests\Fixtures;

use SlashDw\TaggingKit\Contracts\TenantContextContract;

/**
 * Test fixture implementing TenantContextContract with injectable values.
 */
class TestTenantContext implements TenantContextContract
{
    public function __construct(
        private readonly string|int|null $tenantId = null,
        private readonly string|int|null $actorId = null,
    ) {}

    public function currentTenantId(): string|int|null
    {
        return $this->tenantId;
    }

    public function currentActorId(): string|int|null
    {
        return $this->actorId;
    }
}
