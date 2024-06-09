<?php

namespace App\Http\Controllers;

use App\Domain\Repositories\OrganizationRepository;
use App\Http\Resources\OrganizationResource;
use App\Traits\ValidatesTables;
use Illuminate\Http\Request;

class OrganizationController extends Controller {

    use ValidatesTables;

    private $repo;

    public function __construct(OrganizationRepository $repo) {
        $this->repo = $repo;
    }

    public function index(Request $request) {
        $this->validateTable($request);

        $organizations = $this->repo->table($request->all());

        return OrganizationResource::collection($organizations);
    }
}
