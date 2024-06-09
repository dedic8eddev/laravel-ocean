<?php

namespace Tests\Feature;

use App\Domain\Models\Country;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CountryTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetCountries() {
        $country = factory(Country::class)->create();

        $this->get('/countries')
            ->assertStatus(200)
            ->assertJson(['data' => [
                [
                    'code' => $country->code,
                    'name' => $country->name,
                ]
            ]]);
    }
}
