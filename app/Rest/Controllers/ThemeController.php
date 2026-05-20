<?php

namespace App\Rest\Controllers;

use App\Http\Requests\UpdateLogo;
use App\Models\Theme;
use App\Rest\Resources\ThemeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Lomkit\Rest\Http\Requests\MutateRequest;
use Lomkit\Rest\Http\Resource;

class ThemeController extends Controller
{
    /**
     * The resource the controller corresponds to.
     *
     * @var class-string<\Lomkit\Rest\Http\Resource>
     */
    public static $resource = ThemeResource::class;

    /**
     * @param  UpdateLogo  $request
     * @return JsonResponse
     */
    public function updateLogo(UpdateLogo $request): JsonResponse
    {

        $disk = Storage::disk('public');

        $theme = Theme::findOrFail($request->id);
        /** @var Theme $theme */
        $image = $request->file('image');
        $isDarkLogo = $request->isDarkLogo;
        $themeFolder = $isDarkLogo ? 'dark' : 'light';
        $newPath = 'company_logo/'.tenant()->id.'/themelogo/'.$themeFolder.'/'.$image->getClientOriginalName();

        $oldLogo = $isDarkLogo ? $theme->logo_dark_theme : $theme->logo_light_theme;

        if ($oldLogo && $disk->exists($oldLogo)) {
            $disk->delete($oldLogo);
        }

        $disk->putFileAs(
            'company_logo/'.tenant()->id.'/themelogo/'.$themeFolder,
            $image,
            $image->getClientOriginalName()
        );

        $theme->update([
            $isDarkLogo ? 'logo_dark_theme' : 'logo_light_theme' => $newPath,
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Logo mis à jour avec succès',
        ]);
    }

    /**
     * @param  MutateRequest  $request
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function beforeMutate(MutateRequest $request): void
    {
        if ($request['mutate']['0']['operation'] === 'update') {
            Theme::where('status', 1)->update(['status' => 0]);
        }
    }
}
