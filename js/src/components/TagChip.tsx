import { getTaggingConfig } from '../config/store';
import type { Tag } from '../types/Tag';

export interface TagChipProps {
  tag: Tag;
  /** Detach from the current selection — the tag stays in the system. */
  onRemove: () => void;
  disabled?: boolean;
  className?: string;
}

export function TagChip({ tag, onRemove, disabled, className }: TagChipProps) {
  const { i18n } = getTaggingConfig();

  return (
    <span className={className} data-tagging-chip="" data-tag-id={String(tag.id)}>
      <span data-tagging-chip-label="">{tag.name}</span>
      <button
        type="button"
        data-tagging-chip-remove=""
        onClick={onRemove}
        disabled={disabled}
        title={i18n.detachTooltip}
        aria-label={`${i18n.detachTooltip}: ${tag.name}`}
      >
        ×
      </button>
    </span>
  );
}
