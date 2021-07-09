<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;

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
        'active'
    ];

    /**
     * @return string
     */
    public function getPerPage(): string
    {
        $this->perPage = env('DEFAULT_PER_PAGE');
        return $this->perPage;
    }

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
        return ['id', 'name','icon', 'url', 'slug', 'active'];
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

}
