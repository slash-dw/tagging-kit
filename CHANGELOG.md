# Changelog

All notable changes to `slash-dw/tagging-kit` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html)
starting from `v0.1.0` (during `v0.0.x` development period breaking changes may occur).

## [Unreleased]

## [0.0.3] - 2026-05-29

### Added
- Config-driven primary key type for the Tag model (`keys.tags` = int/uuid/ulid):
  `getKeyType()`/`getIncrementing()` adapt, and uuid/ulid ids are generated on
  create. Consumers using UUID/ULID ecosystems no longer hit type mismatches
  (e.g. ActivityLog UUID morph columns). The Spatie `create_tag_tables`
  migration's `$table->id()` must be replaced by the consumer with uuid/ulid
  accordingly.

## [0.0.2] - 2026-05-29

### Fixed
- Migration `down()` uses `dropForeign(['col'])` instead of `dropConstrainedForeignKey('col')` — the latter is not recognized by Larastan stubs in consumer projects (PHPStan level 8 false-positive). Runtime behavior identical.

## [0.0.1] - 2026-05-29

### Added
- Initial package scaffolding (Faz 0)
- `composer.json`, `phpstan.neon.dist` (level 8, no baseline), `pint.json`, `phpunit.xml.dist`
- MIT License
- Public API contracts: `TagTypeContract` (extends `BackedEnum`), `ActorContextContract`, `TenantContextContract`, `SharedTypesResolverContract`
- Config file `tagging-kit.php` with 14 flexibility points
- 7 event classes for host side-effect injection
- GitHub Actions CI workflow (`Pint`, `PHPStan`, `PHPUnit`)
- Pull request template
- **Tag model** (Faz 1A) — extends Spatie Tag with multi-tenant visibility global scope, tenant/actor auto-assignment, `tag_type` ↔ Spatie `type` slug sync observer, and `forTenant`/`systemOnly`/`ofTagTypes` scopes
- **ALTER migration stub** — config-driven PK types (uuid/ulid/int), driver-aware indexes (pgsql GIN trigram / B-tree)
- **`SuggestTagsQuery`** — driver-aware JSON locale LIKE, cross-domain shared type expansion, 5 sort strategies (use_count_desc/alphabetic/recent/mixed/custom)
- **`Support\JsonLocaleQuery`** — driver-aware JSON column path + case-insensitive LIKE operator
- **`Support\TagTypeResolver`** — int → host enum resolution
- **Policies** (SRP split): `AbstractAdminTagPolicy` (class) + `AbstractUserTagPolicy` (trait)
- **Controllers**: `SuggestTagController`, `DeleteUserTagController`, `ConfigController`
- **Routes** — `GET /tagging/{config,suggest}`, `DELETE /tagging/tags/{tag}` (config-driven prefix/middleware/throttle)
- Migration publish (`tagging-kit-migrations`) + route registration in service provider
- 27 tests (feature + unit + contract guards), PHPStan level 8 clean

### Changed
- `TagTypeContract` now extends `BackedEnum` (removed `value()` method — use native `->value`) to avoid the reserved enum `value` member collision
