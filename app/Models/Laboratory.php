<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laboratory extends Model
{
    use HasFactory;

    protected $perPage = '10';
    protected $table = 'laboratories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $fillable = [
        'name',
        'address',
        'email',
        'phone',
        'redirect',
        'status'
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
        return ['id', 'name', 'address', 'email', 'phone', 'status', 'redirect'];
    }

    public function getTable():string
    {
        return $this->table;
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'laboratory_modules')
            ->withPivot('user_id', 'created_at', 'updated_at')
            ->withTimestamps();;
    }

    /**
     * Scope a query to only include active users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

}
