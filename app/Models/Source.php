<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Source extends Model
{
    protected $fillable = ['key', 'title', 'publisher', 'url', 'type'];

    public function dataPoints(): BelongsToMany
    {
        return $this->belongsToMany(DataPoint::class);
    }

    public function breakdowns(): BelongsToMany
    {
        return $this->belongsToMany(Breakdown::class);
    }
}
