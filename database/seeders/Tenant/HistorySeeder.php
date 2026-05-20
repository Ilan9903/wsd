<?php

namespace Database\Seeders\Tenant;

use App\Models\History;
use App\Models\User;
use Hopla\HistoryManagement\Enums\HistoryActionType;
use Hopla\HistoryManagement\Enums\HistoryModelType;
use Illuminate\Database\Seeder;

class HistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory(3)->create();
        $users->each(function (User $user) {
            History::factory()->create([
                'action' => HistoryActionType::LOGIN,
                'model_type' => HistoryModelType::USER,
                'user_id' => $user->id,
                'description' => "Connexion de {$user->last_name} {$user->first_name}",
            ]);
            History::factory()->create([
                'action' => HistoryActionType::LOGOUT,
                'model_type' => HistoryModelType::USER,
                'user_id' => $user->id,
                'description' => "Déconnexion de {$user->last_name} {$user->first_name}",
            ]);
        });
    }
}
