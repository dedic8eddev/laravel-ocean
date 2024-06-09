<?php

namespace App\Domain\Repositories;

use App\Traits\Queries\FiltersQueries;
use App\Traits\Queries\PaginatesQueries;
use App\Traits\Queries\SearchesQueries;
use App\Traits\Queries\SortsQueries;

class BaseRepository {
    use PaginatesQueries, SearchesQueries, SortsQueries, FiltersQueries;
}