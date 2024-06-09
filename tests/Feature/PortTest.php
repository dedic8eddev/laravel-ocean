<?php

namespace Tests\Feature;

use App\Domain\Models\Port;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PortTest extends TestCase
{
    use DatabaseTransactions;

    public function testCreatePort() {
        $this->post('/ports', [
            'name' => 'New Port',
            'code' => 'NPT',
            'country_code' => 'US',
        ])
            ->assertStatus(201)
            ->assertJson(['data' => [
                'name' => 'New Port',
                'code' => 'NPT',
                'country_code' => 'US',
            ]]);

        $this->assertDatabaseHas('ports', [
            'name' => 'New Port',
            'code' => 'NPT',
        ]);
    }

    public function testUpdatePort() {
        $port = factory(Port::class)->create();

        $this->put('/ports/' . $port->id, [
            'name' => 'Updated Port',
            'code' => 'UPT',
            'country_code' => 'US',
        ])
            ->assertStatus(200)
            ->assertJson(['data' => [
                'name' => 'Updated Port',
                'code' => 'UPT',
                'country_code' => 'US',
            ]]);

        $this->assertDatabaseHas('ports', [
            'name' => 'Updated Port',
            'code' => 'UPT',
            'country_code' => 'US',
        ]);
    }
}
