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
            'color' => 'text-red-600',
        ]);

        NoteFolder::query()->create([
            'name' => 'Work',
            'color' => 'text-blue-600',
        ]);

        NoteFolder::query()->create([
            'name' => 'Personal',
            'color' => 'text-green-600',
        ]);

        NoteFolder::query()->create([
            'name' => 'Favorites',
            'color' => 'text-purple-600',
        ]);

        NoteFolder::query()->create([
            'name' => 'Miscellaneous',
            'color' => 'text-gray-600',
        ]);

        NoteFolder::query()->create([
            'name' => 'Reminders',
            'color' => 'text-cyan-600',
        ]);

        NoteFolder::query()->create([
            'name' => 'Links',
            'color' => 'text-indigo-600',
        ]);
    }
}
