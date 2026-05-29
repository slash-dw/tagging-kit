<?php

declare(strict_types=1);

namespace SlashDw\TaggingKit\Tests\Contract;

use Illuminate\Database\Eloquent\Collection;
use ReflectionMethod;
use ReflectionNamedType;
use SlashDw\TaggingKit\Contracts\TagTypeContract;
use SlashDw\TaggingKit\Models\Tag;
use SlashDw\TaggingKit\Queries\SuggestTagsQuery;
use SlashDw\TaggingKit\Tests\Fixtures\TestTagType;
use SlashDw\TaggingKit\Tests\TestCase;

/**
 * Guards the kit's outward-facing contracts (RULES 07). Changes that break
 * these shapes must be deliberate and accompanied by a CHANGELOG note.
 */
class PublicApiContractTest extends TestCase
{
    public function test_tag_type_contract_exposes_value_slug_label(): void
    {
        $type = TestTagType::Alpha;

        $this->assertSame(100, $type->value);
        $this->assertSame('alpha', $type->slug());
        $this->assertSame('Alpha', $type->label());
        $this->assertInstanceOf(TagTypeContract::class, $type);
    }

    public function test_suggest_query_execute_signature_is_stable(): void
    {
        $method = new ReflectionMethod(SuggestTagsQuery::class, 'execute');
        $params = $method->getParameters();

        $this->assertSame('q', $params[0]->getName());
        $this->assertSame('tagType', $params[1]->getName());
        $this->assertSame('limit', $params[2]->getName());
        $this->assertSame('locale', $params[3]->getName());

        $returnType = $method->getReturnType();
        $this->assertInstanceOf(ReflectionNamedType::class, $returnType);
        $this->assertSame(Collection::class, $returnType->getName());
    }

    public function test_suggest_endpoint_response_shape(): void
    {
        $this->withoutMiddleware();

        Tag::create(['name' => ['en' => 'ShapeTag'], 'tag_type' => TestTagType::Alpha]);

        $this->getJson('/api/tagging/suggest?q=shape&tag_type=100')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    ['id', 'name', 'tag_type', 'tenant_id', 'use_count'],
                ],
            ]);
    }
}
