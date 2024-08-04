<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasFactory;

    public $guarded = [];

    public static function hasApiKeys()
    {
        return self::exists();
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {

            $model->created_at = now();
            $model->updated_at = now();

            // Check if this is the first key being created
            if (static::count() === 0) {
                $model->active = true;
            } else {
                $model->active = false;
            }

            if ($model->active) {
                static::whereActive()->update(['active' => false]);
            }
        });
    }

    public function scopeWhereActive($query)
    {
        return $query->where('active', true);
    }
}
