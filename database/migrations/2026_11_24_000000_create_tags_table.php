<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['user_id', 'name']);
        });

        Schema::create('expense_tag', function (Blueprint $table) {
            $table->uuid('expense_id');
            $table->uuid('tag_id');

            $table->primary(['expense_id', 'tag_id']);
            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('expense_tag');
        Schema::dropIfExists('tags');
    }
};
