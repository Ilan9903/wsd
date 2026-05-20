<?php

namespace App\Rest\Controllers;

use App\Models\FileOrFolder;
use App\Models\Link;
use App\Rest\Resources\LinkResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class LinkController extends Controller
{
    /** @var class-string<\Lomkit\Rest\Http\Resource> */
    public static $resource = LinkResource::class;

    public function linkData(Request $request): JsonResponse
    {
        $link = Link::where('url', $request->input('url'))->first();

        $date = Carbon::parse($link->expired_at)->endOfDay();

        if (! $link) {
            return response()->json([
                'message' => 'Lien introuvable',
            ], 404);
        }

        if ($link->expired_at && $date->isPast()) {
            return response()->json([
                'message' => 'Lien expiré',
            ], 410);
        }

        if ($link->password && ! Hash::check($request->input('password'), $link->password)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $fileSystemItems = FileOrFolder::where('link_id', $link->id)
            ->where('id', '!=', $link->src_folder_id)
            ->where('parent_id', $link->src_folder_id)
            ->get();

        return response()->json([
            'link' => $link,
            'fileSystemItems' => $fileSystemItems,
        ]);
    }

    public function linkCheckAccess(Request $request): JsonResponse
    {
        $link = Link::where('url', $request->input('url'))->first();

        $date = Carbon::parse($link->expired_at)->endOfDay();

        if ($link->expired_at && $date->isPast()) {
            return response()->json(['is_expired' => (bool) true], 404);
        }

        return response()->json(['needPassword' => (bool) $link->password], 200);
    }
}
