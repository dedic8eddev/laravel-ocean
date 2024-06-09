<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationBlacklist extends Model
{
    protected $table = "organization_blacklist";

    protected $primaryKey = null;

    public $timestamps = false;

    public $incrementing = false;
}
