<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait PaginateCollection
{
    /**
     * Paginate cho Collection
     *
     * @param  \Illuminate\Support\Collection|array  $items
     * @param  int  $perPage
     * @param  int|null  $page
     * @param  array  $options
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function paginateFromApiZalo(array $apiResponse, int $perPage = 15, int $page = null, array $options = []): LengthAwarePaginator
    {
        $page = $page ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);

        $items = Collection::make($apiResponse['data'] ?? []);
        $total = $apiResponse['metadata']['total'] ?? $items->count();

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            $options
        );
    }
}
