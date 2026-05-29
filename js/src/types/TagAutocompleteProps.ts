import type { Tag } from './Tag';

export interface TagAutocompleteProps {
  /** TagType int value (e.g. 100). Drives the suggest query's tag_type. */
  tagType: number;

  /** Controlled selection. */
  value: Tag[];
  onChange: (tags: Tag[]) => void;

  placeholder?: string;
  maxTags?: number;
  disabled?: boolean;

  /** Allow creating a new (user) tag inline. Default: config.defaults.allowCreate. */
  allowCreate?: boolean;
  /** Show the 🗑 delete affordance next to user tags. Default: config.defaults.allowDeleteUserTags. */
  allowDeleteUserTags?: boolean;

  /** Min chars before querying. Default from backend config. */
  minLengthToSearch?: number;
  /** Debounce in ms. Default from backend config. */
  debounceMs?: number;
  cacheTtlMs?: number;
  cacheSize?: number;
  initialFetchLimit?: number;

  /** Admin curated UI — system tags only. */
  systemOnly?: boolean;

  size?: 'sm' | 'md' | 'lg';
  variant?: 'app' | 'admin';
  className?: string;
  testId?: string;
}
