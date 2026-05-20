<?php

namespace App\Rest\Controllers;

use App\Rest\Resources\TenantContractResource;
use Lomkit\Rest\Http\Resource;

class TenantContractController extends Controller
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = TenantContractResource::class;
}
