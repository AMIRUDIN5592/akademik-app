<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kartu_mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')
                ->constrained('mahasiswas')
                ->cascadeOnDelete();
            $table->string('nomor_kartu')->unique();
            $table->date('tanggal_terbit');
            $table->date('tanggal_berlaku');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('kartu_mahasiswas');
    }
};
