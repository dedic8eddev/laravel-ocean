<?php

namespace Tests\Unit;

use App\Domain\Models\Cargo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CargoTest extends TestCase
{
    use DatabaseTransactions;

    public function testPassInvalidParams() {
        $this->json('GET', '/cargoes?current_page=a')
            ->assertStatus(422);

        $this->json('GET', '/cargoes?per_page=a')
            ->assertStatus(422);

        $this->json('POST', '/cargoes', [
            'description' => 'Cargo Description'
        ])->assertStatus(422);

        $this->json('POST', '/cargoes', [
            'name' => 'Invalid Cargo',
            'stowage_factor_bale' => 'Invalid Value',
        ])->assertStatus(422);

        $this->json('POST', '/cargoes', [
            'name' => 'Invalid Cargo',
            'stowage_factor_grain' => 'Invalid Value',
        ])->assertStatus(422);

        $this->json('POST', '/cargoes', [
            'name' => 'Invalid Cargo',
            'stowage_factor_unit_id' => 2.3,
        ])->assertStatus(422);

        $this->json('POST', '/cargoes', [
            'name' => 'Invalid Cargo',
            'updated_by_organization' => 'Invalid Value',
        ])->assertStatus(422);

        $this->json('POST', '/cargoes', [
            'name' => 'Invalid Cargo',
            'hidden' => 'Invalid Value',
        ])->assertStatus(422);
    }

    public function testUpdateNonExistingCargo() {
        $this->put('/cargoes/9999', [
            'name' => 'Updated Name'
        ])->assertStatus(404);
    }
}
