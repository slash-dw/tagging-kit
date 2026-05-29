<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use SlashDw\TaggingKit\TaggingKitServiceProvider;
use SlashDw\TaggingKit\Tests\Fixtures\TestTagType;
use Spatie\Tags\TagsServiceProvider;

class TestCase extends Orchestra
{
    /**
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            TagsServiceProvider::class,
            TaggingKitServiceProvider::class,
        ];
    }

    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('tagging-kit.contracts.tag_type', TestTagType::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        Schema::create('tenants', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamps();
            });
        }

        // Spatie tags + taggables (mirrors create_tag_tables.php.stub)
        Schema::create('tags', function (Blueprint $table): void {
            $table->id();
            $table->json('name');
            $table->json('slug');
            $table->string('type')->nullable();
            $table->integer('order_column')->nullable();
            $table->timestamps();
        });

        Schema::create('taggables', function (Blueprint $table): void {
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->morphs('taggable');
            $table->unique(['tag_id', 'taggable_id', 'taggable_type']);
        });

        // Kit ALTER migration (config-driven PK; int defaults in tests)
        $migration = include __DIR__.'/../database/migrations/alter_tags_add_tagging_kit_columns.php.stub';
        $migration->up();
    }
}
