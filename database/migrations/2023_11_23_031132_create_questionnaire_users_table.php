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
        Schema::create('questionnaire_users', function (Blueprint $table) {
            $table->id('questionnaire_user_id');
            $table->string('name');
            $table->string('comment');
            $table->foreignId('facility_id')->constrained('facilities','id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_users');
    }
};
