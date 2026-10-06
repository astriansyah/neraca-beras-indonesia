<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Breakdown extends Model
{
    protected $fillable = [
        'indicator_id', 'tahun', 'label', 'nilai', 'satuan',
        'is_aggregate', 'is_derived', 'catatan', 'sumber_url', 'sort',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'nilai' => 'float',
        'is_aggregate' => 'boolean',
        'is_derived' => 'boolean',
    ];

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(Indicator::class);
    }

    public function sources(): BelongsToMany
    {
        return $this->belongsToMany(Source::class);
    }
}
