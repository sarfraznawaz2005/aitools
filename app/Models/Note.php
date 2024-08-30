<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_folder_id',
        'title',
        'content',
        'reminder_at',
        'html',
        'width',
        'height',
        'ratio',
        'url',
        'image',
        'author',
        'author_url',
        'source',
        'source_url',
        'source_icon',
        'published_at',
    ];

    public function note(): BelongsTo
    {
        return $this->belongsTo(NoteFolder::class);
    }
}
