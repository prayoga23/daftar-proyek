<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // PBG fields
            $table->text('irk_pbg')->nullable();
            $table->text('gambar_arsitek_pbg')->nullable();
            $table->text('gambar_struktur_pbg')->nullable();
            $table->text('gambar_mep_pbg')->nullable();
            $table->text('ska_pbg')->nullable();
            $table->text('proses_pbg')->nullable();
            
            // SLF fields
            $table->text('pengukuran_slf')->nullable();
            $table->text('irk_slf')->nullable();
            $table->text('gambar_arsitek_slf')->nullable();
            $table->text('gambar_struktur_slf')->nullable();
            $table->text('gambar_mep_slf')->nullable();
            $table->text('ska_slf')->nullable();
            $table->text('kajian_slf')->nullable();
            $table->text('proses_slf')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop PBG fields
            $table->dropColumn(['irk_pbg', 'gambar_arsitek_pbg', 'gambar_struktur_pbg', 'gambar_mep_pbg', 'ska_pbg', 'proses_pbg']);
            
            // Drop SLF fields
            $table->dropColumn(['pengukuran_slf', 'irk_slf', 'gambar_arsitek_slf', 'gambar_struktur_slf', 'gambar_mep_slf', 'ska_slf', 'kajian_slf', 'proses_slf']);
        });
    }
};
