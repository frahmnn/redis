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
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("user_id", 21);
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->string("dateid", 8);
            $table->string("place");
            $table->enum("type", ["Pendampingan", "Advokasi", "Humas", "Lainnya"]);
            $table->text("description");
            $table->string("assistant", 21)->nullable();
            $table->foreign("assistant")->references("id")->on("users")->onDelete("cascade");
            $table->enum("status", ["Menunggu", "Selesai", "Dibatalkan"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
