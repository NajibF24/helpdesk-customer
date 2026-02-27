<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceiveApproval extends Model
{
    use HasFactory, CreatedUpdatedBy, SoftDeletes;

    protected $fillable = [
        'goods_receive_id',
        'approver_id',
        'status',
        'reason',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
