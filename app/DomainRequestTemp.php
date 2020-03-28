<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainRequestTemp extends Model
{
    protected $table = 'domain_request_temp';
    protected $primaryKey = 'domain_request_id';
    protected $fillable = ['domain','status','created_at'];
}
