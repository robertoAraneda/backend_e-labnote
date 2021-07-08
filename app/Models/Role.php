<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @OA\Schema(
 *     title="Role",
 *     description="Modelo Role",
 *     @OA\Xml(
 *         name="Role"
 *     )
 * )
 */
class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;

    protected $table = 'roles';
    protected $perPage = '10';
    protected $fillable = [
        'name',
        'guard_name',
        'created_user_id',
        'updated_user_id',
        'active'
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
        return ['id', 'name'];
    }

    public function getTable():string
    {
        return $this->table;
    }

    public function getPerPage(): string
    {
        return $this->perPage;
    }

    public function created_user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }
}
