<?php

namespace App\Rest\Controllers;

use App\Rest\Resources\ClientResource;
use Lomkit\Rest\Http\Resource;

class ClientController extends Controller
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = ClientResource::class;
}
