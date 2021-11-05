<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModulePermission extends Model
{
    use HasFactory;

    protected $table = 'module_permission';

    protected $fillable = [
        'module_id',
        'permission_id',
        'user_id'
    ];
}
