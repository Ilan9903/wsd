<?php

namespace App\Rest\Controllers;

use App\Rest\Resources\Resource as resource;
use App\Rest\Resources\UserResource;

/** * @codeCoverageIgnore */
class UsersController extends Controller
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<resource>
     */
    public static $resource = UserResource::class;
}
