<?php

namespace App\Domain\Models;

use App\Traits\HideItems;
use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    use HideItems;

    protected $table = "cargo_grades";

    protected $fillable = [
        'name', 'description', 'stowage_factor_bale', 'stowage_factor_grain', 'stowage_factor_unit_id'
    ];
}
