<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostChunk extends Model
{
    protected $fillable = ['post_id', 'chunk_index', 'content'];
}
