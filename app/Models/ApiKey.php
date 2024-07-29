<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasFactory;

    public $guarded = [];

    public function isActive()
    {
        return $this->active;
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            // Check if this is the first key being created
            if (static::count() === 0) {
                $model->active = true;
            }

            if ($model->isActive()) {
                static::whereActive()->update(['active' => false]);
            }
        });
    }

    public function scopeWhereActive($query)
    {
        return $query->where('active', true);
    }
}
