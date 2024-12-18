<?php

use Database\Seeders\NotesFolderSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('note_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->default('gray');
            $table->timestamps();
        });

        $seeder = new NotesFolderSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_folders');
    }
};
