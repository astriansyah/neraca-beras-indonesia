<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['slug', 'name', 'description', 'sort'];

    public function indicators(): HasMany
    {
        return $this->hasMany(Indicator::class)->orderBy('sort');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }
}
