<?php

namespace Tests\Feature;

use App\Services\PostChunkRetriever;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PostChunkRetrieverTest extends TestCase
{
    public function test_it_embeds_a_question_and_queries_published_chunks_by_vector_distance(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'http://127.0.0.1:11434/api/embed' => Http::response([
                'embeddings' => [array_fill(0, 768, 0.25)],
            ]),
        ]);

        DB::shouldReceive('select')
            ->once()
            ->withArgs(function (string $query, array $bindings): bool {
                return str_contains($query, 'post_chunks.embedding <=> CAST(? AS vector)')
                    && str_contains($query, 'posts.is_published IS TRUE')
                    && str_contains($query, 'LIMIT 3')
                    && count(json_decode($bindings[0], true, flags: JSON_THROW_ON_ERROR)) === 768;
            })
            ->andReturn([
                (object) [
                    'title' => 'Laravel Authentication Explained',
                    'slug' => 'rag-demo-laravel-authentication',
                    'content' => 'Auth::attempt() checks the submitted credentials.',
                    'distance' => '0.12',
                ],
            ]);

        $matches = app(PostChunkRetriever::class)->retrieve('Which method checks credentials?');

        $this->assertSame('Laravel Authentication Explained', $matches[0]['title']);
        $this->assertSame(0.12, $matches[0]['distance']);
        Http::assertSent(fn (Request $request): bool => $request->url() === 'http://127.0.0.1:11434/api/embed'
            && $request['model'] === 'nomic-embed-text'
            && $request['input'] === 'search_query: Which method checks credentials?');
    }
}
