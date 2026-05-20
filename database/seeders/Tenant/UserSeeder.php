<?php

namespace Database\Seeders\Tenant;

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

        $roleUser = Role::where('name', 'user')->where('guard_name', 'api')->firstOrFail();
        $roleAdmin = Role::where('name', 'admin')->where('guard_name', 'api')->firstOrFail();

        User::factory()->create([
            'email' => 'user@hopla.cloud',
        ])->assignRole($roleUser);

        User::factory()->create([
            'email' => 'admin@hopla.cloud',
        ])->assignRole($roleAdmin);
    }
}
