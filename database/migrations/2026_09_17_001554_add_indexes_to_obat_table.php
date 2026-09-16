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
        Schema::table('obat', function (Blueprint $table) {
            // Mempercepat filter kategori, status stok, dan kadaluarsa
            // (dipakai scopeFilter di tabel + export Excel/PDF)
            $table->index('category');
            $table->index('stock');
            $table->index('expired_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropIndex(['stock']);
            $table->dropIndex(['expired_date']);
        });
    }
};
