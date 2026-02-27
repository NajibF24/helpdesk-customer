<?php

namespace App\Models;

use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contact';

    protected $fillable = [
        "id",
        "nik",
        "email",
        "salutation",
        "name",
        "first_name",
        "last_name",
        "company",
        "country",
        "location",
        "position",
        "job_title",
        "organization",
        "place_of_birth",
        "birth_date",
        "join_date",
        "resign_date",
        "gender",
        "marriage_status",
        "alternate_email",
        "mobile_phone",
        "phone",
        "address",
        "manager",
        "picture",
        "status",
        "tier",
        "notification",
        "function",
        "type",
        "created_by",
        "updated_by",
        "is_agent",
        "coverage_windows",
    ];

    public function jobTitle() {
        return $this->belongsTo(JobTitle::class, 'job_title', 'id');
    }
}
