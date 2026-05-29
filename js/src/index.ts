// Components
export { TagAutocomplete } from './components/TagAutocomplete';
export { TagChip, type TagChipProps } from './components/TagChip';
export { TagSuggestionItem, type TagSuggestionItemProps } from './components/TagSuggestionItem';

// Hooks
export { useTagAutocomplete, type UseTagAutocomplete, type UseTagAutocompleteOptions } from './hooks/useTagAutocomplete';
export { useTaggingBootstrap, type UseTaggingBootstrapOptions } from './hooks/useTaggingBootstrap';
export { useDeleteUserTag, type UseDeleteUserTag } from './hooks/useDeleteUserTag';

// Config
export {
  applyBackendDefaults,
  type BackendFrontendConfig,
  configureTaggingUi,
  getTaggingConfig,
  resetTaggingConfig,
  type TaggingConfig,
  type TaggingDefaults,
  type TaggingFetch,
  type TaggingHooks,
  type TaggingI18n,
} from './config/store';

// API (advanced / custom UIs)
export { deleteUserTag, fetchTaggingConfig, suggestTags } from './api/taggingClient';

// Types
export { isSystemTag, type Tag } from './types/Tag';
export type { TagAutocompleteProps } from './types/TagAutocompleteProps';
