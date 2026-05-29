<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as Orchestra;
use SlashDw\TaggingKit\TaggingKitServiceProvider;
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
}
