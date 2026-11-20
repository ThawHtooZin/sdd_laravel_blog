<?php

namespace App\Console\Commands;

use App\Models\PostChunk;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

#[Signature('rag:embed-post-chunks')]
#[Description('Generate embeddings for post chunks that do not have one yet')]
class EmbedPostChunks extends Command
{
    public function handle(): int
    {
        $embeddedChunks = 0;

        PostChunk::query()
            ->whereNull('embedding')
            ->chunkById(25, function (Collection $chunks) use (&$embeddedChunks): void {
                foreach ($chunks as $chunk) {
                    $response = Http::connectTimeout(3)
                        ->timeout(30)
                        ->post('http://127.0.0.1:11434/api/embed', [
                            'model' => 'nomic-embed-text',
                            'input' => 'search_document: '.$chunk->content,
                        ])
                        ->throw();

                    $vector = $response->json('embeddings.0');

                    if (! is_array($vector) || count($vector) !== 768) {
                        throw new RuntimeException("Ollama returned an invalid embedding for chunk {$chunk->id}; expected 768 values.");
                    }

                    foreach ($vector as $value) {
                        if (! is_int($value) && ! is_float($value)) {
                            throw new RuntimeException("Ollama returned a non-numeric embedding value for chunk {$chunk->id}.");
                        }
                    }

                    $updatedRows = DB::update(
                        'UPDATE post_chunks SET embedding = CAST(? AS vector) WHERE id = ?',
                        [json_encode($vector, JSON_THROW_ON_ERROR), $chunk->id],
                    );

                    if ($updatedRows !== 1) {
                        throw new RuntimeException("Could not save the embedding for chunk {$chunk->id}.");
                    }

                    $embeddedChunks++;
                }
            });

        $this->info("Embedded {$embeddedChunks} post chunks.");

        return self::SUCCESS;
    }
}
