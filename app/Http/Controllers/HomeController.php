<?php

namespace App\Http\Controllers;

use App\DomainRequest;
use Illuminate\Http\Request;
use App\Domain;

require_once '../app/helpers.php';
use Auth,Hash;

use Illuminate\Support\Facades\DB;
use App\Imports\ListCheckImport; 
use Maatwebsite\Excel\Facades\Excel;

ini_set('memory_limit','-1');

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        set_error_handler(null);
        echo 'Web under construction';die;
        set_exception_handler(null);
        $this->middleware('auth');
    
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if(Auth::user()->ip != 1){
            if( strpos(Auth::user()->ip,$_SERVER["HTTP_CF_CONNECTING_IP"]) == false){
                echo $_SERVER["HTTP_CF_CONNECTING_IP"].'<br>';
                echo 'block';die;
            }
        }

        $filters = $request->filter;
        foreach ($filters as $item) {
            if(($item == 2 || $item == 3) && Auth::user()->email != 'admin@domain.com'){
                return 'permission - request';
            }
        }

        $search = $request->search;

        if ($search) {
            $search = str_replace(' ', '', $search);
            $searchs = explode(',', $search);
        }

        // $domainLinks = DomainRequest::where('status',1)->get();
        // $domains = [];
        // foreach ($domainLinks as $key => $domainLink) {
        //     $domains[] = $domainLink->domain;
        // }
        
        //DB::table('users')->where('email','admin@domain.com')->update(['password' =>  Hash::make('Apktovi5678@')]);
        //DB::table('users')->insert(['name'=> 'Tú','email' =>'tu@domain.com','password' =>  Hash::make('Tovi1234@')]);
        if($request->page){
            $page = $request->page;
        } else {
            $page = 1;
        }
        $params = $request->all();
        $where = array();
        foreach ($params as $key => $param) {
            if($key != 'page' && $key != 'sort' && $key != 'type' && $key != 'filter' && $key != 'search' && $key != 'code'){
                if($param !=  -1){
                    if($key == 'price'){
                        $where[] = array($key,'<=',$param);
                    } else {
                        $where[] = array($key,'>=',$param);
                    }
                }

            }
        }

        if(isset($params['sort']) && isset($params['type'])){
            $sort = $params['sort'];
            $type = $params['type'];
        } else {
            $sort = 'price';
            $type = 'asc';
        }
        //$where[] = array('statusRdomain',1);whereNull
        //$maxPrice = Domain::where('isBuy',1)->where('price','>',0)->where('is_del',0)->orderBy('price','desc')->first();
        //$maxAnchor = Domain::where('isBuy',1)->where('price','>',0)->where('is_del',0)->orderBy('TotalAnchor','desc')->first();
        //$maxReferr = Domain::where('isBuy',1)->where('price','>',0)->where('is_del',0)->orderBy('RDomain','desc')->first();
        
        // domcop
        if(in_array(9, $filters)){
            $where[] = array('price','>',-1);
           // $where[] = array('is_del',0);
            if(isset($params['sort']) && isset($params['type'])){
            $sort = $params['sort'];
            $type = $params['type'];
            } else {
                $sort = 'id';
                $type = 'asc';
            }
            //print_r($where);die;
            //$list =DB::table('domain_domcop_select')->where($where)->where('is_del',0)->orderBy($sort,$type)->limit(20)->offset(($page-1)*20)->get();
            $limit = ($page-1)*20;
            $list = DB::select(DB::raw('SELECT * FROM domain_domcop_select where is_del = 0 and statusRdomain = 1 AND statusFinal = 1 limit '.$limit.',20'));
            //$totalPage = DB::table('domain_domcop_select')->where($where)->where('is_del',0)->count();
            $totalPage = 100000;
            return view('domain/domcop',['list' => $list,'totalPage' => round($totalPage/20), 'maxPrice' => $maxPrice->price, 'maxAnchor' => $maxAnchor->TotalAnchor, 'maxReferr' => $maxReferr->RDomain,'total' => $totalPage, 'page' => $page, 'domainLinks' => $domains,]);
        } else {
        
            $where[] = array('price','>',0);
            // enddomcop
            if ($search) {
                $list = Domain::where('isBuy',1)->where($where)->where('is_del',0)->whereIn('domain_name', $searchs)->orderBy($sort,$type)->limit(20)->offset(($page-1)*20)->get();
                $totalPage = DB::table('domain_out')->where($where)->where('is_del',0)->whereIn('domain_name', $searchs)->orderBy($sort,$type)->count();
            }elseif($filters){
                $list = Domain::where('isBuy',1)->where($where)->where('is_del',0)->whereIn('check_status', $filters)->orderBy($sort,$type)->limit(20)->offset(($page-1)*20)->get();
                $totalPage = DB::table('domain_out')->where($where)->where('is_del',0)->whereIn('check_status', $filters)->orderBy($sort,$type)->count();
            } else {
                $list = Domain::where('isBuy',1)->where($where)->where('is_del',0)->where('check_status', '=', 0)->orderBy($sort,$type)->limit(20)->offset(($page-1)*20)->get();
                $totalPage = DB::table('domain_out')->where($where)->where('is_del',0)->orderBy($sort,$type)->count();
            }
        }
        return view('domain/index',['list' => $list,'totalPage' => round($totalPage/20), 'maxPrice' => $maxPrice->price, 'maxAnchor' => $maxAnchor->TotalAnchor, 'maxReferr' => $maxReferr->RDomain,'total' => $totalPage, 'page' => $page, 'domainLinks' => $domains])
        ;
    }
    public function user(Request $request){
        DB::table('users')->insert(['name'=> 'Hiếu','email' =>'hieu@domain.com','password' =>  Hash::make('Tovi1234@')]);
    }
    public function domCop(Request $request){

    }
    public function domain(Request $request){
        $domain = $request->domain;  
        $url =  'https://archive.org/wayback/available?url='.$domain;
        //print_r($url);
        $res = httprequest($url, 'GET', 30, array(), array(), array('statuscode' => false));
        //print_r($res);
        $status = $res['status'];
        if($status == true){
            $info = json_decode($res['msg'],true);
            $urlInfo = $info['archived_snapshots']['closest']['url'];
            //$html = httprequest($urlInfo, 'GET', 30, array(), array(), array('statuscode' => false));
            //print_r($html);
            $content = $urlInfo;
            $content = str_replace('http://', 'https://', $content);
            // if($html['status'] == true){
            //     $content = $html['msg'];
            // }
            echo json_encode(array('status' => 1,'content' => $content));
        } else {
            echo json_encode(array('status' => 0));
        }
    }   
    
    public function logout(Request $request) {
      Auth::logout();
      return redirect('/login');
    }

    /**
     * @param Request $request
     * @return string
     */
    public function selected(Request $request)
    {
        $selected = explode('-', $request->selected);

        Domain::updateOrCreate(
            ['domain_out_id' => $selected[0]],
            [
                'check_status'=> $selected[1]
            ]
        );
        var_dump($selected);
        return '1';
    }
    public function postUpload(Request $request){

        if($request->hasFile('domain_csv')){
            try {
                $type = $request->file('domain_csv')->extension();
                $image = $request->file('domain_csv')->storeAs('public/csv', time().'.csv');
                $image = str_replace('public/csv', 'csv', $image);
                //echo "cd /var/www/html/testapktot/keywords; nohup php artisan domain import --min=".$request->domain." --max=".$image." >/dev/null 2>&1&";die;
                exec("cd /var/www/html/testapktot/keywords; nohup php artisan domain import --min=".$request->domain." --max=".$image." >/dev/null 2>&1&");
                return back();
            } catch (Exception $e) {
                print_r($e);
            }
           
        } else {
            echo 'nofile';
        }
    }
    /**
     * @return true
     */
    public function joinDomainRequestToDomainTemp()
    {
        $domains = DB::table('domain_requests')
            ->select(['domain_requests.domain','domain_out.domain_out_id', 'domain_out.link_out'])
            ->where('domain_requests.status', '=', 1)
            ->join('domain_link', 'domain_requests.domain_request_id', '=', 'domain_link.domain_request_id')
            ->join('domain_out', 'domain_out.domain_out_id', '=', 'domain_link.domain_out_id')
            ->get();

        foreach ($domains as $item) {
            $domain = $item->domain;
            $domain_out_id = $item->domain_out_id;
            $link_out = $item->link_out;
            DB::table('domain_temps')->updateOrInsert(
                [
                    'domain' => $domain,
                    'domain_out_id' => $domain_out_id,
                    'link_out' => $link_out
                ],
                [
                    'domain' => $domain,
                    'domain_out_id' => $domain_out_id,
                    'link_out' => $link_out
                ]
            );
            print_r('-');
        }
        return true;
    }
    public function upload(){
        return view('upload');
    }
    function csv_to_array($filename='', $delimiter='    ')
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $header = NULL;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
            {
                if(!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }
    /**
     * @param Request $request
     * @return false|string
     */
    public function filter(Request $request)
    {
        $domain = $request->domainLink;
        if ($domain == '') {
            return '';
        }
        $domain_out_id = DB::table('domain_temps')->where('domain', '=', 'developer.android.com')->pluck('domain_out_id')->toArray();

        $data = DB::table('domain_out')->whereIn('domain_out_id', $domain_out_id)->orderBy('domain_out_id', 'ASC')->get();

        return json_encode($data);

    }
}
