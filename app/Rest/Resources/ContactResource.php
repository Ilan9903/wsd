<?php

namespace App\Rest\Resources;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Builder;
use Lomkit\Rest\Http\Requests\RestRequest;
use Lomkit\Rest\Relations\BelongsToMany;
use Lomkit\Rest\Relations\Relation;

class ContactResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>
     */
    public static $model = Contact::class;

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
            'last_name',
            'first_name',
            'created_at',
            'email',
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
            'last_name' => 'string',
            'first_name' => 'string',
            'email' => 'email',
        ];
    }

    /**
     * @param  RestRequest  $request
     * @return string[]
     */
    public function createRules(RestRequest $request): array
    {
        return [
            'last_name' => 'required',
            'first_name' => 'required',
            'email' => 'required',
        ];
    }

    /**
     * The scout fields that could be provided.
     *
     * @param  RestRequest  $request
     * @return array<string>
     */
    public function scoutFields(RestRequest $request): array
    {
        return ['id', 'last_name', 'first_name', 'email'];
    }

    /**
     * Build a "search" scout query for fetching resource.
     *
     * @param  RestRequest  $request
     * @param  Builder<Contact>  $query
     * @return Builder<Contact>
     */
    public function searchScoutQuery(RestRequest $request, Builder $query)
    {
        return $query;
    }
}
