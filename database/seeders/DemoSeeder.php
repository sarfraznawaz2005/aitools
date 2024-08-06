<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ApiKey;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        ApiKey::factory(5)->create();

        Conversation::factory(20)->create()->each(function ($conversation) {
            $conversation->messages()->saveMany(Message::factory(30)->make());
        });
    }
}
