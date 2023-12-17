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
        Schema::create('audit_plan_areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_user_id')->constrained('users','id');
            $table->foreignId('audit_plan_id')->nullable()->constrained();
            $table->foreignId('area_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_plan_areas');
    }
};
