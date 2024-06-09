<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class MarketIndexValue extends Model
{
    protected $table = "market_index_values";

    protected $fillable = ['market_index_id', 'value', 'value_date'];
}
