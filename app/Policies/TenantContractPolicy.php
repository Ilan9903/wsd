<?php

namespace App\Policies;

use App\Access\Controls\TenantContractControl;
use Lomkit\Access\Policies\ControlledPolicy;

class TenantContractPolicy extends ControlledPolicy
{
    protected string $control = TenantContractControl::class;
}
