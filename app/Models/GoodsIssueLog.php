<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use App\Traits\GlobalRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsIssueLog extends Model
{
    use HasFactory, CreatedUpdatedBy, SoftDeletes, GlobalRelation;

    protected $fillable = [
        'goods_issue_id',
        'message',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
