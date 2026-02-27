<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiveLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_receive_id',
        'message',
    ];
}
