<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * County model
 *
 * @apiDefine CountyModel
 * @apiSuccess {Number} id County ID
 * @apiSuccess {String} name County name
 * @apiSuccess {Object[]} cities List of cities belonging to this county
 */
class County extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // A county has many cities
    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
