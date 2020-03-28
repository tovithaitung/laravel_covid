<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    //
    protected $table = 'domain_out';
    protected $primaryKey = 'domain_out_id';
    protected $fillable = ['domain_name','status','code','created_at','dr','link_out', 'check_status','RDomain','ahrefs_rank'];
}
