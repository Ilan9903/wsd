<?php

namespace App\Rest\Resources;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Lomkit\Rest\Http\Requests\RestRequest;
use Lomkit\Rest\Http\Resource as RestResource;

/** * @codeCoverageIgnore */
abstract class Resource extends RestResource
{
    /**
     * Build a "search" query for fetching resource.
     */
    public function searchQuery(RestRequest $request, Builder $query): Builder
    {
        return $query->controlled();
    }

    /**
     * Build a query for mutating resource.
     */
    public function mutateQuery(RestRequest $request, Builder $query): Builder
    {
        return $query->controlled();
    }

    /**
     * Build a "destroy" query for the given resource.
     *
     * @return Builder
     */
    public function destroyQuery(RestRequest $request, Builder $query)
    {
        return $query->controlled();
    }

    /**
     * Build a "restore" query for the given resource.
     *
     * @return Builder
     */
    public function restoreQuery(RestRequest $request, Builder $query)
    {
        return $query->controlled();
    }

    /**
     * Build a "forceDelete" query for the given resource.
     *
     * @return Builder
     */
    public function forceDeleteQuery(RestRequest $request, Builder $query)
    {
        return $query->controlled();
    }
}
