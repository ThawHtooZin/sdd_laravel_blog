<?php

namespace Tests\Feature;

use App\Services\PostChunkRetriever;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AskBlogTest extends TestCase
{
    public function test_ask_page_is_available_to_public_visitors(): void
    {
        $this->get(route('ask.index'))
            ->assertOk()
            ->assertSee('Ask the blog')
            ->assertSee('Your question');
    }

    public function test_question_returns_a_groq_answer_with_linked_sources(): void
    {
        $question = 'Which method checks submitted login credentials?';
        config()->set('services.groq.key', 'test-groq-key');
        config()->set('services.groq.model', 'openai/gpt-oss-120b');

        $this->mock(PostChunkRetriever::class)
            ->shouldReceive('retrieve')
            ->once()
            ->with($question)
            ->andReturn([
                [
                    'title' => 'Laravel Authentication Explained',
                    'slug' => 'rag-demo-laravel-authentication',
                    'content' => 'Auth::attempt() checks the submitted credentials.',
                    'distance' => 0.12,
                ],
            ]);

        Http::preventStrayRequests();
        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Laravel uses Auth::attempt() to check submitted credentials [1].',
                        ],
                    ],
                ],
            ]),
        ]);

        $this->post(route('ask.store'), ['question' => $question])
            ->assertOk()
            ->assertSee('Laravel uses Auth::attempt() to check submitted credentials [1].')
            ->assertSee('Laravel Authentication Explained')
            ->assertSee(route('posts.show', ['post' => 'rag-demo-laravel-authentication']));

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.groq.com/openai/v1/chat/completions'
            && $request->hasHeader('Authorization', 'Bearer test-groq-key')
            && $request['model'] === 'openai/gpt-oss-120b'
            && str_contains($request['messages'][1]['content'], $question)
            && str_contains($request['messages'][1]['content'], 'Auth::attempt() checks the submitted credentials.'));
    }

    public function test_question_shows_an_error_when_groq_api_key_is_missing(): void
    {
        config()->set('services.groq.key', null);

        $this->mock(PostChunkRetriever::class)
            ->shouldReceive('retrieve')
            ->once()
            ->andReturn([
                [
                    'title' => 'Laravel Authentication Explained',
                    'slug' => 'rag-demo-laravel-authentication',
                    'content' => 'Auth::attempt() checks the submitted credentials.',
                    'distance' => 0.12,
                ],
            ]);

        Http::preventStrayRequests();

        $this->post(route('ask.store'), ['question' => 'How does login work?'])
            ->assertOk()
            ->assertSee('Add your Groq API key as GROQ_API_KEY in .env')
            ->assertSee('Laravel Authentication Explained');
    }

    public function test_question_is_required(): void
    {
        $this->post(route('ask.store'), [])
            ->assertSessionHasErrors('question');
    }
}
