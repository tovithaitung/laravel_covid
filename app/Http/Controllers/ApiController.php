<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class ApiController extends Controller
{
    //
    private $token = 'eAv8Oo04cyUKSiPWf1OZgUmAYTFyd5vznewPBn03MiTCvTw9Ca56gfTqE8OV';
    public function getTopApp(Request $request){
		$list = DB::table('keywords')->where('releaseDate','>=',time()-86400*30*12*3)->where('appid','!=','')->get();
        // $list = json_encode($list);
        // $list = json_decode($list,true);
        return response()->json($list);
    }
}
