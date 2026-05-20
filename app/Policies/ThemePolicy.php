<?php

namespace App\Policies;

use App\Access\Controls\ThemeControl;
use Lomkit\Access\Policies\ControlledPolicy;

class ThemePolicy extends ControlledPolicy
{
    protected string $control = ThemeControl::class;
}
