<?php

use Database\Seeders\LanguageBotSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {

    public function up(): void
    {
        $seeder = new LanguageBotSeeder();
        $seeder->run();
    }

    public function down(): void
    {
        //
    }

};
