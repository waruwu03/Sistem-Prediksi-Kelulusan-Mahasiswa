<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('mahasiswa');
        Schema::create('mahasiswa', function (Blueprint $table): void {
            $table->id();
            $table->string('nim')->unique();
            $table->string('nama');
            $table->decimal('ipk', 3, 2);
            $table->unsignedTinyInteger('kehadiran');
            $table->unsignedSmallInteger('sks_lulus');
            $table->enum('status_kerja', ['Tidak Bekerja', 'Part Time', 'Full Time']);
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->enum('status_kelulusan', ['Lulus', 'Tidak Lulus']);
            $table->timestamps();

            $table->index(['status_kelulusan', 'ipk']);
            $table->index('nim');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
