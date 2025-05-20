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
        // Create lokasi table first
        Schema::create('lokasi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lokasi');
            $table->decimal('latitude', 10, 8)->default(0);
            $table->decimal('longitude', 11, 8)->default(0);
            $table->timestamps();
        });

        // Then create barang table with foreign key
        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->unsignedBigInteger('id_lokasi');
            $table->timestamps();

            // Add foreign key constraint
            $table->foreign('id_lokasi')
                  ->references('id')
                  ->on('lokasi')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order
        Schema::dropIfExists('barang');
        Schema::dropIfExists('lokasi');
    }
};
