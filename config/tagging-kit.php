<?php

declare(strict_types=1);

use SlashDw\TaggingKit\Models\Tag;
use SlashDw\TaggingKit\Queries\SuggestTagsQuery;
use SlashDw\TaggingKit\Support\DefaultSharedTypesResolver;

return [
    /*
    |--------------------------------------------------------------------------
    | Route registration & API surface
    |--------------------------------------------------------------------------
    |
    | `register=false` lets hosts disable kit routes and provide their own
    | controllers (e.g. for custom auth flows or non-standard URL prefixes).
    |
    */
    'routes' => [
        'register' => env('TAGGING_KIT_ROUTES', true),
        'prefix' => env('TAGGING_KIT_ROUTE_PREFIX', 'api/tagging'),
        'middleware' => ['api', 'auth:sanctum'],
        'name_prefix' => 'tagging.',
    ],

    'throttle' => [
        'suggest' => env('TAGGING_KIT_SUGGEST_THROTTLE', '60,1'),
        'delete_user_tag' => env('TAGGING_KIT_DELETE_THROTTLE', '30,1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database driver
    |--------------------------------------------------------------------------
    |
    | `null` → DB::getDriverName() auto-detect.
    | Supported explicit values: `pgsql` | `mysql` | `sqlite`.
    | Controls JSON LIKE syntax and index strategy via Support\JsonLocaleQuery.
    |
    */
    'database' => [
        'driver' => env('TAGGING_KIT_DB_DRIVER', null),
        'use_trigram_index' => env('TAGGING_KIT_USE_TRIGRAM', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Primary key types
    |--------------------------------------------------------------------------
    |
    | Default `int` (Spatie laravel-tags + Laravel framework convention).
    | Hosts using UUID/ULID override via env.
    | Supported: `int` | `uuid` | `ulid`
    |
    */
    'keys' => [
        'tags' => env('TAGGING_KIT_TAGS_KEY_TYPE', 'int'),
        'tenant' => env('TAGGING_KIT_TENANT_KEY_TYPE', 'int'),
        'actor' => env('TAGGING_KIT_ACTOR_KEY_TYPE', 'int'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant scope
    |--------------------------------------------------------------------------
    |
    | `enabled=false` → tenant_id column stays NULL, visibility scope
    | becomes "all visible" (tenant-less or single-tenant hosts).
    |
    */
    'tenant' => [
        'enabled' => env('TAGGING_KIT_TENANT_ENABLED', true),
        'foreign_table' => env('TAGGING_KIT_TENANT_TABLE', 'tenants'),
        'foreign_key' => 'id',
        'on_delete' => 'cascade',
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit — created_by actor FK
    |--------------------------------------------------------------------------
    |
    | `actor_table` host-pattern dependent:
    |   - Pure user-based: 'users'
    |   - Membership-based (user + scope pivot): 'user_memberships'
    | Field name stays `created_by`; FK target is config-driven.
    |
    */
    'audit' => [
        'track_created_by' => env('TAGGING_KIT_AUDIT_CREATED_BY', true),
        'actor_table' => env('TAGGING_KIT_ACTOR_TABLE', 'users'),
        'on_actor_delete' => 'set_null',
    ],

    /*
    |--------------------------------------------------------------------------
    | Locale
    |--------------------------------------------------------------------------
    |
    | `mode=single` → JSON single key, suggest LIKE targets default locale only.
    | `mode=multi` → JSON multi-key, suggest active locale-aware.
    |
    */
    'locale' => [
        'mode' => env('TAGGING_KIT_LOCALE_MODE', 'multi'),
        'default' => env('TAGGING_KIT_DEFAULT_LOCALE', 'en'),
        'supported' => array_values(array_filter(
            array_map('trim', explode(',', env('TAGGING_KIT_SUPPORTED_LOCALES', 'en')))
        )),
        'fallback_strategy' => 'first_available',
    ],

    /*
    |--------------------------------------------------------------------------
    | Suggest behavior
    |--------------------------------------------------------------------------
    */
    'suggest' => [
        'default_limit' => 10,
        'max_limit' => 50,
        'min_query_length' => 1,
        'sort_strategy' => 'use_count_desc', // 'alphabetic' | 'recent' | 'mixed' | 'custom'
        'custom_sort_callback' => null,      // 'App\Tagging\MySort@apply' (when sort_strategy=custom)
    ],

    /*
    |--------------------------------------------------------------------------
    | User tag lifecycle
    |--------------------------------------------------------------------------
    */
    'user_tags' => [
        'allow_inline_create' => true,
        'allow_inline_delete' => true,
        'cascade_detach_before_delete' => true,
        'min_use_count_warning' => 10,
    ],

    'cleanup' => [
        'orphan_user_tag_days' => env('TAGGING_KIT_ORPHAN_DAYS', 90),
        'cleanup_system_tags' => false,
        'schedule' => 'weekly', // 'daily' | 'weekly' | null
    ],

    'soft_delete' => [
        'enabled' => env('TAGGING_KIT_SOFT_DELETE', false),
        'retention_days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Model & contract bindings — host override points
    |--------------------------------------------------------------------------
    */
    'models' => [
        'tag' => Tag::class,
    ],

    'contracts' => [
        'tag_type' => null,                                                                        // host: own enum class
        'tenant_context' => null,                                                                  // host: own implementation
        'shared_types_resolver' => DefaultSharedTypesResolver::class,
        'policy' => null,                                                                          // host: own policy class
        'suggest_query' => SuggestTagsQuery::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Frontend defaults — exposed via GET /api/tagging/config
    |--------------------------------------------------------------------------
    |
    | @slash-dw/tagging-ui useTaggingBootstrap hook fetches these values
    | and writes them to its internal store at app boot.
    |
    */
    'frontend' => [
        'debounce_ms' => 300,
        'min_length_to_search' => 2,
        'initial_fetch_limit' => 50,
        'cache_ttl_ms' => 5 * 60 * 1000,
        'cache_size' => 20,
        'fuse_threshold' => 0.4,
        'allow_create_default' => true,
        'allow_delete_user_tags_default' => true,
        'config_endpoint' => '/tagging/config',
    ],

    'events' => [
        'enabled' => true,
    ],
];
