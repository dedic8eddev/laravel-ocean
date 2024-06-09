<?php

namespace App\Traits\Queries;

use Illuminate\Support\Arr;

trait SearchesQueries {

    /**
     * Applies the search string to the provided attributes
     *
     * Any string keyed attribute indicates a relationship lookup,
     * where the key is the relation's name and the value the related entity's attribute(s) to search
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @param array $params The search parameters
     * @param array $attributes The attributes to search in
     *
     * @return \App\Traits\Queries\SearchesQueries|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function search($query, $params, array $attributes) {
        $search = Arr::get($params, 'search');

        if ($search == null) {
            return $query;
        }

        return $this->applySearch($query, $search, $attributes);
    }

    private function applySearch($query, $search, array $attributes) {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        return $query->where(function ($builder) use ($attributes, $search) {
            /** @var \Illuminate\Database\Eloquent\Builder $builder */
            foreach ($attributes as $relation => $attribute) {
                if (is_numeric($relation)) {
                    $builder->orWhere($attribute, 'ILIKE', $search . '%');
                } else {
                    $builder->orWhereHas($relation, function ($query) use ($attribute, $search) {
                        $attribute = is_array($attribute) ? $attribute : [$attribute];
                        $this->applySearch($query, $search, $attribute);
                    });
                }
            }
        });
    }
}