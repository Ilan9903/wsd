<?php

namespace App\Rest\Resources;

use App\Models\FileOrFolder;
use App\Models\Link;
use App\Services\Minio\Bucket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Lomkit\Rest\Concerns\Resource\DisableAuthorizations;
use Lomkit\Rest\Http\Requests\MutateRequest;
use Lomkit\Rest\Http\Requests\RestRequest;
use Lomkit\Rest\Relations\BelongsToMany;
use Lomkit\Rest\Relations\HasMany;
use Lomkit\Rest\Relations\HasOne;
use Lomkit\Rest\Relations\Relation;

class LinkResource extends Resource
{
    use DisableAuthorizations;

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Model>
     */
    public static $model = Link::class;

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
            'url',
            'user_id',
            'password',
            'is_active',
            'expired_at',
            'has_receipt',
            'has_watermark',
            'recipients_email_addresses',
            'message_subject',
            'message',
            'src_folder_id',
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
            HasOne::make('user', UserResource::class),
            BelongsToMany::make('histories', HistoryResource::class),
            HasMany::make('fileOrFolder', FileOrFolderResource::class),
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
        return [
            'onlyTrashed',
        ];
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
            'password' => 'string',
            'expired_at' => 'date_format:Y-m-d H:i:s',
            'is_active' => 'boolean',
            'user_id' => 'string',
            'has_receipt' => 'boolean',
            'has_watermark' => 'boolean',
            'recipients_email_addresses' => 'array',
            'message_subject' => 'string',
            'message' => 'string',
            'src_folder_id' => 'string',
        ];
    }

    /**
     * @param  RestRequest  $request
     * @return string[]
     */
    public function createRules(RestRequest $request): array
    {
        return [
            'password' => 'nullable',
            'expired_at' => 'nullable',
            'has_receipt' => 'nullable',
            'has_watermark' => 'nullable',
            'recipients_email_addresses' => 'nullable',
            'message_subject' => 'nullable',
            'message' => 'nullable',
            'src_folder_id' => 'nullable',
        ];
    }

    /**
     * @param  MutateRequest  $request
     * @param  string[]  $requestBody
     * @param  Model  $link
     * @return void
     */
    public function mutating(MutateRequest $request, array $requestBody, Model $link): void
    {
        $user = auth()->user();

        if ($requestBody['operation'] === 'create') {
            /** @var Link $link */
            $link->user_id = $user->id;
            $link->url = Str::random(70);
        }
    }

    /**
     * @param  MutateRequest  $request
     * @param  string[]  $requestBody
     * @param  Model  $link
     * @return void
     */
    public function mutated(MutateRequest $request, array $requestBody, Model $link): void
    {
        /** @var Link $link */
        (new Bucket)->getBucket();

        if ($requestBody['operation'] === 'create') {
            $srcFolder = FileOrFolder::find($link->src_folder_id);

            $srcFolder->update([
                'link_id' => $link->id,
            ]);

            $folderIds = FileOrFolder::where('parent_id', $srcFolder->id)
                ->pluck('id')
                ->toArray();

            $allParentIds = array_merge([$srcFolder->id], $folderIds);

            FileOrFolder::whereIn('parent_id', $allParentIds)
                ->update([
                    'link_id' => $link->id,
                ]);
        }
    }
}
