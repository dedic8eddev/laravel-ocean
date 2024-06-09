<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class MarketIndex extends Model {

    protected $table = "market_indexes";

    protected $fillable = [
        'name', 'issuer', 'frequency', 'vessel_type_id', 'vessel_size', 'source', 'value_unit',
    ];

    public function values() {
        $this->hasMany(MarketIndexValue::class);
    }
}
