import Fuse from 'fuse.js';
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { suggestTags } from '../api/taggingClient';
import { getTaggingConfig } from '../config/store';
import type { Tag } from '../types/Tag';
import { debounce } from '../utils/debounce';
import { LruCache } from '../utils/lruCache';

export interface UseTagAutocompleteOptions {
  tagType: number;
  minLength?: number;
  debounceMs?: number;
  initialFetchLimit?: number;
  cacheTtlMs?: number;
  cacheSize?: number;
  fuseThreshold?: number;
}

export interface UseTagAutocomplete {
  query: string;
  suggestions: Tag[];
  isLoading: boolean;
  error: Error | null;
  /** Update the query — Fuse local-first instant results + debounced server. */
  search: (q: string) => void;
  /** Prefetch the top tags into a local Fuse index (call on focus). */
  loadInitial: () => void;
  /** Build an optimistic, not-yet-persisted user tag for inline creation. */
  createTag: (name: string) => Tag;
  reset: () => void;
}

/**
 * Autocomplete data hook: debounce + min-length gate + initial-fetch Fuse index
 * (local-first instant results) + LRU cache + AbortController (cancels stale
 * in-flight requests). Server results are authoritative and overwrite local.
 */
export function useTagAutocomplete(options: UseTagAutocompleteOptions): UseTagAutocomplete {
  const defaults = getTaggingConfig().defaults;
  const { tagType } = options;
  const minLength = options.minLength ?? defaults.minLengthToSearch;
  const debounceMs = options.debounceMs ?? defaults.debounceMs;
  const initialFetchLimit = options.initialFetchLimit ?? defaults.initialFetchLimit;
  const cacheTtlMs = options.cacheTtlMs ?? defaults.cacheTtlMs;
  const cacheSize = options.cacheSize ?? defaults.cacheSize;
  const fuseThreshold = options.fuseThreshold ?? defaults.fuseThreshold;

  const [query, setQuery] = useState('');
  const [suggestions, setSuggestions] = useState<Tag[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<Error | null>(null);

  const cache = useRef(new LruCache<Tag[]>(cacheSize, cacheTtlMs));
  const abortRef = useRef<AbortController | null>(null);
  const fuse = useRef<Fuse<Tag> | null>(null);

  const runServer = useCallback(
    async (q: string): Promise<void> => {
      const cached = cache.current.get(q);
      if (cached !== undefined) {
        setSuggestions(cached);

        return;
      }

      abortRef.current?.abort();
      const controller = new AbortController();
      abortRef.current = controller;

      setIsLoading(true);
      setError(null);
      try {
        const results = await suggestTags({ q, tagType, signal: controller.signal });
        cache.current.set(q, results);
        setSuggestions(results);
      } catch (err) {
        if (!(err instanceof DOMException && err.name === 'AbortError')) {
          setError(err instanceof Error ? err : new Error(String(err)));
        }
      } finally {
        if (abortRef.current === controller) {
          setIsLoading(false);
        }
      }
    },
    [tagType],
  );

  const debouncedServer = useMemo(() => debounce(runServer, debounceMs), [runServer, debounceMs]);

  useEffect(() => () => debouncedServer.cancel(), [debouncedServer]);

  const loadInitial = useCallback((): void => {
    void (async () => {
      try {
        const top = await suggestTags({ q: '', tagType, limit: initialFetchLimit });
        fuse.current = new Fuse(top, { keys: ['name'], threshold: fuseThreshold });
      } catch {
        // Initial prefetch is best-effort; server search still works.
      }
    })();
  }, [tagType, initialFetchLimit, fuseThreshold]);

  const search = useCallback(
    (q: string): void => {
      setQuery(q);

      if (q.length < minLength) {
        setSuggestions([]);
        debouncedServer.cancel();

        return;
      }

      // Local-first: instant results from the Fuse index while the server
      // request is debounced. Server response overwrites these.
      if (fuse.current !== null) {
        setSuggestions(fuse.current.search(q).map((r) => r.item));
      }

      debouncedServer(q);
    },
    [minLength, debouncedServer],
  );

  const createTag = useCallback(
    (name: string): Tag => ({
      id: `new:${name}`,
      name,
      tag_type: tagType,
      tenant_id: null,
    }),
    [tagType],
  );

  const reset = useCallback((): void => {
    setQuery('');
    setSuggestions([]);
    setError(null);
    debouncedServer.cancel();
  }, [debouncedServer]);

  return { query, suggestions, isLoading, error, search, loadInitial, createTag, reset };
}
