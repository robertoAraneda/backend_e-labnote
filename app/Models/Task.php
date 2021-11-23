<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected  $fillable = [
        'based_on',
        'part_of',
        'business_status_id',
        'authored_on',
        'owner_id',
    ];

    /**
     * @return string
     */
    public function getPerPage(): string
    {
        return env('DEFAULT_PER_PAGE');

    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }


}
