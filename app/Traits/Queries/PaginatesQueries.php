<?php

namespace App\Traits\Queries;

use Illuminate\Support\Arr;

trait PaginatesQueries {

    protected $page_size     = 20;
    protected $max_page_size = 100;

    /**
     * Return the paginated results of a query, based on the parameters provided
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @param $params
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function paginate($query, $params) {
        $page_size = Arr::get($params, 'per_page', $this->page_size);
        $page_size = min($page_size, $this->max_page_size);
        return array_key_exists('current_page',$params) ? $query->paginate($page_size, ['*'], 'page', $params['current_page']) : $query->limit($page_size)->get();
    }

}