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
        Schema::create('identities', function (Blueprint $table){
            $table->uuid("id")->primary();
            $table->string("user_id", 21)->nullable();
            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
            $table->string("name");
            $table->enum("gender", ["Laki-Laki", "Perempuan"])->nullable();
            $table->enum("special_role", ["Pendamping", "Admin"])->nullable();
            $table->enum("division", ["Pendampingan", "Advokasi", "Humas", "BPH"])->nullable();
            $table->string("student_id", 10)->unique()->nullable();
            $table->uuid("major_id")->nullable();
            $table->foreign("major_id")->references("id")->on("majors")->onDelete("set null");
            $table->string("generation", 4)->nullable();
            $table->string("whatsapp_number")->nullable();
            $table->string("email");
            $table->boolean("verified");
            $table->datetime("requested")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identities');
    }
};
