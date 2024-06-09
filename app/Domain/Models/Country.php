<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = "countries";

    protected $fillable = [
        'code', 'name', 'updated_by_organization', 'hidden',
    ];
}
