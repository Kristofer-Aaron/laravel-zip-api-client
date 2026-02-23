<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * City model
 *
 * @apiDefine CityModel
 * @apiSuccess {Number} id City ID
 * @apiSuccess {String} zip Postal code
 * @apiSuccess {String} name City name
 * @apiSuccess {Number} county_id Associated county ID
 * @apiSuccess {Object} county Associated county object when included
 */
class City extends Model
{
    use HasFactory;

    // Columns that can be mass-assigned
    protected $fillable = [
        'zip',
        'name',
        'county_id',
    ];

    // Relationship: one city belongs to one county
    public function county()
    {
        return $this->belongsTo(County::class);
    }
}
