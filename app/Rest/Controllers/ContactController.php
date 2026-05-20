<?php

namespace App\Rest\Controllers;

use App\Rest\Resources\ContactResource;
use Lomkit\Rest\Http\Resource;

class ContactController extends Controller
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<resource>
     */
    public static $resource = ContactResource::class;
}
