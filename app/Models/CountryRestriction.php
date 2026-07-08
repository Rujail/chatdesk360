<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryRestriction extends Model
{
    protected $fillable = ['site_id', 'country_code', 'country_name'];
}