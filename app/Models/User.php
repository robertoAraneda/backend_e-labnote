<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
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
    use HasRoles, HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
        'phone',
        'created_user_id',
        'created_user_ip',
        'updated_user_id',
        'updated_user_ip',
        'deleted_user_id',
        'deleted_user_ip',
        'active'
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


    public function getTable(): string
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

    /**
     * Scope a query to only include active users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * @return BelongsToMany
     */
    public function laboratories(): BelongsToMany
    {
        return $this->belongsToMany(Laboratory::class, 'laboratory_users')
            ->withPivot('user_id', 'created_at', 'updated_at')
            ->withTimestamps();
    }

    /**
     * @return BelongsTo
     */
    public function createdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    /**
     * @return BelongsTo
     */
    public function updatedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_user_id');
    }

    /**
     * @return BelongsTo
     */
    public function deletedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_user_id');
    }

}
