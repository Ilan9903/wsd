<?php

namespace App\Rest\Resources;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Lomkit\Rest\Http\Requests\RestRequest;
use Lomkit\Rest\Relations\BelongsToMany;

/** * @codeCoverageIgnore */
class UserResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @codeCoverageIgnore
     *
     * @var class-string<Model>
     */
    public static $model = User::class;

    /**
     * The exposed fields that could be provided
     *
     * @return array<int, string>
     */
    public function fields(RestRequest $request): array
    {
        return [
            'id',
            'last_name',
            'first_name',
            'email',
            'image',
        ];
    }

    /**
     * The exposed relations that could be provided
     *
     * @return array<int, BelongsToMany>
     */
    public function relations(RestRequest $request): array
    {
        return [
            BelongsToMany::make('groups', GroupResource::class),
        ];
    }

    /**
     * The exposed scopes that could be provided
     *
     * @return array<int, string>
     */
    public function scopes(RestRequest $request): array
    {
        return [];
    }

    /**
     * The exposed limits that could be provided
     *
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
     * @return string[]
     */
    public function actions(RestRequest $request): array
    {
        return [];
    }

    /**
     * The instructions that should be linked
     *
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
    public function rules(RestRequest $request)
    {
        return [
            'last_name' => 'string',
            'first_name' => 'string',
            'email' => 'email',
            'image' => 'nullable|string',
        ];
    }

    /**
     * @param  RestRequest  $request
     * @return string[]
     */
    public function createRules(RestRequest $request)
    {
        return [
            'last_name' => 'required',
            'first_name' => 'required',
            'email' => 'required',
        ];
    }
}
