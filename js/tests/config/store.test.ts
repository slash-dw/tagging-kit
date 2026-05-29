import { describe, expect, it } from 'vitest';
import { applyBackendDefaults, configureTaggingUi, getTaggingConfig } from '../../src/config/store';

describe('config store', () => {
  it('ships sensible defaults', () => {
    const { defaults } = getTaggingConfig();
    expect(defaults.debounceMs).toBe(300);
    expect(defaults.minLengthToSearch).toBe(2);
    expect(defaults.allowDeleteUserTags).toBe(true);
  });

  it('configureTaggingUi partial-merges and wins over backend defaults', () => {
    configureTaggingUi({ baseUrl: '/api', defaults: { debounceMs: 500 } });

    // Backend tries to set debounceMs=300, but host pinned 500.
    applyBackendDefaults({ debounce_ms: 300, min_length_to_search: 3 });

    const { baseUrl, defaults } = getTaggingConfig();
    expect(baseUrl).toBe('/api');
    expect(defaults.debounceMs).toBe(500); // host override preserved
    expect(defaults.minLengthToSearch).toBe(3); // backend filled (host didn't pin)
  });

  it('merges i18n overrides', () => {
    configureTaggingUi({ i18n: { placeholder: 'Etiket ekle…' } });
    expect(getTaggingConfig().i18n.placeholder).toBe('Etiket ekle…');
    expect(getTaggingConfig().i18n.addNew).toBe('Create'); // untouched default
  });
});
