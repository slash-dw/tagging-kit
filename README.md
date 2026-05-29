# slash-dw/tagging-kit

> Generic tagging infrastructure for Laravel. Spatie laravel-tags based, multi-tenant + multi-locale + config-driven. Mixed system/user tag model.

[![CI](https://github.com/slash-dw/tagging-kit/actions/workflows/ci.yml/badge.svg)](https://github.com/slash-dw/tagging-kit/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/packagist/v/slash-dw/tagging-kit.svg)](https://packagist.org/packages/slash-dw/tagging-kit)
[![License](https://img.shields.io/packagist/l/slash-dw/tagging-kit.svg)](LICENSE)

## Status

🚧 **Active development — `v0.0.x`**. Public API may change between minor versions. SemVer applies from `v0.1.0`.

Companion frontend package: [`@slash-dw/tagging-ui`](https://github.com/slash-dw/tagging-ui) (NPM, React/TypeScript) — provides `<TagAutocomplete>` and hooks.

## Installation

```bash
composer require slash-dw/tagging-kit
```

Publish config (optional — defaults work out of the box):
```bash
php artisan vendor:publish --tag="tagging-kit-config"
```

## Documentation

Detailed documentation is in progress. For now, see:
- [`src/Config/tagging-kit.php`](src/Config/tagging-kit.php) — all configuration options with inline comments
- [`src/Contracts/`](src/Contracts/) — public API interfaces (`TagTypeContract`, `ActorContextContract`, `TenantContextContract`, `SharedTypesResolverContract`)

Full README will be published with `v0.1.0` (Faz 6).

## Development

```bash
composer install
composer ci   # runs lint + analyse + test
```

Individual scripts:
- `composer format` — Pint auto-format
- `composer lint` — Pint check (no fix)
- `composer analyse` — PHPStan level 8 (no baseline)
- `composer test` — PHPUnit feature + unit + contract tests

## License

MIT. See [LICENSE](LICENSE).

## Related packages

Part of the SlashDw kit ecosystem:
- [`slash-dw/core-kit`](https://github.com/slash-dw/core-kit) — Repository + API response helpers
- [`slash-dw/error-kit`](https://github.com/slash-dw/error-kit) — Exception handling
- [`slash-dw/filter-kit`](https://github.com/slash-dw/filter-kit) — Eloquent filter/sort DSL
- [`slash-dw/idempotency-kit`](https://github.com/slash-dw/idempotency-kit) — Per-route idempotency middleware
- **`slash-dw/tagging-kit`** — This package (PHP backend)
- **`@slash-dw/tagging-ui`** — React frontend companion (NPM)
