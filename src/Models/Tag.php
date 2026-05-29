<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Models;

use Illuminate\Database\Eloquent\Builder;
use SlashDw\TaggingKit\Contracts\ActorContextContract;
use SlashDw\TaggingKit\Contracts\TagTypeContract;
use SlashDw\TaggingKit\Contracts\TenantContextContract;
use Spatie\Tags\Tag as SpatieTag;

/**
 * Kit Tag model — extends Spatie\Tags\Tag with multi-tenant visibility,
 * actor/tenant auto-assignment, and tag_type ↔ Spatie `type` slug sync.
 *
 * `name` and `slug` remain JSON multi-locale via Spatie's HasTranslations.
 *
 * Authoritative type column is `tag_type` (SMALLINT, int enum). Spatie's native
 * `type` VARCHAR is kept in sync (slug derived from tag_type) for compatibility
 * with Spatie's `withType()` scope and pivot lookups.
 *
 * Hosts override this model via `config('tagging-kit.models.tag')`
 * (e.g. to add `LogsActivity` trait — see README "model extension pattern").
 *
 * @property string $name Spatie HasTranslations — resolved to active locale
 * @property string $slug Spatie HasTranslations — resolved to active locale
 * @property string|null $type Spatie native type slug (synced from tag_type)
 * @property TagTypeContract|int|null $tag_type
 * @property string|int|null $tenant_id
 * @property string|int|null $created_by
 */
class Tag extends SpatieTag
{
    protected static function booted(): void
    {
        static::addGlobalScope('tagging_kit_visibility', static function (Builder $query): void {
            if (config('tagging-kit.tenant.enabled') !== true) {
                return;
            }

            $context = self::resolveTenantContext();

            if (! $context instanceof TenantContextContract) {
                return;
            }

            $tenantId = $context->currentTenantId();

            $query->where(static function (Builder $inner) use ($tenantId): void {
                $inner->whereNull('tags.tenant_id');

                if ($tenantId !== null) {
                    $inner->orWhere('tags.tenant_id', $tenantId);
                }
            });
        });

        static::creating(static function (Tag $tag): void {
            $tag->assignTenantId();
            $tag->assignCreatedBy();
            $tag->syncTypeFromTagType();
        });

        static::updating(static function (Tag $tag): void {
            if ($tag->isDirty('tag_type')) {
                $tag->syncTypeFromTagType(force: true);
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        $casts = [];

        $enumClass = config('tagging-kit.contracts.tag_type');

        if (is_string($enumClass) && enum_exists($enumClass)) {
            $casts['tag_type'] = $enumClass;
        }

        return $casts;
    }

    private function assignTenantId(): void
    {
        if (config('tagging-kit.tenant.enabled') !== true) {
            return;
        }

        if ($this->getAttribute('tenant_id') !== null) {
            return;
        }

        $context = self::resolveTenantContext();

        if ($context instanceof TenantContextContract) {
            $this->setAttribute('tenant_id', $context->currentTenantId());
        }
    }

    private function assignCreatedBy(): void
    {
        if (config('tagging-kit.audit.track_created_by') !== true) {
            return;
        }

        if ($this->getAttribute('created_by') !== null) {
            return;
        }

        $context = self::resolveActorContext();

        if ($context instanceof ActorContextContract) {
            $this->setAttribute('created_by', $context->currentActorId());
        }
    }

    /**
     * Derive Spatie native `type` VARCHAR from authoritative `tag_type` int enum.
     */
    private function syncTypeFromTagType(bool $force = false): void
    {
        if (! $force && ! empty($this->getAttribute('type'))) {
            return;
        }

        $slug = $this->resolveSlugFromTagType();

        if ($slug !== null) {
            $this->setAttribute('type', $slug);
        }
    }

    private function resolveSlugFromTagType(): ?string
    {
        $raw = $this->getAttribute('tag_type');

        if ($raw === null) {
            return null;
        }

        if ($raw instanceof TagTypeContract) {
            return $raw->slug();
        }

        $enumClass = config('tagging-kit.contracts.tag_type');

        if (! is_string($enumClass) || ! enum_exists($enumClass)) {
            return null;
        }

        if (! is_a($enumClass, TagTypeContract::class, true)) {
            return null;
        }

        // TagTypeContract extends BackedEnum → tryFrom() available.
        $case = $enumClass::tryFrom((int) $raw);

        return $case?->slug();
    }

    private static function resolveTenantContext(): ?TenantContextContract
    {
        return app()->bound(TenantContextContract::class)
            ? app(TenantContextContract::class)
            : null;
    }

    private static function resolveActorContext(): ?ActorContextContract
    {
        if (app()->bound(ActorContextContract::class)) {
            return app(ActorContextContract::class);
        }

        // TenantContextContract extends ActorContextContract — a tenant context
        // is also an actor context.
        if (app()->bound(TenantContextContract::class)) {
            return app(TenantContextContract::class);
        }

        return null;
    }

    /**
     * Admin/cross-tenant: target a specific tenant (bypasses visibility scope).
     *
     * @param  Builder<Tag>  $query
     * @return Builder<Tag>
     */
    public function scopeForTenant(Builder $query, string|int|null $tenantId): Builder
    {
        return $query->withoutGlobalScope('tagging_kit_visibility')
            ->where('tags.tenant_id', $tenantId);
    }

    /**
     * Admin: only system tags (tenant_id NULL).
     *
     * @param  Builder<Tag>  $query
     * @return Builder<Tag>
     */
    public function scopeSystemOnly(Builder $query): Builder
    {
        return $query->withoutGlobalScope('tagging_kit_visibility')
            ->whereNull('tags.tenant_id');
    }

    /**
     * Filter by a set of tag_type values (int or TagTypeContract).
     *
     * @param  Builder<Tag>  $query
     * @param  array<int, int|TagTypeContract>  $types
     * @return Builder<Tag>
     */
    public function scopeOfTagTypes(Builder $query, array $types): Builder
    {
        $values = array_map(
            static fn (int|TagTypeContract $type): int => $type instanceof TagTypeContract ? (int) $type->value : $type,
            $types,
        );

        return $query->whereIn('tags.tag_type', $values);
    }
}
