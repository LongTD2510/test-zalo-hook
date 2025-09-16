<?php

namespace App\Repositories;

use App\Enums\FileType;
use App\Models\Category;
use App\RepositoryInterfaces\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class CategoryRepository extends EloquentRepository implements CategoryRepositoryInterface
{
    protected $allowedSortFields = [
        'created_at',
    ];
    protected function query(array $args = []): Builder
    {
        return Category::query()
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
            ->when(Arr::has($args, 'status'), function ($q) use ($args) {
                $q->where('status', Arr::get($args, 'status'));
            })
            ->when(Arr::get($args, 'name'), function ($q, $param) {
                $q->where('name', 'LIKE', "%$param%");
            })
            ->when(Arr::get($args, 'slug'), function ($q, $param) {
                $q->where('slug', $param);
            })
            ->when(Arr::has($args, 'is_featured'), function ($q) use ($args) {
                $q->where('is_featured', Arr::get($args, 'is_featured'));
            })

            // ======================
            // Fetch related parent category
            // ======================
            ->when(Arr::has($args, 'with_parent'), function ($query) use ($args) {
                if (Arr::get($args, 'with_parent', true)) {
                    $query->with('parent');
                }
            })


            // ======================
            // Fetch related child category
            // ======================
            ->when(Arr::has($args, 'with_children'), function ($query) use ($args) {
                if (Arr::get($args, 'with_children', true)) {
                    $query->with('children');
                }
            })

            // ======================
            // Fetch related files
            // ======================
            ->when(Arr::has($args, 'with_files'), function ($query) use ($args) {
                if (Arr::get($args, 'with_files', true)) {
                    $query->with(['files' => function ($fileQuery) {
                        $fileQuery->where('type', FileType::CATEGORY);
                    }]);
                }
            })

            // ======================
            // Fetch related posts
            // ======================
            ->when(Arr::has($args, 'with_posts'), function ($query) use ($args) {
                if (Arr::get($args, 'with_posts', true)) {
                    $query->with(['posts' => function ($postQuery) use ($args) {
                        $postQuery->with(['files' => function ($fileQuery) {
                            $fileQuery->where('type', FileType::POST);
                        }]);
                    }]);
                }
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
