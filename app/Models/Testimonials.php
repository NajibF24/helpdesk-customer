<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Testimonials
 * @package App\Models
 * @version February 15, 2021, 3:23 am UTC
 *
 * @property string $title
 * @property string $description
 * @property string $user
 * @property string $position
 * @property string $image
 * @property integer $sort_id
 */
class Testimonials extends Model
{
    use SoftDeletes;

    use HasFactory;

    public $table = 'testimonials';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];



    public $fillable = [
        'title',
        'description',
        'user',
        'position',
        'image',
        'sort_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'user' => 'string',
        'position' => 'string',
        'image' => 'string',
        'sort_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'title' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'user' => 'nullable|string|max:255',
        'position' => 'nullable|string|max:255',
        'image' => 'nullable|string|max:255',
        'sort_id' => 'nullable|integer',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    
}
