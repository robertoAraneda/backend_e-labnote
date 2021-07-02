<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    use HasFactory;

    protected $perPage = '10';
    protected $table = 'menus';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected  $fillable = [
        'name',
        'module_id',
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
        return ['id', 'name', 'status'];
    }

    public function getTable():string
    {
        return $this->table;
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }
}
