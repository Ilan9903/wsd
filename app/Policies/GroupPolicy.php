<?php

namespace App\Policies;

use App\Access\Controls\GroupControl;
use Lomkit\Access\Policies\ControlledPolicy;

class GroupPolicy extends ControlledPolicy
{
    protected string $control = GroupControl::class;
}
