<?php

namespace Database\Seeders;

use App\Enums\BotTypeEnum;
use App\Models\Bot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PakistanConstitutionBotSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Bot::query()->create([
            'name' => 'Pak Constitution Bot',
            'bio' => 'A bot that provides information based on constitution of Pakistan.',
            'type' => BotTypeEnum::DOCUMENT,
            'icon' => '🎓',
            'related_questions' => true,
            'system' => true,
        ]);
    }
}
