<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loinc extends Model
{
    use HasFactory;

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 10;


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected  $fillable = [
        'loinc_num',
        'long_common_name'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'loinc';

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable():string
    {
        return $this->table;
    }

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'loinc_num';


    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
}
