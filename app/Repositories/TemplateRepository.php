<?php

namespace App\Repositories;

use App\Enums\FileType;
use App\Models\Template;
use App\RepositoryInterfaces\TemplateRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;

class TemplateRepository extends EloquentRepository implements TemplateRepositoryInterface
{
    protected $allowedSortFields = [
        'created_at',
    ];

    protected array $searchPresets = [
        'name' => ['name'],
        'full'  => ['name', 'id'],
    ];

    public function listAll(array $args = []): Builder
    {
        return $this->query($args);
    }

    public function searchTemplates(array $args, string $mode = 'full'): Builder
    {
        return $this->search($args, $mode);
    }

    protected function query(array $args = []): Builder
    {
        return Template::query()
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
            ->when(Arr::has($args, 'user_id'), function ($q) use ($args) {
                $q->where('user_id', Arr::get($args, 'user_id'));
            })
            ->when(Arr::has($args, 'channel'), function ($q) use ($args) {
                $q->where('channel', Arr::get($args, 'channel'));
            })

            // ======================
            // Fetch related template & templates
            // ======================
            ->when(Arr::get($args, 'with_files', true), function ($query) {
                $query->with(['files' => function ($q) {
                    $q->where('type', FileType::TEMPLATE);
                }]);
            })

            // ->when(Arr::get($args, 'with_categories', true), function ($query) {
            //     $query->with('categories');
            // })

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
            });
            
            // ======================
            // Search Query
            // ======================
            // ->when(Arr::get($args, 'category_id'), function ($q, $catId) {
            //     $q->whereHas('categories', function ($q2) use ($catId) {
            //         $q2->where('categories.id', $catId);
            //     });
            // })
            // ->when(Arr::get($args, 'slug_category'), function ($q, $catId) {
            //     $q->whereHas('categories', function ($q2) use ($catId) {
            //         $q2->where('categories.slug', $catId);
            //     });
            // });
    }

    public function search(array $args, string $mode = 'title')
    {
        $fields     = $this->searchPresets[$mode] ?? $this->searchPresets['title'];
        $categoryId = Arr::get($args, 'category_id');

        return Template::query()
            ->when(Arr::get($args, 'keyword'), function ($q, $keyword) use ($fields, $categoryId) {
                $this->applyKeywordSearch($q, $keyword, $fields, $categoryId);
            })
            ->when($categoryId, function ($q, $catId) {
                $q->whereHas('categories', fn($c) => $c->where('categories.id', $catId));
            })
            ->with('categories');
    }

    private function applyKeywordSearch(Builder $query, string $keyword, array $fields, ?int $categoryId = null): void
    {
        $query->where(function ($q2) use ($keyword, $fields, $categoryId) {
            foreach ($fields as $field) {
                if ($field === 'categories.name') {
                    $q2->orWhereHas('categories', function ($c) use ($keyword, $categoryId) {
                        $c->where('categories.name', 'LIKE', "%$keyword%");
                        if ($categoryId) {
                            $c->where('categories.id', $categoryId);
                        }
                    });
                } elseif ($field === 'id') {
                    $q2->orWhere('id', $keyword);
                } else {
                    $q2->orWhere($field, 'LIKE', "%$keyword%");
                }
            }
            if ($categoryId) {
                $q2->whereHas('categories', fn($c) => $c->where('categories.id', $categoryId));
            }
        });
    }
}
