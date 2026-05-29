<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests\Feature;

use SlashDw\TaggingKit\Tests\TestCase;

class ConfigEndpointTest extends TestCase
{
    public function test_config_endpoint_returns_frontend_defaults(): void
    {
        $this->withoutMiddleware();

        $this->getJson('/api/tagging/config')
            ->assertOk()
            ->assertJsonPath('data.debounce_ms', 300)
            ->assertJsonPath('data.min_length_to_search', 2)
            ->assertJsonPath('data.initial_fetch_limit', 50)
            ->assertJsonPath('data.allow_create_default', true);
    }
}
