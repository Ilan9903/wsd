<?php

namespace App\Access\Controls;

use App\Access\Perimeters\GlobalPerimeter;
use App\Access\Perimeters\OwnPerimeter;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lomkit\Access\Controls\Control;
use Lomkit\Access\Perimeters\Perimeter;

class UserControl extends Control
{
    /**
     * The model the control refers to.
     *
     * @var class-string<Model>
     */
    protected string $model = User::class;

    /**
     * Retrieve the list of perimeter definitions for the current control.
     *
     * @return array<Perimeter> An array of Perimeter objects.
     */
    protected function perimeters(): array
    {
        return [
            GlobalPerimeter::new()
                ->allowed(function (Model $user, string $method) {
                    return $user->can(sprintf('%s_users', $method));
                })
                ->should(function () {
                    return true;
                })
                ->query(function (Builder $query) {
                    return $query;
                }),
            OwnPerimeter::new()
                ->allowed(function (Model $user, string $method) {
                    return $user->can(sprintf('%s_own_users', $method));
                })
                ->should(function (Model $user, Model $model) {
                    return $model->is($user);
                })
                ->query(function (Builder $query, Model $user) {
                    return $query->where('id', $user->getKey());
                }),
        ];
    }
}
