<?php

namespace App\Http\Controllers;

use App\Domain\Models\Cargo;
use App\Domain\Repositories\CargoRepository;
use App\Http\Requests\CargoRequest;
use App\Http\Resources\CargoResource;
use Illuminate\Http\Request;

class CargoController extends Controller {

    private $repo;

    public function __construct(CargoRepository $repo) {
        $this->repo = $repo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        $validationRules = [
            "current_page" => "integer",
            "per_page"     => "integer",
            "name"         => "nullable|string",
        ];

        $this->validate($request, $validationRules);

        $cargoes = $this->repo->table($request->all());

        return CargoResource::collection($cargoes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(CargoRequest $request) {
        $cargo = $this->repo->storeCargo($request->all());
        return new CargoResource($cargo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     */
    public function update(Cargo $cargo, CargoRequest $request) {
        $cargo = $this->repo->updateCargo($cargo, $request->all());
        return new CargoResource($cargo);
    }
}
