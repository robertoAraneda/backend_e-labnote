<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends \Spatie\Permission\Models\Permission
{
    use HasFactory;

    protected $table = 'permissions';
    protected $perPage = '10';

    protected $fillable = [
        'name',
        'guard_name',
        'model',
        'action',
        'description'
    ];

    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getPerPage(): string
    {
        return env('DEFAULT_PER_PAGE');

    }

}
