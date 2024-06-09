<?php

namespace Tests\Unit;

use App\Domain\Models\MarketIndex;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TestExistsPropertyRule extends TestCase
{
    // in order to test the exists trait, we need a sample model
    // and here we define it
    private $sampleModel = MarketIndex::class;

    private $table; private $table_id;

    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);

        /** @var \Illuminate\Database\Eloquent\Model $model */
        $model = new $this->sampleModel();
        $this->table = $model->getTable();
        $this->table_id = $model->getKeyName();
    }

    public function getSampleInstance() {
        return factory($this->sampleModel)->create();
    }

    public function testWorksWithValidInput() {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make([
            "indexes" => [
                [ "id" => $this->getSampleInstance()->getKey() ],
                [ "id" => $this->getSampleInstance()->getKey() ],
            ]
        ], [
            "indexes" => "exists_property:id,$this->table,$this->table_id"
        ]);

        $this->assertTrue($validator->passes());
    }

    public function testFailsWithInvalidInput() {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make([
            "indexes" => [
                [ "id" => -1 ],
            ]
        ], [
            "indexes" => "exists_property:id,$this->table,$this->table_id"
        ]);

        $this->assertTrue($validator->fails());
    }

    public function testFailsWithMixedInput() {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make([
            "indexes" => [
                [ "id" => -1 ],
                [ "id" => $this->getSampleInstance()->getKey() ]
            ]
        ], [
            "indexes" => "exists_property:id,$this->table,$this->table_id"
        ]);

        $this->assertTrue($validator->fails());
    }
}
