<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Traits\GlobalRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsIssue extends Model
{
    use HasFactory, CreatedUpdatedBy, SoftDeletes, GlobalRelation;

    protected $table = 'goods_issues';

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
        'deleted_by'
    ];

    protected $appends = [
        'created_date',
        'status_label'
    ];

    public function inventoryManagementDetails() {
        return $this->hasMany(GoodsDetail::class, 'goods_issue_id', 'id');
    }

    public function inventoryType() {
        return $this->belongsTo(InventoryType::class);
    }

    public function inventory_type() {
        return $this->belongsTo(InventoryType::class);
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

    public function details() {
        return $this->hasMany(GoodsDetail::class);
    }

    public function goodsReceive() {
        return $this->belongsTo(GoodsReceive::class, 'id', 'goods_issue_id');
    }

    public function goods_receive() {
        return $this->belongsTo(GoodsReceive::class, 'id', 'goods_issue_id');
    }

    public function requestManagement() {
        return $this->belongsTo(RequestManagement::class);
    }

    public static function mapMaterialListColumn($type) {
        $defaultColumns = ['material_code', 'material_name', 'qty', 'group', 'pic', 'period', 'remarks'];


        switch($type) {
            case 'borrow':
                return array_filter($defaultColumns, function($row) {
                    return !in_array($row, ['supplier', 'amount']);
                });
            case 'deploy':
                return array_filter($defaultColumns, function($row) {
                    return !in_array($row, ['period', 'supplier']);
                });
            case 'in_repair':
                return array_filter($defaultColumns, function($row) {
                    return !in_array($row, ['pic']);
                });
            default:
                return $defaultColumns;
        }

    }
    
    public function getStatusLabelAttribute() {
        if($this->status == 'full_approved') return 'Fully Approved';

        return ucwords(str_replace('_', ' ', $this->status));
    }

}
