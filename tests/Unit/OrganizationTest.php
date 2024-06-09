<?php

namespace Tests\Unit;

use App\Domain\Models\Organization;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Util\TableQueryFiltersBuilder;

class OrganizationTest extends TestCase {

    use DatabaseTransactions;

    public function testPagination() {
        $org1 = factory(Organization::class)->create(['name' => 'Aabc']);
        $org2 = factory(Organization::class)->create(['name' => 'Abbc']);
        $org3 = factory(Organization::class)->create(['name' => 'Abcc']);

        $query = TableQueryFiltersBuilder::instance()
                                         ->sorting('name', 'asc')
                                         ->paging(2, 1)
                                         ->build();
        $this->get('/organizations?' . http_build_query($query))
             ->assertStatus(200)
             ->assertJson(['data' => [
                 $org1->toArray(),
                 $org2->toArray(),
             ]]);

        $query = TableQueryFiltersBuilder::instance()
                                         ->sorting('name', 'asc')
                                         ->paging(2, 2)
                                         ->build();
        $this->get('/organizations?' . http_build_query($query))
             ->assertStatus(200)
             ->assertJson(['data' => [
                 $org3->toArray(),
             ]]);
    }

    public function testFilter() {
        $org1 = factory(Organization::class)->create();
        $org2 = factory(Organization::class)->create();

        $query = TableQueryFiltersBuilder::instance()
                                         ->filter('name', 'contains', $org1->name)
                                         ->paging(10, 1)
                                         ->build();
        $this->get('/organizations?' . http_build_query($query))
             ->assertStatus(200)
             ->assertJson(['data' => [
                 $org1->toArray(),
             ]]);

        $query = TableQueryFiltersBuilder::instance()
                                         ->filter('domain', 'contains', $org2->domain)
                                         ->paging(10, 1)
                                         ->build();
        $this->get('/organizations?' . http_build_query($query))
             ->assertStatus(200)
             ->assertJson(['data' => [
                 $org2->toArray(),
             ]]);
    }

    public function testSearch() {
        $org1 = factory(Organization::class)->create();
        $org2 = factory(Organization::class)->create();

        $this->get('/organizations?search=' . $org1->name)
             ->assertStatus(200)
             ->assertJson(['data' => [
                 $org1->toArray(),
             ]]);

        $this->get('/organizations?search=' . $org2->domain)
             ->assertStatus(200)
             ->assertJson(['data' => []]);
    }
}
