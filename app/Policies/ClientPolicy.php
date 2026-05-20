<?php

namespace App\Policies;

use App\Access\Controls\ClientControl;
use Lomkit\Access\Policies\ControlledPolicy;

class ClientPolicy extends ControlledPolicy
{
    protected string $control = ClientControl::class;
}
