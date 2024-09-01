<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NoteFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
    ];

    public function getBackGroundColor(): string
    {
        if ($this->color === 'text-gray-600') {
            return 'bg-gray-200';
        }

        return str_replace(['text', '600'], ['bg', '50'], $this->color);
    }

    public function getBorderColor(): string
    {
        return str_replace('text', 'border', $this->color);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
