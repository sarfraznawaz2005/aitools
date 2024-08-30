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
            'description' => 'A place to store all your video notes.',
            'color' => 'bg-pink-200',
        ]);

        NoteFolder::query()->create([
            'name' => 'Work',
            'description' => 'A place to store all your video notes.',
            'color' => 'bg-pink-200',
        ]);

        NoteFolder::query()->create([
            'name' => 'Personal',
            'description' => 'A place to store all your video notes.',
            'color' => 'bg-pink-200',
        ]);

        NoteFolder::query()->create([
            'name' => 'Favorites',
            'description' => 'A place to store all your video notes.',
            'color' => 'bg-pink-200',
        ]);

        NoteFolder::query()->create([
            'name' => 'Miscellaneous',
            'description' => 'A place to store all your general notes.',
            'color' => 'bg-gray-200',
        ]);

        NoteFolder::query()->create([
            'name' => 'Videos',
            'description' => 'A place to store all your video notes.',
            'color' => 'bg-pink-200',
        ]);
    }
}
