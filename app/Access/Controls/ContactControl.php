<?php

namespace App\Access\Controls;

use App\Access\Perimeters\GlobalPerimeter;
use App\Access\Perimeters\OwnPerimeter;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lomkit\Access\Controls\Control;
use Lomkit\Access\Perimeters\Perimeter;

class ContactControl extends Control
{
    /**
     * The model the control refers to.
     *
     * @var class-string<Model>
     */
    protected string $model = Contact::class;

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
                    return $user->can(sprintf('%s_contacts', $method));
                })
                ->should(function () {
                    return true;
                })
                ->query(function (Builder $query) {
                    return $query;
                }),

            OwnPerimeter::new()
                ->allowed(function (User $user, string $method) {
                    return $user->can(sprintf('%s_own_contacts', $method));
                })
                ->should(function (User $user, Contact $contact) {
                    return $contact->users()->where('user_id', $user->id)->exists();
                })
                ->query(function (Builder $query, User $user) {
                    return $query->whereHas('users', function ($query) use ($user) {
                        $query->where('user_id', $user->getKey());
                    });
                }),
        ];
    }
}
