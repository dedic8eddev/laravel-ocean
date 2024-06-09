<?php

namespace App\Http\Controllers;

use App\Domain\Repositories\CountryRepository;
use App\Http\Resources\CountryResource;
use Illuminate\Http\Request;

class CountryController extends Controller {

    private $repo;

    public function __construct(CountryRepository $repo) {
        $this->repo = $repo;
    }

    public function index(Request $request) {
        $validationRules = [
            "name" => "nullable|string",
        ];

        $this->validate($request, $validationRules);

        $marketIndexes = $this->repo->table($request->all());

        return CountryResource::collection($marketIndexes);
    }
}
