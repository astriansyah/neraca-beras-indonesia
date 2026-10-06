<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DataPoint extends Model
{
    protected $fillable = [
        'indicator_id', 'tahun', 'periode', 'nilai', 'nilai_min', 'nilai_maks',
        'is_estimate', 'is_derived', 'catatan', 'sumber_url', 'sort',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'nilai' => 'float',
        'nilai_min' => 'float',
        'nilai_maks' => 'float',
        'is_estimate' => 'boolean',
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

    /** Titik tengah: nilai jika ada, jika tidak (min+maks)/2. */
    public function central(): ?float
    {
        if ($this->nilai !== null) {
            return $this->nilai;
        }
        if ($this->nilai_min !== null && $this->nilai_maks !== null) {
            return ($this->nilai_min + $this->nilai_maks) / 2;
        }

        return null;
    }
}
