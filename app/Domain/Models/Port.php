<?php

namespace App\Domain\Models;

use App\Traits\HideItems;
use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    use HideItems;

    protected $table = "ports";

    protected $fillable = [
        'name', 'code', 'country_code', 'type', 'size', 'lat', 'lon', 'timezone', 'updated_by_organization', 'hidden'
    ];
}
