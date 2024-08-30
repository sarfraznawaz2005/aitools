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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('note_folder_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->boolean('pinned')->default(false);
            $table->boolean('archived')->default(false);
            $table->timestamp('reminder_at')->nullable();
            $table->json('code')->nullable(); // json field to store: html, width, height, ratio
            $table->string('url')->nullable();
            $table->string('image')->nullable();
            $table->string('author')->nullable();
            $table->string('author_url')->nullable();
            $table->string('source')->nullable();
            $table->string('source_url')->nullable();
            $table->string('source_icon')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
