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
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('user_id');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->integer('intervals')->default(12);
            $table->integer('reminder')->default(60);
            $table->dateTime('event_date');
            $table->dateTime('action_date')->nullable();
            $table->string('status', 20)->default('active');
            $table->boolean('is_private')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
