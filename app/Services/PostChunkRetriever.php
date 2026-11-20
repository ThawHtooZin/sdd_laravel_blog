<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PostChunkRetriever
{
    /**
     * @return list<array{title: string, slug: string, content: string, distance: float}>
     */
    public function retrieve(string $question): array
    {
        $embeddingUrl = rtrim((string) config('services.ollama.url'), '/').'/api/embed';

        $response = Http::connectTimeout(3)
            ->timeout(30)
            ->post($embeddingUrl, [
                'model' => config('services.ollama.embedding_model'),
                'input' => 'search_query: '.$question,
            ])
            ->throw();

        $vector = $response->json('embeddings.0');

        if (! is_array($vector) || count($vector) !== 768) {
            throw new RuntimeException('Ollama returned an invalid question embedding; expected 768 values.');
        }

        foreach ($vector as $value) {
            if (! is_int($value) && ! is_float($value)) {
                throw new RuntimeException('Ollama returned a non-numeric question embedding value.');
            }
        }

        $vectorJson = json_encode($vector, JSON_THROW_ON_ERROR);

        $matches = DB::select(
            <<<'SQL'
                SELECT
                    post_chunks.content,
                    posts.title,
                    posts.slug,
                    post_chunks.embedding <=> CAST(? AS vector) AS distance
                FROM post_chunks
                INNER JOIN posts ON posts.id = post_chunks.post_id
                WHERE posts.is_published IS TRUE
                    AND post_chunks.embedding IS NOT NULL
                ORDER BY distance ASC
                LIMIT 3
            SQL,
            [$vectorJson],
        );

        return array_map(
            fn (object $match): array => [
                'title' => (string) $match->title,
                'slug' => (string) $match->slug,
                'content' => (string) $match->content,
                'distance' => (float) $match->distance,
            ],
            $matches,
        );
    }
}
