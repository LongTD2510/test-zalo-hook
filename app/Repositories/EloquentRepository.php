<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;

abstract class EloquentRepository
{
    abstract protected function query(array $options = []): Builder;

    public function find($id)
    {
        return $this->query()->whereKey($id)->first();
    }

    public function findWhere(array $options = [])
    {
        return $this->query($options)->first();
    }

    public function getWhere(array $options = [], $limit = null)
    {
        $query = $this->query($options);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function getAll()
    {
        $query = $this->query();
        return $query->get();
    }

    public function countWhere(array $options = [])
    {
        return $this->query($options)->count();
    }

    public function existsWhere(array $options = [])
    {
        return $this->query($options)->exists();
    }

    public function paginate($args = [], $perPage = 15, $columns = ['*'], $pageParam = 'page')
    {
        return $this->query($args)->paginate($perPage, $columns, $pageParam);
    }

    public function toRawSql($args = [])
    {
        return $this->query($args)->toRawSql();
    }

    public function ddRawSql($args = [])
    {
        return $this->query($args)->ddRawSql();
    }

    protected static function splitOperatorValue($expression): array
    {
        $operator = '=';
        $value = $expression;

        if (str_starts_with($expression, '>=')) {
            $operator = '>=';
            $value = substr($expression, 2);
        } elseif (str_starts_with($expression, '<=')) {
            $operator = '<=';
            $value = substr($expression, 2);
        } elseif (str_starts_with($expression, '>')) {
            $operator = '>';
            $value = substr($expression, 1);
        } elseif (str_starts_with($expression, '<')) {
            $operator = '<';
            $value = substr($expression, 1);
        }

        return [$operator, $value];
    }
}
