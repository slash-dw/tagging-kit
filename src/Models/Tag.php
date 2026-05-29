<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Models;

use Spatie\Tags\Tag as SpatieTag;

/**
 * Kit Tag model — extends Spatie\Tags\Tag without adding domain logic.
 *
 * Faz 0 scaffolding only — Faz 1A will add:
 *  - `tag_type` column cast (TagTypeContract concrete class from config)
 *  - Visibility global scope (system + own tenant filter via TenantContextContract)
 *  - Creating observer: tenant_id + created_by autoset, tag_type ↔ type slug sync
 *  - Updating observer: tag_type ↔ type sync on change
 *  - Custom scopes: forTenant, systemOnly, ofTagTypes
 *
 * The model is intentionally minimal — Spatie's `HasTranslations` trait already
 * provides JSON multi-locale for `name`, and Spatie's relations work out of the box.
 *
 * Hosts override this model via `config('tagging-kit.models.tag')` (e.g. for
 * adding `LogsActivity` trait — see README "model extension pattern").
 */
class Tag extends SpatieTag
{
    // Faz 1A: implementation
}
