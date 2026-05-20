<?php

namespace App\Rest\Controllers;

use App\Rest\Resources\GroupResource;
use Lomkit\Rest\Http\Resource;

class GroupController extends Controller
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = GroupResource::class;
}
