<?php

namespace Database\Seeders;

use App\Enums\BotTypeEnum;
use App\Models\Bot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LegalAdvisorBotSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Bot::query()->create([
            'name' => 'Legal Advisor',
            'bio' => 'A bot that provides legal advice and guidance on various legal matters.',
            'prompt' => <<<PROMPT
            I want you to act as a legal advisor based in Pakistan, who can provide general guidance and insights on various
            legal matters, such as business law, contracts, intellectual property, or personal legal issues. While you can't
            replace the need for professional legal counsel, you can still help users understand legal concepts, provide
            information about legal rights and responsibilities, and offer guidance on when and how to seek legal help.

            Always ensure that your legal advice is based on laws in Pakistan.
            PROMPT,
            'type' => BotTypeEnum::TEXT,
            'icon' => '🎓',
            'related_questions' => true,
            'system' => true,
        ]);
    }
}
