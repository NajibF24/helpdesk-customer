<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Traits\GlobalRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsDetail extends Model
{
    use HasFactory, CreatedUpdatedBy, SoftDeletes, GlobalRelation;

    public const STATUS_RETURN_SELECTED = 'selected';
    public const STATUS_RETURN_APPROVED = 'approved';
    public const STATUS_RETURN_ISSUED = 'issued';
    public const STATUS_RETURN_HOLD = 'hold';

    protected $table = 'goods_details';

    protected $fillable = [
        'goods_issue_id',
        'goods_receive_id',
        'material_code_id',
        'material_id',
        'pic_user_id',
        'qty',
        'start_date',
        'end_date',
        'remarks',
        'supplier',
        'amount',
        'created_by',
        'updated_by',
        'deleted_by',
        'condition',
        'status_return',
        'name',
        'serial_number',
        'brand_id',
        'material_tag',
        'po_number',
        'description',
        'specification',
        'image',
        'store_location_id',
    ];

    protected $appends = [
        'period'
    ];

    public function goodsIssue() {
        return $this->belongsTo(GoodsIssue::class);
    }

    public function goodsReceive() {
        return $this->belongsTo(GoodsReceive::class);
    }

    // public function header() {
    //     if($this->goods_receive_id) {
    //         return $this->belongsTo(GoodsReceive::class, 'goods_receive_id', 'id');
    //     }

    //     return $this->belongsTo(GoodsIssue::class, 'goods_issue_id', 'id');
    // }

    public function storeLocation() {
        return $this->belongsTo(StoreLocation::class);
    }

    public function material() {
        return $this->belongsTo(Material::class);
    }

    public function brand() {
        return $this->belongsTo(Brand::class);
    }

    // public function goodsReceiveDetail() {
    //     return $this->belongsTo(GoodsReceiveDetail::class);
    // }

    public function materialCode() {
        return $this->belongsTo(MaterialCode::class);
    }

    public function pic() {
        return $this->belongsTo(User::class, 'pic_user_id');
    }

    public function getPeriodAttribute() {
        if($this->start_date && $this->end_date) {
            return date('d F Y', strtotime($this->start_date)).' - ' .date('d F Y', strtotime($this->end_date));
        }   

        return '-';
    }
}
