<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prediction_histories', function (Blueprint $table): void {
            if (! Schema::hasColumn('prediction_histories', 'nim')) {
                $table->string('nim')->nullable()->after('user_id');
            }
            if (! Schema::hasColumn('prediction_histories', 'nama_mahasiswa')) {
                $table->string('nama_mahasiswa')->nullable()->after('nim');
            }
        });
    }

    public function down(): void
    {
        Schema::table('prediction_histories', function (Blueprint $table): void {
            $table->dropColumn(['nim', 'nama_mahasiswa']);
        });
    }
};
