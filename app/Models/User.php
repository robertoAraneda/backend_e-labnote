<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @OA\Schema(
 *     title="User",
 *     description="Modelo Usuario",
 *     @OA\Xml(
 *         name="User"
 *     )
 * )
 */
class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;


    /**
     * @OA\Property(
     *      title="RUT",
     *      description="RUT del usuario",
     *      example="12.345.678-9"
     * )
     *
     * @var string
     */
    protected string $rut;

    /**
     * @OA\Property(
     *      title="Nombres",
     *      description="Nombre del usuario utilizado para acceder al sistema",
     *      example="Roberto Alejandro"
     * )
     *
     * @var string
     */
    protected string $names;

    /**
     * @OA\Property(
     *      title="Apellido paterno",
     *      description="Apellido paterno del usuario",
     *      example="Araneda"
     * )
     *
     * @var string
     */
    protected string $lastname;

    /**
     * @OA\Property(
     *      title="Apellido materno",
     *      description="Apellido materno del usuario",
     *      example="Espinoza"
     * )
     *
     * @var string
     */
    protected string $mother_lastname;

    /**
     * @OA\Property(
     *      title="Email",
     *      description="Correo electrónico del usuario",
     *      example="robaraneda@gmail.com"
     * )
     *
     * @var string
     */
    protected string $email;


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
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $fillable = [
        'rut',
        'names',
        'lastname',
        'mother_lastname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    protected $table = 'users';
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
        return ['id', 'rut', 'names', 'lastname', 'mother_lastname', 'email'];
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getPerPage() : string
    {
        return $this->perPage;
    }

}
