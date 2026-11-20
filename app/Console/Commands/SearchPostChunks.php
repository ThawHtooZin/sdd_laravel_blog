<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

#[Signature('rag:search {question : The question to search for}')]
#[Description('Find published blog chunks that are semantically similar to a question')]
class SearchPostChunks extends Command
{
    public function handle(): int
    {
        $question = trim((string) $this->argument('question'));

        if ($question === '') {
            $this->error('Enter a non-empty question.');

            return self::INVALID;
        }

        $response = Http::connectTimeout(3)
            ->timeout(30)
            ->post('http://127.0.0.1:11434/api/embed', [
                'model' => 'nomic-embed-text',
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

        if ($matches === []) {
            $this->warn('No embedded chunks found for published posts. Build and embed the post chunks first.');

            return self::SUCCESS;
        }

        $this->info('Closest published-post chunks:');

        foreach ($matches as $index => $match) {
            $this->newLine();
            $this->line(sprintf(
                '%d. %s (/posts/%s) - cosine distance: %s',
                $index + 1,
                $match->title,
                $match->slug,
                number_format((float) $match->distance, 4),
            ));
            $this->line($match->content);
        }

        return self::SUCCESS;
    }
}
