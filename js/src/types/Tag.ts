/**
 * A tag as returned by the tagging-kit suggest endpoint.
 *
 * `id` and `tenant_id` are `number | string` because the host's primary key
 * type is configurable (int / uuid / ulid). `name` is already resolved to the
 * active locale by the backend.
 */
export interface Tag {
  id: number | string;
  name: string;
  tag_type: number;
  tenant_id: number | string | null;
  use_count?: number;
}

/** A tag with tenant_id === null is a shared/system tag (not user-deletable). */
export function isSystemTag(tag: Tag): boolean {
  return tag.tenant_id === null;
}
