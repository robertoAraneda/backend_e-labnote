<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NobilisAnalyte extends Model
{
    use HasFactory;

    protected $table = 'nobilis_observations_service_requests';

    protected $keyType = 'string';

    public $incrementing = false;

    protected  $fillable = [
        'id',
        'description',
    ];

    public function getPerPage(): string
    {
        return env('DEFAULT_PER_PAGE');
    }
}
