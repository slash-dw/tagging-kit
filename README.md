# slash-dw/tagging-kit

> Generic tagging infrastructure for Laravel. Spatie laravel-tags based, multi-tenant + multi-locale + config-driven. Mixed system/user tag model.

[![CI](https://github.com/slash-dw/tagging-kit/actions/workflows/ci.yml/badge.svg)](https://github.com/slash-dw/tagging-kit/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/packagist/v/slash-dw/tagging-kit.svg)](https://packagist.org/packages/slash-dw/tagging-kit)
[![License](https://img.shields.io/packagist/l/slash-dw/tagging-kit.svg)](LICENSE)

## Status

🚧 **Active development — `v0.0.x`**. Public API may change between minor versions. SemVer applies from `v0.1.0`.

This is a **monorepo**: the PHP backend lives at the root (Packagist:
`slash-dw/tagging-kit`) and the React frontend in [`js/`](js/) (npm:
`@slash-dw/tagging-ui`). See [Frontend](#frontend--slash-dwtagging-ui) below.

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
- [`config/tagging-kit.php`](config/tagging-kit.php) — all configuration options with inline comments
- [`src/Contracts/`](src/Contracts/) — public API interfaces (`TagTypeContract`, `ActorContextContract`, `TenantContextContract`, `SharedTypesResolverContract`)

Full README will be published with `v0.1.0` (Faz 6).

## Frontend — @slash-dw/tagging-ui

React/TypeScript companion in [`js/`](js/), published to npm as
`@slash-dw/tagging-ui`. Headless `<TagAutocomplete>` + hooks (debounce,
Fuse.js local-first, LRU cache, AbortController). Styling is host-owned via
`data-tagging-*` attributes + `className`.

### Install

```bash
npm install @slash-dw/tagging-ui
npm install react @tanstack/react-query   # peer deps
```

### Bootstrap (once, at app entry)

```tsx
import { configureTaggingUi, useTaggingBootstrap } from '@slash-dw/tagging-ui';

// Optional host overrides (win over backend /tagging/config defaults).
configureTaggingUi({
  baseUrl: '/api',                  // → {base}/tagging/{suggest,config,tags/:id}
  fetch: myCsrfAwareFetch,          // optional auth-aware fetch wrapper
  i18n: { placeholder: 'Etiket ekle…' },
  hooks: { onUserTagDeleted: (t) => analytics.track('tag_deleted', t) },
});

function TaggingBootstrap() {
  useTaggingBootstrap();            // GET /api/tagging/config → internal store
  return null;
}
```

### Use the component

```tsx
import { TagAutocomplete, type Tag } from '@slash-dw/tagging-ui';

function Form() {
  const [tags, setTags] = useState<Tag[]>([]);
  return <TagAutocomplete tagType={100} value={tags} onChange={setTags} />;
}
```

### Public API

| Kind | Exports |
|---|---|
| Components | `TagAutocomplete`, `TagChip`, `TagSuggestionItem` |
| Hooks | `useTagAutocomplete`, `useTaggingBootstrap`, `useDeleteUserTag` |
| Config | `configureTaggingUi`, `getTaggingConfig` |
| Low-level API | `suggestTags`, `deleteUserTag`, `fetchTaggingConfig` |
| Types | `Tag`, `TagAutocompleteProps`, `TaggingConfig`, `TaggingDefaults`, … |

**Config precedence** (low → high): hardcoded → backend `/tagging/config` →
`configureTaggingUi` → per-component prop.

### Develop the frontend

```bash
cd js
npm install
npm run ci        # typecheck + vitest + vite build (ESM + CJS + d.ts)
```

## Development (backend)

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
