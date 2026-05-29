import type { Tag } from '../types/Tag';

/** Fetch signature the client uses; host may inject an auth-aware wrapper. */
export type TaggingFetch = (input: string, init?: RequestInit) => Promise<Response>;

export interface TaggingDefaults {
  debounceMs: number;
  minLengthToSearch: number;
  initialFetchLimit: number;
  cacheTtlMs: number;
  cacheSize: number;
  fuseThreshold: number;
  allowCreate: boolean;
  allowDeleteUserTags: boolean;
}

export interface TaggingI18n {
  placeholder: string;
  addNew: string;
  deleteConfirm: string;
  detachTooltip: string;
  deleteTooltip: string;
  loading: string;
  noResults: string;
}

export interface TaggingHooks {
  onTagCreated?: (tag: Tag) => void;
  onUserTagDeleted?: (tag: Tag) => void;
}

export interface TaggingConfig {
  baseUrl: string;
  fetch: TaggingFetch;
  defaults: TaggingDefaults;
  i18n: TaggingI18n;
  hooks: TaggingHooks;
}

const HARDCODED_DEFAULTS: TaggingDefaults = {
  debounceMs: 300,
  minLengthToSearch: 2,
  initialFetchLimit: 50,
  cacheTtlMs: 5 * 60 * 1000,
  cacheSize: 20,
  fuseThreshold: 0.4,
  allowCreate: true,
  allowDeleteUserTags: true,
};

const HARDCODED_I18N: TaggingI18n = {
  placeholder: 'Add tag…',
  addNew: 'Create',
  deleteConfirm: 'Delete this tag permanently?',
  detachTooltip: 'Remove from this item (tag stays in the system)',
  deleteTooltip: 'Delete permanently',
  loading: 'Loading…',
  noResults: 'No tags found',
};

const HARDCODED_FETCH: TaggingFetch = (input, init) => globalThis.fetch(input, init);

// Three precedence layers (low → high): HARDCODED < backend (/tagging/config)
// < host (configureTaggingUi). Resolved at read time in getTaggingConfig().
let backendDefaults: Partial<TaggingDefaults> = {};
let hostBaseUrl: string | undefined;
let hostFetch: TaggingFetch | undefined;
let hostDefaults: Partial<TaggingDefaults> = {};
let hostI18n: Partial<TaggingI18n> = {};
let hostHooks: TaggingHooks = {};

/**
 * Host override (priority 2: above backend `/tagging/config`, below per-component
 * props). Call once at app bootstrap. Partial-merges; always wins over backend.
 */
export function configureTaggingUi(partial: {
  baseUrl?: string;
  fetch?: TaggingFetch;
  defaults?: Partial<TaggingDefaults>;
  i18n?: Partial<TaggingI18n>;
  hooks?: TaggingHooks;
}): void {
  if (partial.baseUrl !== undefined) {
    hostBaseUrl = partial.baseUrl;
  }
  if (partial.fetch !== undefined) {
    hostFetch = partial.fetch;
  }
  if (partial.defaults !== undefined) {
    hostDefaults = { ...hostDefaults, ...partial.defaults };
  }
  if (partial.i18n !== undefined) {
    hostI18n = { ...hostI18n, ...partial.i18n };
  }
  if (partial.hooks !== undefined) {
    hostHooks = { ...hostHooks, ...partial.hooks };
  }
}

/** Raw shape returned by GET /api/tagging/config (snake_case). */
export interface BackendFrontendConfig {
  debounce_ms?: number;
  min_length_to_search?: number;
  initial_fetch_limit?: number;
  cache_ttl_ms?: number;
  cache_size?: number;
  fuse_threshold?: number;
  allow_create_default?: boolean;
  allow_delete_user_tags_default?: boolean;
}

/** Apply backend defaults (priority 3). Host overrides always win at read time. */
export function applyBackendDefaults(cfg: BackendFrontendConfig): void {
  const next: Partial<TaggingDefaults> = {};
  if (cfg.debounce_ms !== undefined) {
    next.debounceMs = cfg.debounce_ms;
  }
  if (cfg.min_length_to_search !== undefined) {
    next.minLengthToSearch = cfg.min_length_to_search;
  }
  if (cfg.initial_fetch_limit !== undefined) {
    next.initialFetchLimit = cfg.initial_fetch_limit;
  }
  if (cfg.cache_ttl_ms !== undefined) {
    next.cacheTtlMs = cfg.cache_ttl_ms;
  }
  if (cfg.cache_size !== undefined) {
    next.cacheSize = cfg.cache_size;
  }
  if (cfg.fuse_threshold !== undefined) {
    next.fuseThreshold = cfg.fuse_threshold;
  }
  if (cfg.allow_create_default !== undefined) {
    next.allowCreate = cfg.allow_create_default;
  }
  if (cfg.allow_delete_user_tags_default !== undefined) {
    next.allowDeleteUserTags = cfg.allow_delete_user_tags_default;
  }

  backendDefaults = { ...backendDefaults, ...next };
}

export function getTaggingConfig(): TaggingConfig {
  return {
    baseUrl: hostBaseUrl ?? '',
    fetch: hostFetch ?? HARDCODED_FETCH,
    defaults: { ...HARDCODED_DEFAULTS, ...backendDefaults, ...hostDefaults },
    i18n: { ...HARDCODED_I18N, ...hostI18n },
    hooks: hostHooks,
  };
}

/** Test helper — reset all layers to initial state. */
export function resetTaggingConfig(): void {
  backendDefaults = {};
  hostBaseUrl = undefined;
  hostFetch = undefined;
  hostDefaults = {};
  hostI18n = {};
  hostHooks = {};
}
