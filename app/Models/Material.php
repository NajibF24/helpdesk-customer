<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedBy;

    protected $fillable = [
        'id',
        'name',
        'material_code_id',
        'serial_number',
        'brand_id',
        'store_location_id',
        'material_tag',
        'description',
        'specification',
        'image',
        'qty',
        'po_number',
        'created_by',
        'updated_by',
        'deleted_by',
        'inventory_type_id',
        'document'
    ];

    public function goodsIssueDetails() {
        return $this->hasMany(GoodsDetail::class);
    }

    public function goodsDetails() {
        return $this->hasMany(GoodsDetail::class);
    }

    public function materialCode() {
        return $this->belongsTo(MaterialCode::class);
    }

    public function material_code() {
        return $this->belongsTo(MaterialCode::class);
    }

    // public function material_code() {
    //     return $this->belongsTo(MaterialCode::class);
    // }
    public function store_location() {
        return $this->belongsTo(StoreLocation::class, 'store_location_id', 'id');
    }
    public function storeLocation() {
        return $this->belongsTo(StoreLocation::class, 'store_location_id', 'id');
    }
    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    public function inventory_type() {
        return $this->belongsTo(InventoryType::class);
    }

    public function inventoryType() {
        return $this->belongsTo(InventoryType::class);
    }    
}
