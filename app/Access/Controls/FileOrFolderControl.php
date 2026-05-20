<?php

namespace App\Access\Controls;

use App\Access\Perimeters\GlobalPerimeter;
use App\Access\Perimeters\OwnPerimeter;
use App\Models\FileOrFolder;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lomkit\Access\Controls\Control;
use Lomkit\Access\Perimeters\Perimeter;

class FileOrFolderControl extends Control
{
    /**
     * The model the control refers to.
     *
     * @var class-string<Model>
     */
    protected string $model = FileOrFolder::class;

    /**
     * Retrieve the list of perimeter definitions for the current control.
     *
     * @return array<Perimeter> An array of Perimeter objects.
     */
    protected function perimeters(): array
    {
        return [
            GlobalPerimeter::new()
                ->allowed(function (User $user, string $method) {
                    return $user->can(sprintf('%s_file_or_folders', $method));
                })
                ->should(function () {
                    return true;
                })
                ->query(function (Builder $query) {
                    return $query;
                }),
            OwnPerimeter::new()
                ->allowed(function (User $user, string $method) {
                    return $user->can(sprintf('%s_own_file_or_folders', $method));
                })
                ->should(function (User $user, FileOrFolder $fileOrFolder) {
                    return $fileOrFolder->user_id === $user->id;
                })
                ->query(function (Builder $query, Model $user) {
                    return $query->where('user_id', $user->getKey());
                }),
        ];
    }
}
