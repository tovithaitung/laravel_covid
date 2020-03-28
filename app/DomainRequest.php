<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DomainRequest extends Model
{
    //
    protected $table = 'domain_requests';
    protected $primaryKey = 'domain_request_id';
    protected $fillable = ['domain','status','created_at','link_out'];
}
