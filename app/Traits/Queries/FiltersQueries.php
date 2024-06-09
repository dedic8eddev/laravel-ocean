<?php

namespace App\Traits\Queries;

trait FiltersQueries {

    protected $filterCallbacks = [];

    /**
     * Applies filters to the specified query.
     * All input params that are allowed will be applied as where equalities, except those present in the callbacks
     *
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query The query to filter
     * @param array $input The input params as key=>value
     * @param array $allowed The params that should be used for filtering
     * @param array $defaults Any default values for the filtering parameters
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
public function filter($query, array $input, array $allowed = [], array $defaults = []) {
        // Merge input with defaults, with input having precedence
        $params = $input + $defaults;
        foreach ($allowed as $field) {
            if (!array_key_exists($field, $params)) {
                continue;
            }

            $value = $params[$field];

            // A callback has been defined, use that
            if (array_key_exists($field, $this->filterCallbacks)) {
                $callback = $this->filterCallbacks[$field];
                $callback($query, $value);
                continue;
            }

            $query->where($field, $value);
        }

        return $query;
    }

    /**
     * Register a callback for a field
     *
     * @param string $field The field to register the callback for
     * @param callable $callback The callback for the specified field. It receives two arguments: the query builder and
     *     the input param
     */
    public function registerCallback(string $field, callable $callback) {
        $this->filterCallbacks[$field] = $callback;
    }

}
