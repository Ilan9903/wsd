<?php

namespace App\Rest\Resources;

use App\Models\TenantContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Lomkit\Rest\Actions\Action;
use Lomkit\Rest\Http\Requests\RestRequest;
use Lomkit\Rest\Instructions\Instruction;
use Lomkit\Rest\Relations\Relation;

/** * @codeCoverageIgnore */
class TenantContractResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>
     */
    public static $model = TenantContract::class;

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
            'used_storage',
            'allocated_storage',
            'allocated_users',
            'bucket',
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
            'allocated_storage' => 'int',
            'allocated_users' => 'int',
        ];
    }

    /**
     * @param  RestRequest  $request
     * @return string[]
     */
    public function createRules(RestRequest $request)
    {
        return [
            'allocated_storage' => 'required',
            'allocated_users' => 'required',
        ];
    }
}
