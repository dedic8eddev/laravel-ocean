<?php

namespace Tests\Feature;

use App\Domain\Models\Cargo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CargoTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreateCargo() {
        $this->post('/cargoes', [
            'name' => 'New Cargo',
            'description' => 'Cargo Description',
        ])
            ->assertStatus(201)
            ->assertJson(['data' => [
                'name' => 'New Cargo',
                'description' => 'Cargo Description',
            ]]);

        $this->assertDatabaseHas('cargo_grades', [
            'name' => 'New Cargo',
            'description' => 'Cargo Description',
        ]);
    }

    public function testUpdateCargo() {
        $cargo = factory(Cargo::class)->create();

        $this->put('/cargoes/' . $cargo->id, [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ])
            ->assertStatus(200)
            ->assertJson(['data' => [
                'name' => 'Updated Name',
                'description' => 'Updated Description',
            ]]);

        $this->assertDatabaseHas('cargo_grades', [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);
    }
}
