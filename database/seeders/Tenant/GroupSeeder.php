<?php

namespace Database\Seeders\Tenant;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Group::factory(5)->create()->each(function ($group) {
            $group->users()->attach(User::factory(3)->create());
        });
    }
}
