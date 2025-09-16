<?php

namespace App\Repositories;

use App\Enums\FileType;
use App\Models\Category;
use App\Models\Config;
use App\RepositoryInterfaces\ConfigRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class ConfigRepository extends EloquentRepository implements ConfigRepositoryInterface
{
    protected $allowedSortFields = [
        'created_at',
    ];
    protected function query(array $args = []): Builder
    {
        return Config::query()
            // ======================
            // Default Query
            // ======================
            ->when(isset($args['with_count']), function ($query) use ($args) {
                $query->withCount($args['with_count']);
            })
            ->when(isset($args['with']), function ($query) use ($args) {
                $query->with($args['with']);
            })
            ->when(Arr::get($args, 'where-in'), function ($query, $param) {
                $query->whereIn($param[0], $param[1]);
            })
            ->when(Arr::get($args, 'where_in'), function ($query, $param) {
                $query->whereIn($param[0], $param[1]);
            })
            ->when(Arr::get($args, 'where_not_in'), function ($query, $param) {
                $query->whereNotIn($param[0], $param[1]);
            })
            ->when(Arr::get($args, 'between'), function ($query, $param) {
                $query->whereBetween($param[0], $param[1]);
            })
            ->when(Arr::get($args, 'id'), function ($query, $param) {
                if (is_array($param)) {
                    $query->whereIn('id', $param);
                } else {
                    $query->whereKey($param);
                }
            })
            ->when(Arr::get($args, 'ids'), function ($query, $param) {
                $query->whereIn('id', $param);
            })

            // ======================
            // Fields need to be searched
            // ======================
            ->when(Arr::has($args, 'key'), function ($q) use ($args) {
                $q->where('key', Arr::get($args, 'key'));
            })

            ->when(Arr::has($args, 'key_like'), function ($q) use ($args) {
                $q->where('key', 'like', '%' . Arr::get($args, 'key_like') . '%');
            })

            // ======================
            // Default Query
            // ======================
            ->when(!empty($args['sort_field']) && in_array($args['sort_field'], $this->allowedSortFields), function ($query) use ($args) {
                $sortOrder = $args['sort_order'] ?? 'asc';

                if ($sortOrder == 'descend') {
                    $sortOrder = 'desc';
                }

                if ($sortOrder == 'ascend') {
                    $sortOrder = 'asc';
                }

                if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
                    $sortOrder = 'asc';
                }

                $query->orderBy($args['sort_field'], $sortOrder);
            })
            ;
    }
}
