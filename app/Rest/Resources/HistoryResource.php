<?php

namespace App\Rest\Resources;

use App\Models\History;
use Illuminate\Database\Eloquent\Model;
use Lomkit\Rest\Http\Requests\RestRequest;
use Lomkit\Rest\Relations\BelongsTo;
use Lomkit\Rest\Relations\Relation;

class HistoryResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>
     */
    public static $model = History::class;

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
            'action',
            'description',
            'ip_address',
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
            BelongsTo::make('user', UserResource::class),
            BelongsTo::make('links', LinkResource::class),
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
     * @param  RestRequest  $request
     * @return string[]
     */
    public function rules(RestRequest $request): array
    {
        return [
            'action' => 'string',
            'description' => 'string',
            'ip_address' => 'string',
        ];
    }

    /**
     * @param  RestRequest  $request
     * @return string[]
     */
    public function createRules(RestRequest $request): array
    {
        return [
            'action' => 'required',
            'description' => 'required',
            'ip_address' => 'required',
        ];
    }
}
