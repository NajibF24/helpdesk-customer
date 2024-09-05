<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedBy;

    protected $fillable = [
        'code',
        'name',
        'company_id',
        'location_id',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }
}
