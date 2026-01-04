<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
