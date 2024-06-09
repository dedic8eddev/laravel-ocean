<?php

namespace App\Traits;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

trait ValidatesTables {

    use ValidatesRequests;

    public function validateTable(Request $request) {
        $this->validate($request, [
            'visible' => 'nullable|array',

            'filters'             => 'array',
            'filters.*'           => 'array',
            'filters.*.name'      => 'required|string',
            'filters.*.operation' => 'required|string',
            'filters.*.value'     => 'required',

            'paging'              => 'array',
            'paging.current_page' => 'integer',
            'paging.per_page'     => 'integer',

            'sorting' => 'array',
        ]);
    }
}
