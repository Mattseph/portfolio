<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GithubActivityCache extends Model
{
    protected $table = 'github_activity_cache';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'fetched_at' => 'datetime'];
    }

    public static function latestSnapshot(): ?self
    {
        return static::orderByDesc('fetched_at')->first();
    }
}
