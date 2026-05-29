import { useCallback, useState } from 'react';
import { getTaggingConfig } from '../config/store';
import { useDeleteUserTag } from '../hooks/useDeleteUserTag';
import { useTagAutocomplete } from '../hooks/useTagAutocomplete';
import type { Tag } from '../types/Tag';
import type { TagAutocompleteProps } from '../types/TagAutocompleteProps';
import { TagChip } from './TagChip';
import { TagSuggestionItem } from './TagSuggestionItem';

/**
 * Headless tag autocomplete (controlled). Styling is up to the host via
 * `className` + `data-tagging-*` attributes. Backend config defaults apply
 * unless overridden per-prop.
 */
export function TagAutocomplete(props: TagAutocompleteProps) {
  const { tagType, value, onChange, maxTags, disabled = false } = props;
  const { defaults, i18n } = getTaggingConfig();
  const allowCreate = props.allowCreate ?? defaults.allowCreate;
  const allowDeleteUserTags = props.allowDeleteUserTags ?? defaults.allowDeleteUserTags;
  const minLength = props.minLengthToSearch ?? defaults.minLengthToSearch;

  const { query, suggestions, isLoading, search, loadInitial, createTag, reset } = useTagAutocomplete({
    tagType,
    minLength,
    debounceMs: props.debounceMs,
    initialFetchLimit: props.initialFetchLimit,
    cacheTtlMs: props.cacheTtlMs,
    cacheSize: props.cacheSize,
  });
  const { remove } = useDeleteUserTag();
  const [open, setOpen] = useState(false);

  const atMax = maxTags !== undefined && value.length >= maxTags;

  const selectTag = useCallback(
    (tag: Tag): void => {
      if ((maxTags !== undefined && value.length >= maxTags) || value.some((t) => t.id === tag.id)) {
        return;
      }
      onChange([...value, tag]);
      reset();
      setOpen(false);
    },
    [maxTags, value, onChange, reset],
  );

  const detachTag = useCallback(
    (tag: Tag): void => {
      onChange(value.filter((t) => t.id !== tag.id));
    },
    [value, onChange],
  );

  const deleteTag = useCallback(
    async (tag: Tag): Promise<void> => {
      const deleted = await remove(tag);
      if (deleted) {
        detachTag(tag);
      }
    },
    [remove, detachTag],
  );

  const createInline = useCallback((): void => {
    if (query.length < minLength) {
      return;
    }
    selectTag(createTag(query));
  }, [query, minLength, selectTag, createTag]);

  const selectedIds = new Set(value.map((t) => t.id));
  const filtered = suggestions.filter((t) => !selectedIds.has(t.id));
  const exactExists = suggestions.some((t) => t.name.toLowerCase() === query.toLowerCase());
  const showCreate = allowCreate && !atMax && query.length >= minLength && !exactExists;

  return (
    <div className={props.className} data-tagging-autocomplete="" data-testid={props.testId}>
      <div data-tagging-chips="" role="list">
        {value.map((tag) => (
          <TagChip key={String(tag.id)} tag={tag} onRemove={() => detachTag(tag)} disabled={disabled} />
        ))}
      </div>

      <input
        type="text"
        data-tagging-input=""
        value={query}
        disabled={disabled || atMax}
        placeholder={props.placeholder ?? i18n.placeholder}
        aria-expanded={open}
        role="combobox"
        aria-controls="tagging-listbox"
        onChange={(e) => {
          search(e.target.value);
          setOpen(true);
        }}
        onFocus={() => {
          loadInitial();
          setOpen(true);
        }}
        onKeyDown={(e) => {
          if (e.key === 'Enter' && showCreate) {
            e.preventDefault();
            createInline();
          }
        }}
      />

      {open && (
        <ul id="tagging-listbox" data-tagging-listbox="" role="listbox">
          {isLoading && <li data-tagging-loading="">{i18n.loading}</li>}

          {!isLoading &&
            filtered.map((tag) => (
              <TagSuggestionItem
                key={String(tag.id)}
                tag={tag}
                allowDelete={allowDeleteUserTags}
                onSelect={() => selectTag(tag)}
                onDelete={() => void deleteTag(tag)}
              />
            ))}

          {!isLoading && filtered.length === 0 && !showCreate && (
            <li data-tagging-empty="">{i18n.noResults}</li>
          )}

          {showCreate && (
            <li data-tagging-create="">
              <button type="button" onClick={createInline}>
                {i18n.addNew}: “{query}”
              </button>
            </li>
          )}
        </ul>
      )}
    </div>
  );
}
