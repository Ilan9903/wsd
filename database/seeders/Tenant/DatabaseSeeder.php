<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ThemeSeeder::class,
            // GroupSeeder::class,
            LinkSeeder::class,
            FileOrFolderSeeder::class,
            ContactSeeder::class,
            HistorySeeder::class,
        ]);
    }
}
