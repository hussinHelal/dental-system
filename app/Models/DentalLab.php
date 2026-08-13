<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalLab extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'phone', 'address', 'notes'];

    public function cases()
    {
        return $this->hasMany(LabCase::class);
    }
}
