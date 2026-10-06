<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    protected $fillable = ['code', 'type', 'category_id', 'title', 'body', 'severity', 'badge', 'scopes', 'sort'];

    protected $casts = ['scopes' => 'array'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
