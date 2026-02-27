<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreLocation extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedBy;

    protected $fillable = [
        'code',
        'name',
        'warehouse_id',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function warehouse() {
        return $this->belongsTo(Warehouse::class);
    }
}
