<?php

namespace App\Policies;

use App\Access\Controls\TenantConfigControl;
use Lomkit\Access\Policies\ControlledPolicy;

class TenantConfigPolicy extends ControlledPolicy
{
    protected string $control = TenantConfigControl::class;
}
