<?php

namespace App\Access\Controls;

use App\Access\Perimeters\GlobalPerimeter;
use App\Access\Perimeters\OwnPerimeter;
use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lomkit\Access\Controls\Control;
use Lomkit\Access\Perimeters\Perimeter;

class LinkControl extends Control
{
    /**
     * The model the control refers to.
     *
     * @var class-string<Model>
     */
    protected string $model = Link::class;

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
                    return $user->can(sprintf('%s_links', $method));
                })
                ->should(function () {
                    return true;
                })
                ->query(function (Builder $query) {
                    return $query;
                }),

            OwnPerimeter::new()
                ->allowed(function (Model $user, string $method) {
                    return $user->can(sprintf('%s_own_links', $method));
                })
                ->should(function (User $user, Link $link) {
                    return $link->user_id === $user->id;
                })
                ->query(function (Builder $query, User $user) {
                    return $query->whereHas('user', function (Builder $query) use ($user) {
                        $query->where('user_id', $user->getKey());
                    });
                }),
        ];
    }
}
