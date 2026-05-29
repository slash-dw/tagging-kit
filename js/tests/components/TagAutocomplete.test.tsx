import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { useState } from 'react';
import { describe, expect, it, vi } from 'vitest';
import { TagAutocomplete } from '../../src/components/TagAutocomplete';
import { configureTaggingUi, type TaggingFetch } from '../../src/config/store';
import type { Tag } from '../../src/types/Tag';

function stubFetch(suggestions: Tag[]): TaggingFetch {
  return vi.fn(async (url: string, init?: RequestInit) => {
    if (url.includes('/tagging/suggest')) {
      return new Response(JSON.stringify({ data: suggestions }), { status: 200 });
    }
    if (init?.method === 'DELETE') {
      return new Response(null, { status: 204 });
    }

    return new Response('{}', { status: 404 });
  });
}

function Harness({ initial = [] }: { initial?: Tag[] }) {
  const [value, setValue] = useState<Tag[]>(initial);

  return (
    <TagAutocomplete
      tagType={100}
      value={value}
      onChange={setValue}
      debounceMs={0}
      minLengthToSearch={2}
    />
  );
}

describe('TagAutocomplete', () => {
  it('suggests on input and selects a tag into the chip list', async () => {
    const white: Tag = { id: 1, name: 'White', tag_type: 199, tenant_id: null, use_count: 5 };
    configureTaggingUi({ baseUrl: '/api', fetch: stubFetch([white]) });
    const user = userEvent.setup();

    render(<Harness />);

    await user.type(screen.getByRole('combobox'), 'whi');

    const suggestion = await screen.findByText('White');
    await user.click(suggestion);

    // Selected → rendered as a chip (input cleared).
    await waitFor(() => {
      expect(screen.getByRole('combobox')).toHaveValue('');
    });
    expect(screen.getByText('White').closest('[data-tagging-chip]')).not.toBeNull();
  });

  it('offers inline creation when no exact match exists', async () => {
    configureTaggingUi({ baseUrl: '/api', fetch: stubFetch([]) });
    const user = userEvent.setup();

    render(<Harness />);
    await user.type(screen.getByRole('combobox'), 'Mehmet Bey');

    const createBtn = await screen.findByText(/Create:/);
    await user.click(createBtn);

    expect(screen.getByText('Mehmet Bey').closest('[data-tagging-chip]')).not.toBeNull();
  });

  it('detaches a selected tag via the chip × (stays selectable again)', async () => {
    const oak: Tag = { id: 7, name: 'Oak', tag_type: 199, tenant_id: null };
    const user = userEvent.setup();
    configureTaggingUi({ baseUrl: '/api', fetch: stubFetch([oak]) });

    render(<Harness initial={[oak]} />);

    const removeBtn = screen.getByRole('button', { name: /Oak/ });
    await user.click(removeBtn);

    expect(screen.queryByText('Oak')).not.toBeInTheDocument();
  });
});
