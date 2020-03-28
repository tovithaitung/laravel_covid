<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    //
    protected $table = 'app_top';
    protected $primaryKey = 'app_top_id';
    protected $fillable = ['appid','title','keywords','relate','kwplanner','installs','releaseDate','status','created_at'];
}
