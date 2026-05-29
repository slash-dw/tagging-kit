<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use SlashDw\TaggingKit\Contracts\SharedTypesResolverContract;
use SlashDw\TaggingKit\Support\DefaultSharedTypesResolver;

/**
 * TaggingKit service provider.
 *
 * - register(): config merge + default contract bindings
 * - boot(): config/migration publish, route registration
 *
 * Tag model observers (visibility scope, tenant/actor autoset, tag_type↔type
 * sync) are registered via the model's `booted()` method — no provider wiring.
 */
class TaggingKitServiceProvider extends ServiceProvider
{
    private const CONFIG_PATH = __DIR__.'/../config/tagging-kit.php';

    private const MIGRATION_STUB = __DIR__.'/../database/migrations/alter_tags_add_tagging_kit_columns.php.stub';

    private const ROUTES_PATH = __DIR__.'/../routes/tagging.php';

    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, 'tagging-kit');

        $this->app->bind(
            SharedTypesResolverContract::class,
            function (): SharedTypesResolverContract {
                $class = config('tagging-kit.contracts.shared_types_resolver', DefaultSharedTypesResolver::class);

                if (! is_string($class) || ! is_a($class, SharedTypesResolverContract::class, true)) {
                    $class = DefaultSharedTypesResolver::class;
                }

                /** @var SharedTypesResolverContract */
                return $this->app->make($class);
            }
        );
    }

    public function boot(): void
    {
        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::CONFIG_PATH => config_path('tagging-kit.php'),
            ], 'tagging-kit-config');

            $this->publishes([
                self::MIGRATION_STUB => database_path(
                    'migrations/'.date('Y_m_d_His').'_alter_tags_add_tagging_kit_columns.php'
                ),
            ], 'tagging-kit-migrations');
        }
    }

    private function registerRoutes(): void
    {
        if (config('tagging-kit.routes.register') !== true) {
            return;
        }

        $middleware = config('tagging-kit.routes.middleware', []);

        Route::group([
            'prefix' => config('tagging-kit.routes.prefix', 'api/tagging'),
            'middleware' => is_array($middleware) ? $middleware : [],
            'as' => config('tagging-kit.routes.name_prefix', 'tagging.'),
        ], function (): void {
            $this->loadRoutesFrom(self::ROUTES_PATH);
        });
    }
}
