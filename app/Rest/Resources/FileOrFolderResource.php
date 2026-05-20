<?php

namespace App\Rest\Resources;

use App\Models\FileOrFolder;
use Illuminate\Database\Eloquent\Model;
use Lomkit\Rest\Http\Requests\MutateRequest;
use Lomkit\Rest\Http\Requests\RestRequest;
use Lomkit\Rest\Relations\BelongsTo;
use Lomkit\Rest\Relations\Relation;

class FileOrFolderResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>
     */
    public static $model = FileOrFolder::class;

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
            'parent_id',
            'size',
            'delete_at',
            'type',
            'path',
            'user_id',
            'link_id',
            'mime_type',
            'updated_at',
            'created_at',
            'deleted_at',
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
     * @param  Model  $files
     * @return void
     */
    public function mutating(MutateRequest $request, array $requestBody, Model $files): void
    {
        $user = auth()->user();

        if ($requestBody['operation'] === 'create') {
            /** @var FileOrFolder $files */
            $files->user_id = $user->id;
        }
    }
}
