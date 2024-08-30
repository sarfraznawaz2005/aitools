<?php

namespace Database\Seeders;

use App\Models\NoteFolder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotesFolderSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        //Bot::query()->where('name', 'General')->first()?->delete();

        NoteFolder::query()->create([
            'name' => 'Important',
            'description' => 'A place to store all your important notes.',
            'color' => 'text-red-600',
        ]);

        NoteFolder::query()->create([
            'name' => 'Work',
            'description' => 'A place to store all your work-related notes.',
            'color' => 'text-blue-600',
        ]);

        NoteFolder::query()->create([
            'name' => 'Personal',
            'description' => 'A place to store all your personal notes.',
            'color' => 'text-green-600',
        ]);

        NoteFolder::query()->create([
            'name' => 'Favorites',
            'description' => 'A place to store all your favorite notes.',
            'color' => 'text-purple-600',
        ]);

        NoteFolder::query()->create([
            'name' => 'Miscellaneous',
            'description' => 'A place to store all your miscellaneous notes.',
            'color' => 'text-gray-600',
        ]);

        NoteFolder::query()->create([
            'name' => 'Videos',
            'description' => 'A place to store all your video-related notes.',
            'color' => 'text-yellow-600',
        ]);
    }
}
