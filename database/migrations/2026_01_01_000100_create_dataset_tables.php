<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Skema dataset Neraca Beras Indonesia.
 *
 * - categories  : kelompok indikator (produksi, konsumsi, impor, ekspor, stok, referensi)
 * - indicators  : definisi indikator (kode, nama, satuan, kategori)
 * - data_points : nilai per tahun/periode, termasuk rentang (min–maks) dan flag estimasi
 * - breakdowns  : rincian per label (mis. negara asal impor)
 * - sources     : daftar sumber (URL) + pivot ke data_points & breakdowns
 * - notes       : catatan dataset & catatan kualitas data (sumber field `warnings` API)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->string('publisher');
            $table->text('url');
            $table->string('type')->default('web'); // pdf | web | press-release | table
            $table->timestamps();
        });

        Schema::create('indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('unit');
            $table->text('description')->nullable();
            $table->boolean('is_reference')->default(false); // bukan data sumber, mis. asumsi populasi
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('data_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun');
            $table->string('periode')->nullable(); // mis. "Awal 2024", "31 Des 2025"
            $table->decimal('nilai', 16, 6)->nullable();
            $table->decimal('nilai_min', 16, 6)->nullable();
            $table->decimal('nilai_maks', 16, 6)->nullable();
            $table->boolean('is_estimate')->default(false);
            $table->boolean('is_derived')->default(false);
            $table->text('catatan')->nullable();
            $table->text('sumber_url')->nullable(); // sumber utama; daftar lengkap di pivot
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
            $table->index(['indicator_id', 'tahun']);
        });

        Schema::create('breakdowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicator_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun');
            $table->string('label');
            $table->decimal('nilai', 16, 6);
            $table->string('satuan');
            $table->boolean('is_aggregate')->default(false); // 'Lainnya' = agregat beberapa negara
            $table->boolean('is_derived')->default(false);   // dihitung sebagai selisih
            $table->text('catatan')->nullable();
            $table->text('sumber_url')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('data_point_source', function (Blueprint $table) {
            $table->foreignId('data_point_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->primary(['data_point_id', 'source_id']);
        });

        Schema::create('breakdown_source', function (Blueprint $table) {
            $table->foreignId('breakdown_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->primary(['breakdown_id', 'source_id']);
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('type'); // dataset | quality
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('severity')->default('info'); // info | warning | danger
            $table->string('badge')->nullable();          // teks badge singkat di chart
            $table->json('scopes')->nullable();            // endpoint/halaman terkait
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
        Schema::dropIfExists('breakdown_source');
        Schema::dropIfExists('data_point_source');
        Schema::dropIfExists('breakdowns');
        Schema::dropIfExists('data_points');
        Schema::dropIfExists('indicators');
        Schema::dropIfExists('sources');
        Schema::dropIfExists('categories');
    }
};
