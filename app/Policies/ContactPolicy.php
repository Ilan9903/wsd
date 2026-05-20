<?php

namespace App\Policies;

use App\Access\Controls\ContactControl;
use Lomkit\Access\Policies\ControlledPolicy;

class ContactPolicy extends ControlledPolicy
{
    protected string $control = ContactControl::class;
}
