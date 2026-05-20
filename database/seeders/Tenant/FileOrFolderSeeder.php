<?php

namespace Database\Seeders\Tenant;

use App\Models\FileOrFolder;
use App\Models\User;
use Illuminate\Database\Seeder;

class FileOrFolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $users = User::take(5)->get();

        foreach ($users as $user) {

            $root = FileOrFolder::factory()
                ->folder()
                ->forUser($user)
                ->create([
                    'name' => 'root-'.$user->id,
                    'parent_id' => null,
                ]);

            $this->generateTree($root, $user, depth: 2);
        }
    }

    /**
     * Génère une arborescence récursive.
     */
    private function generateTree(FileOrFolder $parentFolder, $user, int $depth = 1): void
    {
        if ($depth <= 0) {
            return;
        }

        $folders = FileOrFolder::factory()
            ->count(rand(3, 6))
            ->folder()
            ->forUser($user)
            ->create([
                'parent_id' => $parentFolder->id,
            ]);

        FileOrFolder::factory()
            ->count(rand(5, 15))
            ->file()
            ->forUser($user)
            ->create([
                'parent_id' => $parentFolder->id,
            ]);

        foreach ($folders as $subFolder) {
            $this->generateTree($subFolder, $user, depth: $depth - 1);
        }
    }
}
