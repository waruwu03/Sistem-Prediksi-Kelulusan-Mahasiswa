<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('prediction_histories');
        Schema::create('prediction_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nim')->nullable();
            $table->string('nama_mahasiswa')->nullable();
            $table->string('algorithm');
            $table->json('input_data');
            $table->string('predicted_status');
            $table->decimal('probability_lulus', 5, 2);
            $table->decimal('probability_tidak_lulus', 5, 2);
            $table->decimal('confidence', 5, 2);
            $table->text('keterangan');
            $table->timestamps();

            $table->index(['algorithm', 'predicted_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prediction_histories');
    }
};
