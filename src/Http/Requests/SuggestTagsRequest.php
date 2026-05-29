<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuggestTagsRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Visibility global scope on the Tag model enforces tenant boundary;
        // no per-request policy needed for read-only suggest.
        return true;
    }

    /**
     * @return array<string, array<int, string|int>>
     */
    public function rules(): array
    {
        $minLength = config('tagging-kit.suggest.min_query_length', 1);
        $maxLimit = config('tagging-kit.suggest.max_limit', 50);

        return [
            'q' => ['required', 'string', 'min:'.(is_int($minLength) ? $minLength : 1)],
            'tag_type' => ['required', 'integer'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:'.(is_int($maxLimit) ? $maxLimit : 50)],
        ];
    }
}
