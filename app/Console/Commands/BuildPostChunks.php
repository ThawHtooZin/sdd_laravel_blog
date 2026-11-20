<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('rag:chunk-posts')]
#[Description('Command description')]
class BuildPostChunks extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Post::query()
            ->where('is_published', true)
            ->chunkById(100, function ($posts): void {
                foreach ($posts as $post) {
                    $paragraphs = preg_split(
                        '/\R{2,}/u',
                        trim($post->body),
                        -1,
                        PREG_SPLIT_NO_EMPTY,
                    );

                    $post->chunks()->delete();

                    foreach ($paragraphs as $index => $paragraph) {
                        $post->chunks()->create([
                            'chunk_index' => $index,
                            'content' => trim($paragraph),
                        ]);
                    }
                }
            });
    }
}
