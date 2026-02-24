<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['text', 'number', 'select', 'boolean'])->default('text');
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable()->comment('For select type: list of allowed values');
            $table->timestamps();

            $table->unique(['category_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_attributes');
    }
};
