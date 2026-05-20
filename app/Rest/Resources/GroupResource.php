<?php

namespace App\Rest\Resources;

use App\Models\Group;
use Illuminate\Database\Eloquent\Model;
use Lomkit\Rest\Http\Requests\MutateRequest;
use Lomkit\Rest\Http\Requests\RestRequest;
use Lomkit\Rest\Relations\BelongsTo;
use Lomkit\Rest\Relations\BelongsToMany;
use Lomkit\Rest\Relations\Relation;

class GroupResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>
     */
    public static $model = Group::class;

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
            'created_at',
        ];
    }

    /**
     * The exposed relations that could be provided
     *
     * @param  RestRequest  $request
     * @return Relation[]
     */
    public function relations(RestRequest $request): array
    {
        return [
            BelongsToMany::make('users', UserResource::class),
            BelongsTo::make('owner', UserResource::class),
        ];
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
     * @return array<string>
     */
    public function actions(RestRequest $request): array
    {
        return [];
    }

    /**
     * The instructions that should be linked
     *
     * @param  RestRequest  $request
     * @return array<string>
     */
    public function instructions(RestRequest $request): array
    {
        return [];
    }

    /**
     * @param  MutateRequest  $request
     * @param  string[]  $requestBody
     * @param  Model  $group
     * @return void
     */
    public function mutating(MutateRequest $request, array $requestBody, Model $group): void
    {
        $user = auth()->user();

        if ($requestBody['operation'] === 'create') {
            /** @var Group $group */
            $group->owner_id = $user->id;
        }
    }

    /**
     * @param  RestRequest  $request
     * @return string[]
     */
    public function rules(RestRequest $request): array
    {
        return [
            'name' => 'string',
        ];
    }

    /**
     * @param  RestRequest  $request
     * @return string[]
     */
    public function createRules(RestRequest $request): array
    {
        return [
            'name' => 'required',
        ];
    }
}
