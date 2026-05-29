import { getTaggingConfig } from '../config/store';
import { isSystemTag, type Tag } from '../types/Tag';

export interface TagSuggestionItemProps {
  tag: Tag;
  onSelect: () => void;
  /** Permanent delete (user tags only) — distinct from chip detach. */
  onDelete?: () => void;
  allowDelete: boolean;
  className?: string;
}

export function TagSuggestionItem({ tag, onSelect, onDelete, allowDelete, className }: TagSuggestionItemProps) {
  const { i18n } = getTaggingConfig();
  // No visual distinction between system/user tags (UX decision); only the
  // 🗑 affordance differs — shown for user tags when deletion is allowed.
  const showDelete = allowDelete && onDelete !== undefined && !isSystemTag(tag);

  return (
    <li className={className} data-tagging-suggestion="" data-tag-id={String(tag.id)}>
      <button type="button" data-tagging-suggestion-select="" onClick={onSelect}>
        <span data-tagging-suggestion-label="">{tag.name}</span>
        {tag.use_count !== undefined && (
          <span data-tagging-suggestion-count="">{tag.use_count}</span>
        )}
      </button>
      {showDelete && (
        <button
          type="button"
          data-tagging-suggestion-delete=""
          onClick={onDelete}
          title={i18n.deleteTooltip}
          aria-label={`${i18n.deleteTooltip}: ${tag.name}`}
        >
          🗑
        </button>
      )}
    </li>
  );
}
