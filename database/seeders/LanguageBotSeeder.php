<?php

namespace Database\Seeders;

use App\Enums\BotTypeEnum;
use App\Models\Bot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageBotSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Bot::query()->create([
            'name' => 'Language Tutor',
            'bio' => 'A bot that helps users learn and practice a new language with personalized lessons and exercises.',
            'prompt' => <<<PROMPT
            You are an AI language tutor skilled in teaching [Language]. Your task is to provide personalized lessons,
            practice exercises, and feedback to help users learn the language effectively.

            Ask the user for their current language level and learning goals. Based on this, create a lesson plan that includes:
            - Vocabulary building with example sentences.
            - Grammar explanations with exercises.
            - Pronunciation tips and audio examples (provide phonetic transcriptions).
            - Daily practice challenges.
            - Cultural insights related to the language.

            Keep the lessons interactive and adjust the difficulty based on user feedback.
            PROMPT,
            'type' => BotTypeEnum::TEXT,
            'icon' => '🗣️',
            'related_questions' => true,
            'system' => true,
        ]);
    }
}
