<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit;

use Illuminate\Support\ServiceProvider;
use SlashDw\TaggingKit\Contracts\SharedTypesResolverContract;
use SlashDw\TaggingKit\Support\DefaultSharedTypesResolver;

/**
 * TaggingKit service provider.
 *
 * Faz 0 scaffolding: config publish + default contract bindings.
 * Faz 1A will add: route registration, migration publish, policy alias,
 * Tag model observer registration, suggest query binding.
 */
class TaggingKitServiceProvider extends ServiceProvider
{
    private const CONFIG_PATH = __DIR__.'/../config/tagging-kit.php';

    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, 'tagging-kit');

        $this->app->bind(
            SharedTypesResolverContract::class,
            /** @return SharedTypesResolverContract */
            function () {
                /** @var class-string<SharedTypesResolverContract> $class */
                $class = config('tagging-kit.contracts.shared_types_resolver', DefaultSharedTypesResolver::class);

                return $this->app->make($class);
            }
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::CONFIG_PATH => config_path('tagging-kit.php'),
            ], 'tagging-kit-config');
        }
    }
}
