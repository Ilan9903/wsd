<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(10)->create();

        $admin = Role::where('name', 'admin')->where('guard_name', 'web')->first();
        $user = Role::where('name', 'reseller')->where('guard_name', 'web')->first();
        User::factory()->create([
            'email' => 'user@hopla.cloud',
        ])->assignRole($user);

        User::factory()->create([
            'email' => 'admin@hopla.cloud',
        ])->assignRole($admin);
    }
}
