<?php

namespace App\Policies;

use App\Access\Controls\LinkControl;
use Lomkit\Access\Policies\ControlledPolicy;

class LinkPolicy extends ControlledPolicy
{
    protected string $control = LinkControl::class;
}
