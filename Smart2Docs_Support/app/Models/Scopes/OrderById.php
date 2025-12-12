<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class OrderById implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $query = $builder->getQuery();
        $table = $model->getTable();

        if (!$this->hasOrderedById($query, $table))
            $builder->orderBy($table . '.id', 'ASC');
    }

    protected function hasOrderedById($query, $table)
    {
        $orders = $query->orders ?: [];

        foreach ($orders as $order) {
            if ($order['column'] === $table . '.id') {
                return true;
            }
        }

        return false;
    }
}
