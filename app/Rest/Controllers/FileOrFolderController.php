<?php

namespace App\Rest\Controllers;

use App\Rest\Resources\FileOrFolderResource;
use Lomkit\Rest\Http\Resource;

class FileOrFolderController extends Controller
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = FileOrFolderResource::class;
}
