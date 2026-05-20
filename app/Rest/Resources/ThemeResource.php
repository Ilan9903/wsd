<?php

namespace App\Rest\Resources;

use App\Models\Theme;
use Illuminate\Database\Eloquent\Model;
use Lomkit\Rest\Http\Requests\RestRequest;

/** * @codeCoverageIgnore */
class ThemeResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>
     */
    public static $model = Theme::class;

    /**
     * The exposed fields that could be provided
     *
     * @param  RestRequest  $request
     * @return array<int, string>
     */
    public function fields(RestRequest $request): array
    {
        return [
            'id',
            'name',
            'background',
            'button_primary_color',
            'button_secondary_color',
            'button_text_primary_color',
            'button_text_secondary_color',
            'text_primary_color',
            'text_secondary_color',
            'logo_light_theme',
            'logo_dark_theme',
            'status',
        ];
    }

    /**
     * The exposed relations that could be provided
     *
     * @param  RestRequest  $request
     * @return string[]
     */
    public function relations(RestRequest $request): array
    {
        return [];
    }

    /**
     * The exposed scopes that could be provided
     *
     * @param  RestRequest  $request
     * @return array<int, string>
     */
    public function scopes(RestRequest $request): array
    {
        return [];
    }

    /**
     * The exposed limits that could be provided
     *
     * @param  RestRequest  $request
     * @return array<int>
     */
    public function limits(RestRequest $request): array
    {
        return [
            10,
            25,
            50,
        ];
    }

    /**
     * The actions that should be linked
     *
     * @param  RestRequest  $request
     * @return string[]
     */
    public function actions(RestRequest $request): array
    {
        return [];
    }

    /**
     * The instructions that should be linked
     *
     * @param  RestRequest  $request
     * @return string[]
     */
    public function instructions(RestRequest $request): array
    {
        return [];
    }

    /**
     * @param  RestRequest  $request
     * @return string[]
     */
    public function createRules(RestRequest $request): array
    {
        return [
            'name' => 'required',
            'background' => 'required',
            'button_primary_color' => 'required',
            'button_secondary_color' => 'required',
            'button_text_primary_color' => 'required',
            'button_text_secondary_color' => 'required',
            'text_primary_color' => 'required',
            'text_secondary_color' => 'required',
            'status' => 'boolean|required',
        ];
    }

    /**
     * @param  RestRequest  $request
     * @return string[]
     */
    public function rules(RestRequest $request): array
    {
        return [
            'name' => 'string|max:255',
            'background' => 'string|max:255',
            'button_primary_color' => 'string|max:255',
            'button_secondary_color' => 'string|max:255',
            'button_text_primary_color' => 'string|max:255',
            'button_text_secondary_color' => 'string|max:255',
            'text_primary_color' => 'string|max:255',
            'text_secondary_color' => 'string|max:255',
            'logo_light_theme' => 'string|max:255',
            'logo_dark_theme' => 'string|max:255',
            'status' => 'boolean',
        ];
    }
}
