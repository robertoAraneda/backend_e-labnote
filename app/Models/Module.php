<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $perPage = '10';
    protected $table = 'modules';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $fillable = [
        'name',
        'icon',
        'url',
        'slug',
        'active',
        'created_user_id',
        'updated_user_id',
        'deleted_user_id',
        'created_user_ip',
        'updated_user_ip',
        'deleted_user_ip'
    ];

    /**
     * @return string
     */
    public function getPerPage(): string
    {
       return env('DEFAULT_PER_PAGE');

    }


    public function getTable():string
    {
        return $this->table;
    }

    public function laboratories(): BelongsToMany
    {
        return $this->belongsToMany(Laboratory::class, 'laboratory_modules');
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'module_permission')
            ->withPivot('user_id', 'created_at', 'updated_at')
            ->withTimestamps();;
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
