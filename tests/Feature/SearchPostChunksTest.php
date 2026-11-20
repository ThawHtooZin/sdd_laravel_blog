<?php

namespace Tests\Feature;

use App\Services\PostChunkRetriever;
use Tests\TestCase;

class SearchPostChunksTest extends TestCase
{
    public function test_it_embeds_the_question_and_prints_nearest_published_chunks(): void
    {
        $this->mock(PostChunkRetriever::class)
            ->shouldReceive('retrieve')
            ->once()
            ->with('Which method checks submitted credentials?')
            ->andReturn([
                [
                    'title' => 'Laravel Authentication Explained',
                    'slug' => 'rag-demo-laravel-authentication',
                    'content' => 'Auth::attempt() checks the submitted credentials.',
                    'distance' => 0.12,
                ],
            ]);

        $this->artisan('rag:search', [
            'question' => 'Which method checks submitted credentials?',
        ])
            ->expectsOutput('Closest published-post chunks:')
            ->expectsOutputToContain('Laravel Authentication Explained')
            ->expectsOutputToContain('Auth::attempt() checks the submitted credentials.')
            ->assertExitCode(0);
    }
}
