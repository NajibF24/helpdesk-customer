<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Traits\GlobalRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceive extends Model
{
    use HasFactory, CreatedUpdatedBy, SoftDeletes, GlobalRelation;

    protected $table = 'goods_receives';

    protected $fillable = [
        'code',
        'subject',
        'status',
        'inventory_type_id',
        'description',
        'files',
        'form_builder_data',
        'form_builder_json',
        'form_builder_id',
        'company_id',
        'requestor',
        'request_management_id',
        'next_approver_id', //contact id
        'service_id',
        'servicesubcategory_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'token',
        'goods_issue_id'
    ];

    protected $appends = [
        'created_date',
        'status_label'
    ];

    public function requestManagement() {
        return $this->belongsTo(RequestManagement::class);
    }

    public function inventory_type() {
        return $this->belongsTo(InventoryType::class);
    }

    public function goods_issue() {
        return $this->belongsTo(GoodsIssue::class, 'goods_issue_id', 'id');
    }

    public function details() {
        return $this->hasMany(GoodsDetail::class, 'goods_receive_id', 'id');
    }

    public function getCreatedDateAttribute() {
        return date('d F Y H:i', strtotime($this->created_at));
    }

    public function contactRequestor() {
        return $this->belongsTo(Contact::class, 'requestor', 'id');
    }

    public function nextApprover() {
        return $this->belongsTo(Contact::class, 'next_approver_id', 'id');
    }

    public function inventoryType() {
        return $this->belongsTo(InventoryType::class);
    }

    public function getStatusLabelAttribute() {
        if($this->status == 'full_approved') return 'Fully Approved';

        return ucwords(str_replace('_', ' ', $this->status));
    }

}
