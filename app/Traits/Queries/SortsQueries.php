<?php

namespace App\Traits\Queries;

trait SortsQueries {

    /**
     * Sorts a query based on provided parameters
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query The query to apply
     *     sorting to
     * @param array $input The input parameters. A 'sorting' key is expected, which is a map of field => order pairs
     * @param array $filters The allowed fields to sort by. Aliases can be used for fields, e.g. ["name" =>
     *     "users.name"]
     * @param array $default The default sorting to apply
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function sort($query, array $input, $filters = [], $default = []) {
        $input = data_get($input, 'sorting', []);
        $sortings = count($input) > 0 ? $input : $default;

        $aliases = array_filter(array_keys($filters), "is_string");

        foreach ($sortings as $field => $order) {
            // Ignore invalid sorting
            if (!is_string($field) || !in_array(strtoupper($order), ['ASC', 'DESC'])) continue;

            if (in_array($field, $aliases)) {
                $query->orderBy($filters[$field], $order);
            } elseif (in_array($field, $filters)) {
                $query->orderBy($field, $order);
            }
        }

        return $query;
    }
}
