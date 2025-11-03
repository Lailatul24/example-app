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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // peminjam
            $table->foreignId('facility_id')->constrained()->onDelete('cascade'); // barang yang dipinjam
            $table->integer('quantity')->default(1); // jumlah barang yang dipinjam
            $table->date('borrowed_at'); // tanggal pinjam
            $table->date('returned_at')->nullable(); // tanggal pengembalian (null = belum dikembalikan)
            $table->enum('status', ['dipinjam', 'dikembalikan', 'hilang'])->default('dipinjam');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
