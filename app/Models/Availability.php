<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Availability extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
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

    /**
     * @return string
     */
    public function getPerPage(): string
    {
        return env('DEFAULT_PER_PAGE');
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
