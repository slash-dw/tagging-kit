import { type BackendFrontendConfig, getTaggingConfig, type TaggingFetch } from '../config/store';
import type { Tag } from '../types/Tag';

interface SuggestParams {
  q: string;
  tagType: number;
  limit?: number;
  signal?: AbortSignal;
}

/** GET {base}/tagging/suggest?q&tag_type&limit → { data: Tag[] } */
export async function suggestTags({ q, tagType, limit, signal }: SuggestParams): Promise<Tag[]> {
  const { baseUrl, fetch } = getTaggingConfig();
  const params = new URLSearchParams({ q, tag_type: String(tagType) });
  if (limit !== undefined) {
    params.set('limit', String(limit));
  }

  const res = await fetch(`${baseUrl}/tagging/suggest?${params.toString()}`, { signal });
  if (!res.ok) {
    throw new Error(`tagging suggest failed: ${res.status}`);
  }

  const json = (await res.json()) as { data: Tag[] };

  return json.data;
}

/** DELETE {base}/tagging/tags/{id} → 204 */
export async function deleteUserTag(id: number | string): Promise<void> {
  const { baseUrl, fetch } = getTaggingConfig();

  const res = await fetch(`${baseUrl}/tagging/tags/${id}`, { method: 'DELETE' });
  if (!res.ok) {
    throw new Error(`tagging delete failed: ${res.status}`);
  }
}

/** GET {base}/tagging/config → { data: BackendFrontendConfig } */
export async function fetchTaggingConfig(baseUrl: string, fetchFn: TaggingFetch): Promise<BackendFrontendConfig> {
  const res = await fetchFn(`${baseUrl}/tagging/config`);
  if (!res.ok) {
    throw new Error(`tagging config fetch failed: ${res.status}`);
  }

  const json = (await res.json()) as { data: BackendFrontendConfig };

  return json.data;
}
