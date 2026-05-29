import { useCallback, useState } from 'react';
import { deleteUserTag as deleteUserTagRequest } from '../api/taggingClient';
import { getTaggingConfig } from '../config/store';
import { isSystemTag, type Tag } from '../types/Tag';

export interface UseDeleteUserTag {
  /** Confirms, deletes, fires the onUserTagDeleted hook. Returns true if deleted. */
  remove: (tag: Tag) => Promise<boolean>;
  isDeleting: boolean;
}

/**
 * Inline deletion of a user tag (the dropdown 🗑 affordance). System tags
 * (tenant_id === null) are never deletable. Uses window.confirm by default;
 * hosts can wrap with their own dialog before calling `remove`.
 */
export function useDeleteUserTag(): UseDeleteUserTag {
  const [isDeleting, setIsDeleting] = useState(false);

  const remove = useCallback(async (tag: Tag): Promise<boolean> => {
    if (isSystemTag(tag)) {
      return false;
    }

    const { i18n, hooks } = getTaggingConfig();

    if (!globalThis.confirm(i18n.deleteConfirm)) {
      return false;
    }

    setIsDeleting(true);
    try {
      await deleteUserTagRequest(tag.id);
      hooks.onUserTagDeleted?.(tag);

      return true;
    } finally {
      setIsDeleting(false);
    }
  }, []);

  return { remove, isDeleting };
}
