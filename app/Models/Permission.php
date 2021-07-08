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
    public static function getListJsonStructure(): array
    {
        return [
            'data' => [self::getObjectJsonStructure()],
            'links',
            'meta',
        ];
    }

    public static function getObjectJsonStructure(): array
    {
        return
            ['id', 'name', 'action', 'description', 'model']
        ;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getPerPage()
    {
        return $this->perPage;
    }
}
