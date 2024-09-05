<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventoryType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'created_by',
        'deleted_by',
        'updated_by'
    ];

    protected $appends = [
        'label'
    ];

    public function getLabelAttribute() {
        return ucwords(str_replace('_', ' ', $this->title));
    }
}
