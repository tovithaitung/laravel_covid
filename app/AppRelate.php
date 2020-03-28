<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppRelate extends Model
{
    //
    protected $table = 'app_relate';
    protected $primaryKey = 'app_relate_id';
    protected $fillable = ['appid','title','keywords','relate','kwplanner','installs','releaseDate','status','created_at'];
}
