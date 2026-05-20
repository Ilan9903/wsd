<?php

namespace App\Policies;

use App\Access\Controls\FileOrFolderControl;
use Lomkit\Access\Policies\ControlledPolicy;

class FileOrFolderPolicy extends ControlledPolicy
{
    protected string $control = FileOrFolderControl::class;
}
