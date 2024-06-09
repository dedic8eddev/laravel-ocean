<?php

namespace App\Http\Controllers;

use App\Domain\Models\Port;
use App\Domain\Repositories\PortRepository;
use App\Http\Requests\PortRequest;
use App\Http\Resources\PortResource;
use Illuminate\Http\Request;

class PortController extends Controller {

    private $repo;

    public function __construct(PortRepository $repo) {
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
            "code"         => "nullable|string",
            "country_code" => "nullable|string",
            "type"         => "nullable|string",
            "size"         => "nullable|string",
        ];

        $this->validate($request, $validationRules);

        $ports = $this->repo->table($request->all());

        return PortResource::collection($ports);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function store(PortRequest $request) {
        $port = $this->repo->storePort($request->all());
        return new PortResource($port);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     */
    public function update(Port $port, PortRequest $request) {
        $port = $this->repo->updatePort($port, $request->all());
        return new PortResource($port);
    }
}
