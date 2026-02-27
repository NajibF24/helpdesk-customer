<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaterialCode extends Model
{
    use HasFactory, SoftDeletes, CreatedUpdatedBy;

    protected $fillable = [
        'code',
        'name',
        'uom',
        'material_group_id',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $appends = [
        'uom_label'
    ];

    public function uom() {
        return $this->belongsTo(Uom::class);
    }

    public function materialGroup() {
        return $this->belongsTo(MaterialGroup::class);
    }

    public function material_group() {
        return $this->belongsTo(MaterialGroup::class);
    }

    public function material() {
        return $this->belongsTo(Material::class, 'id', 'material_code_id');
    }

    public function getUomLabelAttribute() {
        return @$this->uom->name ?? '';
    }

    // public function scopeSearchRelation($query, $payload) {
    //     $searchValue = $payload['search']['value'];
    //     if($searchValue) {
    //         return $query->whereHas('materialGroup', function($q) use($searchValue) {
    //             $q->where('name', 'like', "$searchValue");
    //         });
    //     }
    // }
}
