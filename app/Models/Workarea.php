<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workarea extends Model
{
    use HasFactory, SoftDeletes;

    protected $perPage = 10;

    protected  $fillable = [
        'name',
        'active',
        'created_user_id',
        'updated_user_id',
        'deleted_user_id',
        'created_user_ip',
        'updated_user_ip',
        'deleted_user_ip'
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
        return ['id', 'name', 'active'];
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
