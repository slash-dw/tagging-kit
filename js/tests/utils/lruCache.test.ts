import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { LruCache } from '../../src/utils/lruCache';

describe('LruCache', () => {
  beforeEach(() => vi.useFakeTimers());
  afterEach(() => vi.useRealTimers());

  it('stores and retrieves values', () => {
    const cache = new LruCache<number>(3, 1000);
    cache.set('a', 1);
    expect(cache.get('a')).toBe(1);
  });

  it('expires entries after the TTL', () => {
    const cache = new LruCache<number>(3, 1000);
    cache.set('a', 1);
    vi.advanceTimersByTime(1001);
    expect(cache.get('a')).toBeUndefined();
  });

  it('evicts the least-recently-used entry past max size', () => {
    const cache = new LruCache<number>(2, 10_000);
    cache.set('a', 1);
    cache.set('b', 2);
    cache.get('a'); // touch a → b is now LRU
    cache.set('c', 3); // evicts b
    expect(cache.get('b')).toBeUndefined();
    expect(cache.get('a')).toBe(1);
    expect(cache.get('c')).toBe(3);
  });
});
