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
        Schema::create('schedules', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("user_id", 21);
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->string("month", 6);
            $table->foreign("month")->references("id")->on("months")->onDelete("cascade");
            $table->string("day", 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
