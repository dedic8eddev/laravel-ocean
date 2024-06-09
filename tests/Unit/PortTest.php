<?php

namespace Tests\Unit;

use App\Domain\Models\Port;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PortTest extends TestCase
{
    use DatabaseTransactions;

    public function testPassInvalidParams() {
        $this->json('GET', '/ports?current_page=a')
            ->assertStatus(422);

        $this->json('GET', '/ports?per_page=a')
            ->assertStatus(422);

        $this->json('POST', '/ports', [
            'code' => 'CODE',
            'country_code' => 'CC',
        ])->assertStatus(422);

        $this->json('POST', '/ports', [
            'name' => 'New Port',
            'code' => 'NPT',
        ])->assertStatus(422);

        $this->json('POST', '/ports', [
            'name' => 'New Port',
            'country_code' => 'cc',
        ])->assertStatus(422);

        $this->json('POST', '/ports', [
            'name' => 'New Port',
            'code' => 'NPT',
            'country_code' => 'US',
            'lat' => 'lat',
        ])->assertStatus(422);

        $this->json('POST', '/ports', [
            'name' => 'New Port',
            'code' => 'NPT',
            'country_code' => 'US',
            'lon' => 'lon',
        ])->assertStatus(422);

        $this->json('POST', '/ports', [
            'name' => 'Invalid Cargo',
            'updated_by_organization' => 'Invalid Value',
        ])->assertStatus(422);

        $this->json('POST', '/ports', [
            'name' => 'Invalid Cargo',
            'hidden' => 'Invalid Value',
        ])->assertStatus(422);
    }

    public function testUpdateNonExistingPort() {
        $this->put('/ports/9999', [
            'name' => 'Updated Name'
        ])->assertStatus(404);
    }
}
