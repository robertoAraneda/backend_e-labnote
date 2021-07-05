<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessTime extends Model
{
    use HasFactory;

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
        return ['id', 'name', 'active'];
    }
}
