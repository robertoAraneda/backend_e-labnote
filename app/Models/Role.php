<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;

    protected $table = 'roles';
    protected $fillable = [
        'name',
        'guard_name',
        'created_user_id',
        'updated_user_id',
        'active'
    ];

    public function getTable():string
    {
        return $this->table;
    }

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
