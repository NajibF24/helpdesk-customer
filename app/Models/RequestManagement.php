<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestManagement extends Model
{
    use HasFactory;

    protected $table = 'request_management';

    public function service() {
        return $this->belongsTo(Service::class, 'request_name', 'id');
    }

    public function warehouses() 
    {
        return $this->hasMany(RequestManagementWarehouse::class);
    }
}
