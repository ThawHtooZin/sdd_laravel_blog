<?php

namespace App\Console\Commands;

use App\Services\PostChunkRetriever;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('rag:search {question : The question to search for}')]
#[Description('Find published blog chunks that are semantically similar to a question')]
class SearchPostChunks extends Command
{
    public function handle(PostChunkRetriever $retriever): int
    {
        $question = trim((string) $this->argument('question'));

        if ($question === '') {
            $this->error('Enter a non-empty question.');

            return self::INVALID;
        }

        $matches = $retriever->retrieve($question);

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
                $match['title'],
                $match['slug'],
                number_format($match['distance'], 4),
            ));
            $this->line($match['content']);
        }

        return self::SUCCESS;
    }
}
