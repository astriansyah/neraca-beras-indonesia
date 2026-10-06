<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indicator extends Model
{
    protected $fillable = ['category_id', 'code', 'name', 'unit', 'description', 'is_reference', 'sort'];

    protected $casts = ['is_reference' => 'boolean'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function dataPoints(): HasMany
    {
        return $this->hasMany(DataPoint::class)->orderBy('tahun')->orderBy('sort');
    }

    public function breakdowns(): HasMany
    {
        return $this->hasMany(Breakdown::class)->orderBy('tahun')->orderBy('sort');
    }
}
