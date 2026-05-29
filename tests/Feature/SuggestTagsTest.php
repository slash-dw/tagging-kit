<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests\Feature;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use SlashDw\TaggingKit\Contracts\SharedTypesResolverContract;
use SlashDw\TaggingKit\Models\Tag;
use SlashDw\TaggingKit\Queries\SuggestTagsQuery;
use SlashDw\TaggingKit\Tests\Fixtures\TestSharedTypesResolver;
use SlashDw\TaggingKit\Tests\Fixtures\TestTagType;
use SlashDw\TaggingKit\Tests\TestCase;

class SuggestTagsTest extends TestCase
{
    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        // Tenant-less for suggest mechanics (boundary covered separately).
        $app['config']->set('tagging-kit.tenant.enabled', false);
        $app['config']->set('tagging-kit.audit.track_created_by', false);
    }

    public function test_suggest_matches_by_name_substring(): void
    {
        $this->createTag('White Oak', TestTagType::Alpha);
        $this->createTag('Black Walnut', TestTagType::Alpha);

        $results = $this->suggest('whi', TestTagType::Alpha);

        $this->assertCount(1, $results);
        $this->assertSame('White Oak', $results->first()?->name);
    }

    public function test_suggest_includes_shared_types(): void
    {
        app()->bind(SharedTypesResolverContract::class, TestSharedTypesResolver::class);

        $this->createTag('Matte', TestTagType::Shared);
        $this->createTag('Matte Drawer', TestTagType::Alpha);

        // Suggesting in the Alpha domain must surface Shared 'Matte' too.
        $results = $this->suggest('matte', TestTagType::Alpha);

        $names = $results->map(fn (Tag $t): string => $t->name)->all();
        $this->assertContains('Matte', $names);
        $this->assertContains('Matte Drawer', $names);
    }

    public function test_suggest_sorts_by_use_count_desc(): void
    {
        $popular = $this->createTag('Popular', TestTagType::Alpha);
        $rare = $this->createTag('Popcorn', TestTagType::Alpha);

        // Attach 'Popular' to 3 entities, 'Popcorn' to 1.
        foreach (range(1, 3) as $i) {
            DB::table('taggables')->insert(['tag_id' => $popular->getKey(), 'taggable_id' => $i, 'taggable_type' => 'thing']);
        }
        DB::table('taggables')->insert(['tag_id' => $rare->getKey(), 'taggable_id' => 1, 'taggable_type' => 'thing']);

        $results = $this->suggest('pop', TestTagType::Alpha);

        $this->assertSame('Popular', $results->first()?->name);
    }

    private function createTag(string $name, TestTagType $type): Tag
    {
        return Tag::create([
            'name' => ['en' => $name],
            'tag_type' => $type,
        ]);
    }

    /**
     * @return Collection<int, Tag>
     */
    private function suggest(string $q, TestTagType $type): Collection
    {
        return app(SuggestTagsQuery::class)->execute($q, $type, locale: 'en');
    }
}
