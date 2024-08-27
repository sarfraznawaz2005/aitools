<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //User::factory(10)->create();

        //$this->call(BotSeeder::class); now called from within migration due to nativephp

//        if (app()->environment('local')) {
//            $this->call(DemoSeeder::class);
//        }
    }
}
