<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use SlashDw\TaggingKit\Events\TagDeleted;
use SlashDw\TaggingKit\Events\UserTagInlineDeleted;
use SlashDw\TaggingKit\Models\Tag;

/**
 * DELETE /api/tagging/tags/{tag}
 *
 * Inline user tag deletion (the autocomplete dropdown 🗑 affordance).
 * Authorized via the host's `deleteUserTag` policy ability (tenant ownership).
 * System tags (tenant_id NULL) are rejected by the policy.
 */
class DeleteUserTagController
{
    public function __invoke(Request $request, Tag $tag): Response
    {
        if (Gate::denies('deleteUserTag', $tag)) {
            throw new AuthorizationException;
        }

        $actorId = $tag->getAttribute('created_by');

        if (config('tagging-kit.user_tags.cascade_detach_before_delete') === true) {
            DB::table('taggables')->where('tag_id', $tag->getKey())->delete();
        }

        $tag->delete();

        if (config('tagging-kit.events.enabled') === true) {
            event(new TagDeleted($tag));
            event(new UserTagInlineDeleted($tag, $actorId));
        }

        return response()->noContent();
    }
}
