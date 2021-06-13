<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


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


    /**
     * @OA\Property(
     *      title="name",
     *      description="Nombre del rol",
     *      example="Este es el nombre del rol"
     * )
     *
     * @var string
     */
    protected string $name;

    /**
     * @OA\Property(
     *      title="Guard name",
     *      description="Nombre del guard",
     *      example="Este es el nombre del guard del rol"
     * )
     *
     * @var string
     */
    protected string $guard_name;

    /**
     * @OA\Property(
     *     title="Created at",
     *     description="Created at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    protected string $created_at;

    /**
     * @OA\Property(
     *     title="Updated at",
     *     description="Updated at",
     *     example="2020-01-27 17:50:45",
     *     format="datetime",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    protected string $updated_at;

    protected $table = 'roles';
    protected $perPage = '10';

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

}
