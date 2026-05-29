<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Http\Controllers;

use Illuminate\Http\JsonResponse;

/**
 * GET /api/tagging/config
 *
 * Exposes frontend defaults from `tagging-kit.frontend` config so the
 *
 * @slash-dw/tagging-ui `useTaggingBootstrap` hook can hydrate its store
 * at app boot (single source of truth = backend config).
 */
class ConfigController
{
    public function __invoke(): JsonResponse
    {
        /** @var array<string, mixed> $frontend */
        $frontend = config('tagging-kit.frontend', []);

        return response()->json(['data' => $frontend]);
    }
}
