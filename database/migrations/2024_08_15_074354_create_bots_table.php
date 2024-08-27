<?php

use App\Enums\BotTypeEnum;
use Database\Seeders\BotSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bots', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('bio');
            $table->text('prompt')->nullable();
            $table->string('type')->default(BotTypeEnum::TEXT);
            $table->string('icon')->nullable();
            $table->boolean('system')->default(false);
            $table->boolean('related_questions')->default(true);
            $table->timestamps();
        });

        $seeder = new BotSeeder();
        $seeder->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bots');
    }
};
