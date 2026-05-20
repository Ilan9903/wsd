<?php

namespace App\Rest\Controllers;

use App\Rest\Resources\HistoryResource;
use Lomkit\Rest\Http\Resource;

class HistoryController extends Controller
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = HistoryResource::class;
}
