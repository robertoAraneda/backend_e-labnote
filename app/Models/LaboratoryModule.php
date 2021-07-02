<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryModule extends Model
{
    use HasFactory;

    protected $perPage = '10';
    protected $table = 'laboratory_modules';
}
