<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests\Unit;

use SlashDw\TaggingKit\Contracts\ActorContextContract;
use SlashDw\TaggingKit\Contracts\SharedTypesResolverContract;
use SlashDw\TaggingKit\Contracts\TagTypeContract;
use SlashDw\TaggingKit\Contracts\TenantContextContract;
use SlashDw\TaggingKit\Support\DefaultSharedTypesResolver;
use SlashDw\TaggingKit\TaggingKitServiceProvider;
use SlashDw\TaggingKit\Tests\TestCase;

/**
 * Faz 0 scaffolding smoke tests — verifies the package boots within Laravel
 * testbench, contracts are loadable, and DefaultSharedTypesResolver is bound.
 */
class ScaffoldingTest extends TestCase
{
    public function test_package_service_provider_is_registered(): void
    {
        $providers = app()->getLoadedProviders();

        $this->assertArrayHasKey(TaggingKitServiceProvider::class, $providers);
    }

    public function test_config_is_merged_with_defaults(): void
    {
        $this->assertSame('api/tagging', config('tagging-kit.routes.prefix'));
        $this->assertSame('use_count_desc', config('tagging-kit.suggest.sort_strategy'));
        $this->assertTrue(config('tagging-kit.tenant.enabled'));
        $this->assertSame('int', config('tagging-kit.keys.tags'));
        $this->assertSame('users', config('tagging-kit.audit.actor_table'));
    }

    public function test_shared_types_resolver_contract_resolves_to_default_implementation(): void
    {
        $resolver = app(SharedTypesResolverContract::class);

        $this->assertInstanceOf(DefaultSharedTypesResolver::class, $resolver);
    }

    public function test_contract_interfaces_are_loadable(): void
    {
        $this->assertTrue(interface_exists(TagTypeContract::class));
        $this->assertTrue(interface_exists(ActorContextContract::class));
        $this->assertTrue(interface_exists(TenantContextContract::class));
        $this->assertTrue(interface_exists(SharedTypesResolverContract::class));
    }

    public function test_tenant_context_extends_actor_context(): void
    {
        $reflection = new \ReflectionClass(TenantContextContract::class);

        $this->assertTrue(
            $reflection->implementsInterface(ActorContextContract::class),
            'TenantContextContract must extend ActorContextContract (ISP)'
        );
    }
}
