<?php

namespace App\Policies;

use App\Access\Controls\HistoryControl;
use Lomkit\Access\Policies\ControlledPolicy;

class HistoryPolicy extends ControlledPolicy
{
    protected string $control = HistoryControl::class;
}
