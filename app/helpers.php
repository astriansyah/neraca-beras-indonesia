<?php

/*
|--------------------------------------------------------------------------
| Helper format angka lokal Indonesia (id-ID)
|--------------------------------------------------------------------------
| Koma sebagai pemisah desimal, titik sebagai pemisah ribuan.
*/

if (! function_exists('fmt_id')) {
    /** Format angka: fmt_id(1234.5, 2) => "1.234,50". Null => "–". */
    function fmt_id(int|float|null $value, int $decimals = 2, bool $trim = false): string
    {
        if ($value === null || is_nan((float) $value)) {
            return '–';
        }
        $s = number_format((float) $value, $decimals, ',', '.');
        if ($trim && str_contains($s, ',')) {
            $s = rtrim(rtrim($s, '0'), ',');
        }

        return $s;
    }
}

if (! function_exists('fmt_pct')) {
    /** Format persen dengan tanda opsional: fmt_pct(-90.03, 1, true) => "−90,0%". */
    function fmt_pct(int|float|null $value, int $decimals = 1, bool $signed = false): string
    {
        if ($value === null) {
            return '–';
        }
        $sign = $signed ? ($value > 0 ? '+' : ($value < 0 ? '−' : '')) : ($value < 0 ? '−' : '');

        return $sign.fmt_id(abs((float) $value), $decimals).'%';
    }
}

if (! function_exists('fmt_unit')) {
    /** Format angka + satuan: fmt_unit(30.62, 'juta ton') => "30,62 juta ton". */
    function fmt_unit(int|float|null $value, string $unit, int $decimals = 2): string
    {
        return fmt_id($value, $decimals).($value === null ? '' : ' '.$unit);
    }
}
