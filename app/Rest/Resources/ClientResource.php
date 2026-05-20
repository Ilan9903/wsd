<?php

namespace App\Rest\Resources;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Lomkit\Rest\Actions\Action;
use Lomkit\Rest\Http\Requests\RestRequest;
use Lomkit\Rest\Instructions\Instruction;
use Lomkit\Rest\Relations\Relation;

/** * @codeCoverageIgnore */
class ClientResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>
     */
    public static $model = Client::class;

    /**
     * The exposed fields that could be provided
     *
     * @param  RestRequest  $request
     * @return array<string>
     */
    public function fields(RestRequest $request): array
    {
        return [
            'id',
            'name',
            'email',
            'phone_number',
            'address',
            'zipcode',
            'avatar',
            'tenant',
            'domain',
            'bucket',
            'front_route',
            'allocated_users',
            'allocated_storage',
            'used_storage',
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
        return [];
    }

    /**
     * The exposed scopes that could be provided
     *
     * @param  RestRequest  $request
     * @return Scope[]
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
     * @return Action[]
     */
    public function actions(RestRequest $request): array
    {
        return [];
    }

    /**
     * The instructions that should be linked
     *
     * @param  RestRequest  $request
     * @return Instruction[]
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
            'name' => 'string|max:255',
            'email' => 'email|max:255',
            'phone_number' => 'string|max:255',
            'address' => 'string|max:255',
            'zipcode' => 'string|max:255',
            'avatar' => 'nullable|string|max:255',
            'tenant' => 'string|max:255',
            'domain' => 'string|max:255',
            'front_route' => 'string|max:255',
            'allocated_users' => 'int',
            'allocated_storage' => 'int',
        ];
    }

    /**
     * @param  RestRequest  $request
     * @return string[]
     */
    public function createRules(RestRequest $request)
    {
        return [
            'name' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            'address' => 'required',
            'zipcode' => 'required',
            'avatar' => 'nullable',
            'tenant' => 'required',
            'domain' => 'required',
            'front_route' => 'required',
            'allocated_users' => 'required',
            'allocated_storage' => 'required',
        ];
    }
}
