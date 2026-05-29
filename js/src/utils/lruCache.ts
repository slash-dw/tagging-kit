interface Entry<V> {
  value: V;
  expiresAt: number;
}

/**
 * Tiny LRU cache with TTL. Used to memoize suggest results per query string so
 * repeated keystrokes/backspaces don't re-hit the server within the TTL window.
 */
export class LruCache<V> {
  private readonly store = new Map<string, Entry<V>>();

  constructor(
    private readonly maxSize: number,
    private readonly ttlMs: number,
  ) {}

  get(key: string): V | undefined {
    const entry = this.store.get(key);
    if (entry === undefined) {
      return undefined;
    }

    if (Date.now() > entry.expiresAt) {
      this.store.delete(key);

      return undefined;
    }

    // LRU touch: re-insert to mark as most-recently-used.
    this.store.delete(key);
    this.store.set(key, entry);

    return entry.value;
  }

  set(key: string, value: V): void {
    if (this.store.has(key)) {
      this.store.delete(key);
    } else if (this.store.size >= this.maxSize) {
      const oldest = this.store.keys().next().value;
      if (oldest !== undefined) {
        this.store.delete(oldest);
      }
    }

    this.store.set(key, { value, expiresAt: Date.now() + this.ttlMs });
  }

  clear(): void {
    this.store.clear();
  }
}
