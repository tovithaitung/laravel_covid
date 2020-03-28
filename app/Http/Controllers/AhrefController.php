<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ListCheckImport;
use App\Keyword;
use Illuminate\Support\Facades\DB;
use App\Domain;
use App\DomainRequest;
use App\Setting;
use App\Exports\Keywords;
require_once '/var/www/html/keywords/app/helpers.php';
//require_once 'simple_html_dom.php';
ini_set('memory_limit','-1');


class AhrefController extends Controller
{
    //
    private $api;
    private $allintitle;

    function __construct(AnswerPublicController $api, AllInTitleController $allintitle)
    {
        $this->api = $api;
        $this->allintitle = $allintitle;
    }

    public function checkRun()
    {
        //while(1){
        for ($i = 0; $i < 5; $i++) {
            $cmd = "nohup php artisan titles " . $i;
            shell_exec($cmd . " >/dev/null 2>&1&");
        }
        // $cmd = "nohup php artisan ahref";
        // $process=shell_exec("ps aux | grep ahref");
        // if(strpos($process, $cmd)){

        // 	echo "\nDang chay\n";
        // } else {
        // 	shell_exec($cmd." >/dev/null 2>&1&");
        // }
        // $cmd = "nohup php artisan planners tovicorp.com@gmail.com MKwb0MN0@";
        // $process=shell_exec("ps aux | grep planners");
        // if(strpos($process, $cmd)){

        // 	echo "\nDang chay\n";
        // } else {
        // 	shell_exec($cmd." >/dev/null 2>&1&");
        // }
        //}
    }

    public function runCheck($bot)
    {

        //$list = Excel::toArray(new ListCheckImport, public_path('ngoc.xlsx'));
        //print_r($list);die;
        $res = array();
        //$list = DB::select(DB::raw("SELECT DISTINCT(keyword) FROM `keywords` WHERE `kwplaner` >= 1000 and difficult <= 1 and domain is null and keyword='racing limits apk' ORDER BY `keywords`.`difficult` ASC,search_volumn DESC"));
        //$list = DB::table('keywords')->where('keyword','LIKE','%hungry shark%')->where('domain','antovi')->where('statusKw',1)->where('kwplaner','>=',100)->orderBy('keyword_id','DESC')->get();
        //print_r($list);die;
        //$list = DB::table('keywords')->where('domain','keywd_10_9')->where('allintitle','>',0)->where('keyword_id','>',149831)->get();
        $list = DB::table('key_app')->where('status',0)->select('keyword')->distinct()->get();
        foreach ($list as $key => $p) {
            $keyword = $p->keyword;
            print_r($keyword . PHP_EOL);
            $res = $this->checkKeyword($keyword);
            sleep(3);
            print_r($res);
            $difficulty = $res['difficulty'];
            $totalVolume = $res['totalVolume'];
            if (empty($difficulty)) {
                $difficulty = 0;
            }
            //if($difficulty >= 0 && $difficulty <= 1){
            //$search = $this->allintitle->resultSearch($keyword);
            $search = 0;
            DB::table('key_app')->where('keyword',$keyword)->update(['search_volumn' => $totalVolume, 'status' => 1]);
            //Keyword::where('keyword_id', $p->keyword_id)->update(['difficult' => $difficulty, 'search_volumn' => $totalVolume, 'status' => 1, 'statusAhref' => 1]);
            //}
        }
    }

    public function keyWordFromExcel()
    {
        $domain = 'ngoctovi';
        $time = date('Y-m-d H:i:s', time());
        $list = Excel::toArray(new ListCheckImport, public_path('572019.xlsx'));
        foreach ($list[0] as $key => $item) {
            if ($key > 0) {
                DB::table('keywords')->insert(['keyword' => $item[0], 'difficult' => $item[2], 'search_volumn' => (int)$item[1], 'statusAhref' => 1, 'status' => 1, 'statusKw' => 1, 'domain' => $domain, 'created_at' => $time, 'country' => 'us']);
            }
        }
    }

    //
    public function checkBackLink()
    {
        $setting = Setting::where('name', 'minDR')->first();
        $checkPrice = Setting::where('name', 'price')->first();
        //$domains = Domain::where('isBuy',1)->where('price','<=',$checkPrice->content)->where('dr','>=',$setting->content)->where('statusFinal',0)->where('price','>',0)->orderBy('dr','desc')->get();
        //$domains = Domain::where('check_status',7)->get();
        $domains = Domain::where('statusWayback', 1)->whereNotNull('total_index')->orderBy('total_index', 'DESC')->get();
        foreach ($domains as $domain) {
            // $price = str_replace('€', '', $domain->price);
            // $price = str_replace(',', '', $price);
            //$price = (float)$price;
            //print_r($domain->domain_name.PHP_EOL);
            //print_r($price);die;
            //if($price <= (float)$checkPrice->content){
            //DB::table('domain_final')->updateOrInsert(['domain' => $domain->domain_name],['price' => $price]);

            //} else {
            //echo $domain->domain_name.PHP_EOL;die;
            $res = $this->ahrefInfoBackLink($domain->domain_name, 1, true);
            sleep(5);
            echo $domain->domain_name;
            print_r($res['totalRows']);
            Domain::where('domain_out_id', $domain->domain_out_id)->update(['RDomain' => $res['totalBackLinkDomain'], 'TotalAnchor' => $res['totalRows'], 'anchor' => json_encode($res['anchors']), 'statusFinal' => 1]);
            //}
            //Domain::where('domain_out_id',$domain->domain_out_id)->update(['statusFinal' => 1]);
        }

        //https://ahrefs.com/site-explorer/backlinks/v2/anchors/subdomains/live/phrases/all/1/refdomains_dofollow_desc?target=bybeapp.io
        //https://ahrefs.com/site-explorer/overview/v2/subdomains/live?target=bybeapp.io
    }

    public function checkBackLinkDomCop()
    {

        //$domains = Domain::where('statusWayback',1)->whereNotNull('total_index')->orderBy('total_index','DESC')->get();
        //$domains = DB::table('domain_domcop_checked')->where('statusFinal', 0)->where('statusRdomain', 0)->where('is_del', 0)->where('maj_tf', '>=', 10)->where('index', '>', 0)->orderBy('maj_tf', 'DESC')->limit(20000)->get()
        $domains = DB::table('domain_domcop_select')->where('statusSE',2)->where('expiring_in','>=', date('Y-m-d',time()).' 00:00:00')->where('statusFinal', 0)->orderBy('maj_tf', 'DESC')->get();
        $check = 0;
        echo count($domains);
        foreach ($domains as $domain) {
            echo $domain->domain . PHP_EOL;
            $res = $this->ahrefInfoBackLink($domain->domain, 1, true);
            sleep(3);

            print_r($res['totalRows']).PHP_EOL;
            if($res['totalRows'] == 0){
                $check++;
                $this->loginAhref();
            }
            //DB::table('domain_domcop_checked')->where('id', $domain->id)->update(['RDomain' => $res['totalBackLinkDomain'], 'TotalAnchor' => $res['totalRows'], 'anchor' => json_encode($res['anchors']), 'statusFinal' => 1]);
            if($res['totalRows'] > 0){
                DB::table('domain_domcop_checked')->where('domain', $domain->domain)->update(['TotalAnchor' => json_encode($res['totalRows']), 'anchor' => json_encode($res['anchors']), 'statusFinal' => 1]);
                DB::table('domain_domcop_select')->where('domain',$domain->domain)->update(['TotalAnchor' => json_encode($res['totalRows']), 'statusFinal' => 1]);
            } else{
                DB::table('domain_domcop_checked')->where('domain', $domain->domain)->update(['statusFinal' => 2]);
                DB::table('domain_domcop_select')->where('domain', $domain->domain)->update(['statusFinal' => 2]);

            }
            //}
        }
    }

    public function checkRefferDomain()
    {
        //$domains = Domain::where('statusFinal',1)->where('statusRdomain',0)->get();
        //$domains = Domain::where('check_status',7)->where('statusRdomain',0)->get();
        $domains = Domain::where('statusWayback', 1)->whereNotNull('total_index')->orderBy('total_index', 'DESC')->get();
        foreach ($domains as $domain) {
            $res = $this->ahrefInfoRefferDomain($domain->domain_name, 1, true);
            sleep(5);
            Domain::where('domain_out_id', $domain->domain_out_id)->update(['RDomain_detail' => json_encode($res['detail']), 'statusRdomain' => 1]);

            //print_r($res);die;
        }
        //Domain::where('domain_out_id',$domain->domain_out_id)->update(['RDomain' => $res['totalBackLinkDomain'], 'TotalAnchor' => $res['totalRows'],'anchor' => json_encode($res['anchors']),'statusFinal' => 1]);

    }


    // check r Rdomail
    public function checkRefferDomainDomCop()
    {
        //$domains = DB::table('domain_domcop_checked')->where('statusRdomain', 0)->where('statusFinal', 1)->where('is_del', 0)->where('moz_da', '>=', 10)->where('index', '>', 0)->orderBy('statusSE', 'ASC')->limit(20000)->get();
        //$domains = DB::table('domain_domcop_checked')->where('statusRdomain', 0)->where('is_del', 0)->where('maj_tf', '>=', 10)->get();
        $domains = DB::table('domain_domcop_select')->where('statusRdomain', 0)->where('expiring_in','>=',date('Y-m-d H:i:s',time()))->orderBy('moz_da', 'DESC')->get();
        echo count($domains);
        $check = 0;
        $list_new = DB::table('domain_requests')->where('code',1)->orWhere('code',2)->get();
        foreach ($domains as $domain) {
            if($this->checkTLD($domain->domain) == true){
                $res = $this->ahrefInfoRefferDomain($domain->domain, 1, true);
                sleep(4);
                if(isset($res['detail'])){
	                if(count($res['detail']) == 0){
	                    $check++;
	                    $this->loginAhref();
	                }
	                // if($check == 5){
	                //     $this->loginAhref();
	                //     $check = 0;
	                // }
	                
	                
	                if(count($res['detail']) > 0){
	                    $code = 0;
	                    foreach ($res['detail'] as $detail) {
	                        foreach ($list_new as $new) {
	                            if($new->domain == $detail['refdomain']){
	                                $code = $new->code;
	                            }
	                        }
	                    }
	                    echo $domain->domain.PHP_EOL;
	                    DB::table('domain_domcop_checked')->where('domain', $domain->domain)->update(['RDomain_detail' => json_encode($res['detail']), 'statusRdomain' => 1]);
	                    DB::table('domain_domcop_select')->where('domain',$domain->domain)->update(['Rdomain' => count($res['detail']), 'statusRdomain' => 1,'type' => $code]);
	                } else{
	                    DB::table('domain_domcop_checked')->where('domain', $domain->domain)->update(['statusRdomain' => 2]);
	                    DB::table('domain_domcop_select')->where('domain', $domain->domain)->update(['statusRdomain' => 2]);

	                }
            	}
            }

        }
        /*$check = 0;
        while (true) {
            $domain = DB::table('domain_domcop_select')->where('statusRdomain', 0)->where('is_del', 0)->orderBy('id','DESC')->first();
            if($domain){
                $res = $this->ahrefInfoRefferDomain($domain->domain, 1, true);
                sleep(3);
                echo count($res['detail']) . PHP_EOL;
                if(count($res['detail']) == 0){
                    $check++;
                    $this->loginAhref();
                }
                // if($check == 5){
                //     $this->loginAhref();
                //     $check = 0;
                // }
                if(count($res['detail']) > 0){
                    echo $domain->domain.PHP_EOL;
                    DB::table('domain_domcop_checked')->where('domain', $domain->domain)->update(['RDomain_detail' => json_encode($res['detail']), 'statusRdomain' => 1]);
                    DB::table('domain_domcop_select')->where('domain',$domain->domain)->update(['Rdomain' => count($res['detail']), 'statusRdomain' => 1]);
                } else{
                    DB::table('domain_domcop_checked')->where('domain', $domain->domain)->update(['statusRdomain' => 2]);
                    DB::table('domain_domcop_select')->where('domain', $domain->domain)->update(['statusRdomain' => 2]);

                }
            } else {
                break 1;
            }
        }*/

    }

    public function checkSEDomain()
    {
        $domains = Domain::where('check_status', 7)->where('statusRdomain', 0)->where('statusSE', 0)->get();
        //$domains = Domain::where('statusFinal',1)->where('statusSE',0)->get();
        foreach ($domains as $domain) {

            $res = $this->ahrefSEInfo($domain->domain_name);
            //print_r($res);die;
            Domain::where('domain_out_id', $domain->domain_out_id)->update(['backlinks' => $res['backlinks']['raw'], 'url_rating' => $res['url_rating']['raw'], 'ahrefs_rank' => $res['ahrefs_rank']['raw'], 'statusSE' => 1, 'dr' => $res['domain_rating']['raw']]);

            //print_r($res);die;
        }
    }
    public function checkAll(){
        $this->loginAhref();
        $domains = DB::table('domain_domcop_select')->where('statusDR', 0)->where('expiring_in','>=', date('Y-m-d',time()).' 00:00:00')->orderBy('maj_tf', 'DESC')->get();
        foreach ($domains as $domain) {
             $res = $this->ahrefSEInfo($domain->domain);
             if (isset($res['need_redirect'])) {
                $this->loginAhref();
                continue;
            }
            if($res['domain_rating']['raw'] >= 0){
                DB::table('domain_domcop_select')->where('id', $domain->id)->update(['statusDR' => 1, 'dr' => $res['domain_rating']['raw']]);
            }
            $res = $this->ahrefInfoRefferDomain($domain->domain, 1, true);
            sleep(4);
            if(isset($res['detail'])){
                if(count($res['detail']) == 0){
                    //$check++;
                    $this->loginAhref();
                }
                // if($check == 5){
                //     $this->loginAhref();
                //     $check = 0;
                // }
                
                
                if(count($res['detail']) > 0){
                    $code = 0;
                   
                    echo $domain->domain.PHP_EOL;
                    DB::table('domain_domcop_checked')->where('domain', $domain->domain)->update(['RDomain_detail' => json_encode($res['detail']), 'statusRdomain' => 1]);
                    DB::table('domain_domcop_select')->where('domain',$domain->domain)->update(['Rdomain' => count($res['detail']), 'statusRdomain' => 1,'type' => $code]);
                } else{
                    DB::table('domain_domcop_checked')->where('domain', $domain->domain)->update(['statusRdomain' => 2]);
                    DB::table('domain_domcop_select')->where('domain', $domain->domain)->update(['statusRdomain' => 2]);

                }
            }
            $res = $this->ahrefInfoBackLink($domain->domain, 1, true);
            sleep(3);

            print_r($res['totalRows']).PHP_EOL;
            if($res['totalRows'] == 0){
                //$check++;
                $this->loginAhref();
            }
            //DB::table('domain_domcop_checked')->where('id', $domain->id)->update(['RDomain' => $res['totalBackLinkDomain'], 'TotalAnchor' => $res['totalRows'], 'anchor' => json_encode($res['anchors']), 'statusFinal' => 1]);
            if($res['totalRows'] > 0){
                DB::table('domain_domcop_checked')->where('domain', $domain->domain)->update(['TotalAnchor' => json_encode($res['totalRows']), 'anchor' => json_encode($res['anchors']), 'statusFinal' => 1]);
                DB::table('domain_domcop_select')->where('domain',$domain->domain)->update(['TotalAnchor' => json_encode($res['totalRows']), 'statusFinal' => 1]);
            } else{
                DB::table('domain_domcop_checked')->where('domain', $domain->domain)->update(['statusFinal' => 2]);
                DB::table('domain_domcop_select')->where('domain', $domain->domain)->update(['statusFinal' => 2]);

            }
        }
    }
    // check dr
    public function checkSEDomainDomCop()
    {
        //domains = DB::table('domain_domcop_checked')->where('statusRdomain', 0)->where('statusSE', 0)->where('is_del', 0)->where('moz_da', '>=', 10)->where('index', '>', 0)->orderBy('maj_tf', 'DESC')->limit(20000)->get();
        $domains = DB::table('domain_domcop_select')->where('statusDR', 0)->where('expiring_in','>=', date('Y-m-d',time()).' 00:00:00')->orderBy('maj_tf', 'DESC')->get();
        //$domains = Domain::where('statusFinal',1)->where('statusSE',0)->get();
        echo count($domains);
        $check = 0;
        foreach ($domains as $domain) {

            $res = $this->ahrefSEInfo($domain->domain);
            sleep(10);

            
            //print_r($res);die;
            if (isset($res['need_redirect'])) {
                $this->loginAhref();
                continue;
            }
            if($res['domain_rating']['raw'] == 0){
                $check++;
                $this->loginAhref();
            }
            if($res['domain_rating']['raw'] > 0){
                DB::table('domain_domcop_select')->where('id', $domain->id)->update(['statusDR' => 1, 'dr' => $res['domain_rating']['raw']]);
            }
            //print_r($res);die;
        }
    }
    public function checkPEDomain(){
        
        foreach ($domains as $domain) {
            $domain = str_replace('http://', '', $domain);
            $domain = str_replace('https://', '', $domain);
            $domain = str_replace('www.', '', $domain);
            $domain = str_replace('/', '', $domain);
            $res = $this->ahrefPEInfo($domain);
            sleep(3);
            //print_r($res);die;
            if (isset($res['need_redirect'])) {
                $this->loginAhref();
                $res = $this->ahrefPEInfo($domain);
            }
            $response[] = array('domain' => $domain, 'ahref' => $res['monthly']['all']);
            
        }
        $export = new Keywords($response);
        return Excel::store($export,'public/antovi.xlsx',);

    }
    public function ahrefInfoBackLink($target, $page, $login = true, $res = array(), $totalPages = 0, $totalRows = 0, $totalBackLinkDomain = 0)
    {
        $url = 'https://ahrefs.com/site-explorer/backlinks/v2/anchors/subdomains/live/phrases/all/' . $page . '/refdomains_dofollow_desc?target=' . $target;
        $file = public_path('ahref.txt');
        $setting = Setting::where('name', 'href')->first();
        if ($setting->content == 0 || $setting->content == -1) {
            if (file_exists($file)) {
                unlink($file);
            }
            $authenticity_token = $this->loginAhref();
            Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
            //DB::table('setting')->insert(['name' => 'ahref','content' => $authenticity_token]);
            if ($authenticity_token == false) {
                return false;
            }
        } else {
            $setting = Setting::where('name', 'href')->first();
            $authenticity_token = $setting->content;
        }
        $response = $this->api->requestUser($url, $file, true, $authenticity_token);
        sleep(5);
        $html = $response['html'];
        //print_r($url);
        $dom = str_get_html($html);
        if ($dom || isset($html['need_redirect'])) {
            $login_form = $dom->find('a.btn-ghost-secondary', 0);
            if ($login_form) {
                $text = $login_form->href;
                if ($text == '/user/login') {
                    $authenticity_token = $this->loginAhref();
                    Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
                    //$response = $this->api->requestUser($url, $file);
                    //$html = $response['html'];
                }
            }
        }
        //$res = array();
        $html = json_decode($html, true);
        //print_r($html);
        //$totalPage = 0;
        if ($dom || isset($html['need_redirect'])) {
            $login_form = $dom->find('a.btn-ghost-secondary', 0);
            if ($login_form) {
                $text = $login_form->href;
                if ($text == '/user/login') {
                    $authenticity_token = $this->loginAhref();
                    Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
                    //$response = $this->api->requestUser($url, $file);
                    //$html = $response['html'];
                }
            }
        }
        if ($page == 1) {
            if (isset($html['pager'])) {
                $totalPages = $html['pager']['totalPages'];
            }
            $res = array();
        }
        //$totalRows = 0;
        if ($page == 1 && isset($html['totalRows'])) {
            $totalRows = $html['totalRows'];
        }
        if (isset($html['result'])) {
            //$res = array();
            foreach ($html['result'] as $anchor) {
                $res[] = array('anchor' => $anchor['anchor'], 'PercentageRefDomains' => $anchor['PercentageRefDomains'], 'refdomains' => $anchor['refdomains']);
            }
        }

        // ovverview
        //$totalBackLinkDomain = 0;
        if ($page == 1) {
            $url = 'https://ahrefs.com/site-explorer/referring/v2/domains/subdomains/live/all/all/1/domain_rating_desc?target=' . $target;
            $response = $this->api->requestUser($url, $file, true, $authenticity_token);
            $html = $response['html'];
            $html = json_decode($html, true);
            //print_r($html);die;
            if (isset($html['totalRows'])) {
                $totalBackLinkDomain = $html['totalRows'];
            }
        }
        //print_r('total Page:'.$totalPages.PHP_EOL);
        //print_r('page:'.$page.PHP_EOL);
        if ($totalPages > 1 && $page < $totalPages && $page <= 2) {
            return $this->ahrefInfoBackLink($target, $page + 1, false, $res, $totalPages, $totalRows, $totalBackLinkDomain);
        }
        $result = array('totalPage' => $totalPages, 'totalRows' => $totalRows, 'totalBackLinkDomain' => $totalBackLinkDomain, 'anchors' => $res);
        return $result;
    }

    public function ahrefInfoRefferDomain($target, $page, $login = true, $res = array(), $totalPages = 0, $totalRows = 0, $totalBackLinkDomain = 0)
    {
        $url = 'https://ahrefs.com/site-explorer/referring/v2/domains/subdomains/live/all/all/' . $page . '/domain_rating_desc?target=' . $target;
        $file = public_path('ahref.txt');
        $setting = Setting::where('name', 'href')->first();
        if ($setting->content == 0 || $setting->content == -1) {
            if (file_exists($file)) {
                unlink($file);
            }
            $authenticity_token = $this->loginAhref();
            Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
            //DB::table('setting')->insert(['name' => 'ahref','content' => $authenticity_token]);
            if ($authenticity_token == false) {
                return false;
            }
        } else {
            $setting = Setting::where('name', 'href')->first();
            $authenticity_token = $setting->content;
        }
        $response = $this->api->requestUser($url, $file, true, $authenticity_token);
        sleep(5);
        $html = $response['html'];
        //print_r($target);
        $dom = str_get_html($html);
        if ($dom || isset($html['need_redirect'])) {
            $login_form = $dom->find('a.btn-ghost-secondary', 0);
            if ($login_form) {
                $text = $login_form->href;
                if ($text == '/user/login') {
                    $authenticity_token = $this->loginAhref();
                    Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
                    //$response = $this->api->requestUser($url, $file);
                    //$html = $response['html'];
                }
            }
        }
        //$res = array();
        $html = json_decode($html, true);
        //print_r($html);die;
        //$totalPage = 0;

        if ($page == 1) {
            if (isset($html['pager'])) {
                $totalPages = $html['pager']['totalPages'];
            }
            $res = array();
        }
        if (isset($html['result'])) {
            //$res = array();
            $hash_code = $html['CachedSecurityHash'];
            foreach ($html['result'] as $anchor){
                
                // if($anchor['number'] <= 10){
                //     $res_detail = $this->detailBacklink($hash_code, $anchor['refdomain'], $anchor['number'], $anchor['backlinks']);
                //     sleep(3);
                //     //print_r($res_detail);die;
                //     if(isset($res_detail['examples_data']['result'])){
                //         $res_detail = $res_detail['examples_data']['result'];
                //     } else {
                //         $authenticity_token = $this->loginAhref();
                //     }
                // } else {
                //     $res_detail = array();
                // }
                $res_detail = array();
                $res[] = array('refdomain' => $anchor['refdomain'], 'domain_rating' => $anchor['domain_rating'], 'do_follow' => $anchor['Backlinks_dofollow'], 'organic_traffic' => $anchor['traffic'],'detail' => $res_detail);
            }
        }
        if ($totalPages > 1 && $page < $totalPages) {
            return $this->ahrefInfoRefferDomain($target, $page + 1, false, $res, $totalPages, $totalRows, $totalBackLinkDomain);
        }
        $result = array('detail' => $res);
        return $result;
    }
    public function detailBacklink($hash_code, $domain, $number, $total){
        $url = 'https://ahrefs.com/site-explorer/ajax/examples/backlinks-for-referring-domain/'.$hash_code;
        $body = 'domain_index='.$number.'&anchor_index=0&snippet_index=0&url_index=0&offset=0&count=0&total_count='.$total.'&int_ext_type=&for_domain='.$domain;
        $file = public_path('ahref.txt');
        $setting = Setting::where('name', 'href')->first();
        if ($setting->content == 0) {
            if (file_exists($file)) {
                unlink($file);
            }
            $authenticity_token = $this->loginAhref();
            Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
            //DB::table('setting')->insert(['name' => 'ahref','content' => $authenticity_token]);
            if ($authenticity_token == false) {
                return false;
            }
        } else {
            $setting = Setting::where('name', 'href')->first();
            $authenticity_token = $setting->content;
        }
        //echo $authenticity_token;
        //$response = $this->api->requestUser($url, $file, true, $authenticity_token);
        $response = $this->api->postRequest($url, $body, $file, true, 'https://ahrefs.com', $authenticity_token);
        //print_r($url);die;
        $html = $response;
        //print_r($html);
/*        $dom = str_get_html($html);
        if ($dom || isset($html['need_redirect'])) {
            $login_form = $dom->find('a.btn-ghost-secondary', 0);
            if ($login_form) {
                $text = $login_form->href;
                if ($text == '/user/login') {
                    $authenticity_token = $this->loginAhref();
                    Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
                    //$response = $this->api->requestUser($url, $file);
                    //$html = $response['html'];
                }
            }
        }*/
        //$res = array();
        $html = json_decode($html, true);
        return $html;
    }
    public function ahrefSEInfo($target)
    {
        $url = 'https://ahrefs.com/site-explorer/ajax/get/SE-stats/subdomains/live?target=' . $target;
        $file = public_path('ahref.txt');
        $setting = Setting::where('name', 'href')->first();
        if ($setting->content == 0) {
            if (file_exists($file)) {
                unlink($file);
            }
            $authenticity_token = $this->loginAhref();
            Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
            //DB::table('setting')->insert(['name' => 'ahref','content' => $authenticity_token]);
            if ($authenticity_token == false) {
                return false;
            }
        } else {
            $setting = Setting::where('name', 'href')->first();
            $authenticity_token = $setting->content;
        }
        $response = $this->api->requestUser($url, $file, true, $authenticity_token);
        $html = $response['html'];
        print_r($html);
        $dom = str_get_html($html);
        if ($dom || isset($html['need_redirect'])) {
            $login_form = $dom->find('a.btn-ghost-secondary', 0);
            if ($login_form) {
                $text = $login_form->href;
                if ($text == '/user/login') {
                    $authenticity_token = $this->loginAhref();
                    Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
                    //$response = $this->api->requestUser($url, $file);
                    //$html = $response['html'];
                }
            }
        }
        //$res = array();
        $html = json_decode($html, true);
        return $html;
    }
    public function ahrefPEInfo($target)
    {
        $url = 'https://ahrefs.com/site-explorer/ajax/get/PE-stats/subdomains?target=' . $target;
        $file = public_path('ahref.txt');
        $setting = Setting::where('name', 'href')->first();
        if ($setting->content == 0) {
            if (file_exists($file)) {
                unlink($file);
            }
            $authenticity_token = $this->loginAhref();
            Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
            //DB::table('setting')->insert(['name' => 'ahref','content' => $authenticity_token]);
            if ($authenticity_token == false) {
                return false;
            }
        } else {
            $setting = Setting::where('name', 'href')->first();
            $authenticity_token = $setting->content;
        }
        $response = $this->api->requestUser($url, $file, true, $authenticity_token);
        $html = $response['html'];
        print_r($html);
        $dom = str_get_html($html);
        if ($dom || isset($html['need_redirect'])) {
            $login_form = $dom->find('a.btn-ghost-secondary', 0);
            if ($login_form) {
                $text = $login_form->href;
                if ($text == '/user/login') {
                    $authenticity_token = $this->loginAhref();
                    Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
                    //$response = $this->api->requestUser($url, $file);
                    //$html = $response['html'];
                }
            }
        }
        //$res = array();
        $html = json_decode($html, true);
        return $html;
    }
    public function insertDemo()
    {

    }

    /* get linkout domain*/
    public function runDomain()
    {
        $domains = DomainRequest::where('status', 0)->orderBy('domain_request_id', 'asc')->get();
        $filter = Setting::where('name', 'type_domain')->first();
        $list = array();
        if ($filter) {
            $tmp = $filter->content;
            $list = explode(',', $tmp);
        }
        $firstPage = 1;

        foreach ($domains as $domain) {
            $countErr = 0;
            foreach ($list as $item) {
                $info = DB::table('process_request')->where('domain', $domain->domain)->where('type', $item)->first();
                if ($info) {
                    if ($info->status == 0) {
                        $firstPage = $info->page;
                    } else {
                        continue;
                    }
                }
                //$setting = Setting::where('name','href')->first();

                $totalPage = $this->linkedDomain($domain->domain_request_id, $domain->domain, $firstPage, true, $item, 'domain_to_rating_asc', true);
                if ($totalPage == -1) {
                    $countErr = $countErr + 1;
                }
                DB::table('process_request')->updateOrInsert(['domain' => $domain->domain, 'type' => $item], ['page' => $firstPage]);
                //print_r($totalPage.PHP_EOL);
                if ($totalPage > 200) {
                    $totalPage = 200;
                }

                for ($i = $firstPage + 1; $i <= $totalPage; $i++) {
                    $check = $this->linkedDomain($domain->domain_request_id, $domain->domain, $i, false, $item, 'domain_to_rating_asc');
                    sleep(1);
                    //var_dump($check);
                    //var_dump(count($countErr);
                    DB::table('process_request')->updateOrInsert(['domain' => $domain->domain, 'type' => $item], ['page' => $i]);
                    if ($check == -1) {
                        $countErr = $countErr + 1;
                    } else {
                        if ($i == $totalPage) {
                            DB::table('process_request')->updateOrInsert(['domain' => $domain->domain, 'type' => $item], ['page' => $i, 'status' => 1]);
                        } else {
                            DB::table('process_request')->updateOrInsert(['domain' => $domain->domain, 'type' => $item], ['page' => $i]);
                        }
                    }

                }

                if ($countErr == 5) {
                    echo 'loi script';
                    die;
                }
                //DB::table('process_request')->updateOrInsert(['domain' => $domain->domain,'type' => $value],['page' => 0]);
            }
            DomainRequest::where('domain_request_id', $domain->domain_request_id)->update(['status' => 1]);
        }
        /*foreach ($domains as $domain) {
            foreach ($list as $item) {
                //DB::table()
                $totalPage = $this->linkedDomain($domain->domain, 1 , true, $item, 'domain_to_rating_asc');
                print_r($totalPage.PHP_EOL);
                if($totalPage > 200){
                    $totalPage = 200;
                }
                for ($i = $firstPage + 1; $i <= $totalPage; $i++) {
                    $this->linkedDomain($domain->domain, $i, false, $item, 'domain_to_rating_asc');
                    sleep(1);
                }
            }
            DomainRequest::where('domain_request_id',$domain->domain_request_id)->update(['status' => 1]);
        }*/
    }

    public function checkDemo()
    {
        $list = DB::table('keywords')->where('status', '!=', 1)->get();
        foreach ($list as $key => $p) {
            $keyword = $p->keyword;
            $keyword = str_replace(',', '', $keyword);
            $res = $this->checkKeyword($keyword);
            $difficulty = $res['difficulty'];
            $totalVolume = $res['totalVolume'];
            if (empty($difficulty)) {
                $difficulty = 0;
            }
            print_r($difficulty);
            //if($difficulty >= 0 && $difficulty <= 1){
            //$search = $this->allintitle->resultSearch($keyword);
            $search = 0;
            Keyword::where('keyword_id', $p->keyword_id)->update(['difficult' => $difficulty, 'search_volumn' => $totalVolume, 'status' => 1]);
            //}
        }
    }

    public function keywordDomain($domain, $filter)
    {
        try {
            $url = 'https://ahrefs.com/keywords-explorer';
            $file = public_path('ahref.txt');
            $response = $this->api->requestUser($url, $file, false, 0);
            $html = $response['html'];
            //print_r($html);
            $dom = str_get_html($html);
            if ($dom) {
                $login_form = $dom->find('a.btn-ghost-secondary', 0);
                if ($login_form) {
                    $text = $login_form->href;
                    if ($text == '/user/login') {
                        $this->loginAhref();
                        $response = $this->api->requestUser($url, $file);
                        $html = $response['html'];
                    }
                }
            }
            $tmp_token = explode('<meta name="_token" content="', $html);
            $tmp_token = explode('">', $tmp_token[1]);
            //print_r($tmp_token[0]);die;
            $authenticity_token = $tmp_token[0];

            $url = 'https://ahrefs.com/positions-explorer/organic-keywords/v5/subdomains';
            $url = $url . '/' . $filter['country'];
            if (isset($filter['position'])) {
                $url = $url . '/' . $filter['position'];
            } else {
                $url = $url . '/all';
            }
            if (isset($filter['volume'])) {
                $url = $url . '/' . $filter['volume'];
            } else {
                $url = $url . '/all';
            }
            if (isset($filter['kd'])) {
                $url = $url . '/' . $filter['kd'];
            } else {
                $url = $url . '/all';
            }
            if (isset($filter['traffic'])) {
                $url = $url . '/' . $filter['traffic'];
            } else {
                $url = $url . '/all';
            }
            $url = $url . '/all';
            if (isset($filter['word_count'])) {
                $url = $url . '/' . $filter['word_count'];
            } else {
                $url = $url . '/all';
            }
            if (isset($filter['cpc'])) {
                $url = $url . '/' . $filter['cpc'];
            } else {
                $url = $url . '/all';
            }
            if (isset($filter['page'])) {
                $url = $url . '/' . $filter['page'];
            } else {
                $url = $url . '/1';
            }
            $url = $url . '/last_update_desc';
            $url = $url . '?target=' . $domain;
            if (isset($filter['keyword'])) {
                $url = $url . '&include=' . $filter['keyword'] . '&inc-criteria=or&search-in=keyword&search-in-criteria=or';
            }
            //print_r($url);
            $data = $this->api->requestUser($url, $file, true, $authenticity_token);
            //print_r($data['html']);
            //$data['html'] = 'fsfsd';
            $response = json_decode($data['html'], true);
            if (is_array($response)) {
                $page = $response['pager']['currentPage'];
                $totalPage = $response['pager']['totalPages'];
                $list = $response['result'];
                $this->saveKeywords($list, 'ngoctovi');
                $filter['page'] = $page + 1;
                if ($page == $totalPage) {
                    return 1;
                }
                return $this->keywordDomain($domain, $filter);

            } else {
                return 2;
            }
        } catch (\Exception $e) {
            $myfile = file_put_contents(public_path('logs.txt'), $e->getMessage() . PHP_EOL, FILE_APPEND | LOCK_EX);
            //$e->getMessage();
        }


        //$url = 'https://ahrefs.com/positions-explorer/organic-keywords/v5/subdomains/us/1-0/100-0/0-1/all/all/4-6/all/1/last_update_desc?target=apkpure.com&include=apk&inc-criteria=or&search-in=keyword&search-in-criteria=or';

    }

    /* list key word from domain*/
    public function runExtractKeyword($target, $filter)
    {
        $res = $this->extractKeywordFromDomain($target, $filter, 1);
        $list = $res;
        print_r(count($list));
        $this->saveKeywords($list, 'ngoctovi');
    }

    public function extractKeywordFromDomain($target, $filter, $page, $totalPages = 1, $data = array())
    {
        //https://ahrefs.com/positions-explorer/organic-keywords/v5/subdomains/us/all/all/all/all/all/all/all/1/traffic_desc?target=apkmirror.com
        $url = 'https://ahrefs.com/positions-explorer/organic-keywords/v5/subdomains';
        $url = $url . '/' . $filter['country'];
        if (isset($filter['position'])) {
            $url = $url . '/' . $filter['position'];
        } else {
            $url = $url . '/all';
        }
        if (isset($filter['volume'])) {
            $url = $url . '/' . $filter['volume'];
        } else {
            $url = $url . '/all';
        }
        if (isset($filter['kd'])) {
            $url = $url . '/' . $filter['kd'];
        } else {
            $url = $url . '/all';
        }
        if (isset($filter['traffic'])) {
            $url = $url . '/' . $filter['traffic'];
        } else {
            $url = $url . '/all';
        }
        $url = $url . '/all';
        if (isset($filter['word_count'])) {
            $url = $url . '/' . $filter['word_count'];
        } else {
            $url = $url . '/all';
        }
        if (isset($filter['cpc'])) {
            $url = $url . '/' . $filter['cpc'];
        } else {
            $url = $url . '/all';
        }
        if (isset($filter['page'])) {
            $url = $url . '/' . $page;
        } else {
            $url = $url . '/1';
        }
        $url = $url . '/last_update_desc';
        $url = $url . '?target=' . $target;
        if (isset($filter['keyword'])) {
            $url = $url . '&include=' . $filter['keyword'] . '&inc-criteria=or&search-in=keyword&search-in-criteria=or';
        }
        $file = public_path('ahref.txt');
        $setting = Setting::where('name', 'href')->first();
        if ($setting->content == 0) {
            if (file_exists($file)) {
                unlink($file);
            }
            $authenticity_token = $this->loginAhref();
            Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
            //DB::table('setting')->insert(['name' => 'ahref','content' => $authenticity_token]);
            if ($authenticity_token == false) {
                return false;
            }
        } else {
            $setting = Setting::where('name', 'href')->first();
            $authenticity_token = $setting->content;
        }
        $response = $this->api->requestUser($url, $file, true, $authenticity_token);
        $html = $response['html'];
        //print_r($target);
        $dom = str_get_html($html);
        if ($dom || isset($html['need_redirect'])) {
            $login_form = $dom->find('a.btn-ghost-secondary', 0);
            if ($login_form) {
                $text = $login_form->href;
                if ($text == '/user/login') {
                    $authenticity_token = $this->loginAhref();
                    Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
                    //$response = $this->api->requestUser($url, $file);
                    //$html = $response['html'];
                }
            }
        }
        //$res = array();
        $html = json_decode($html, true);
        //print_r($url);
        //print_r($html);die;
        //$totalPage = 0;

        if ($page == 1) {
            if (isset($html['pager'])) {
                $totalPages = $html['pager']['totalPages'];
            }
            $data = array();
        }
        if (isset($html['result'])) {
            //$res = array();
            //$data = $html['result'];
            foreach ($html['result'] as $keyword) {
                if ($keyword['keyword'] != '—') {
                    if ($keyword['difficulty'] == '—') {
                        $keyword['difficulty'] = 0;
                    }
                    $data[] = array('keyword' => $keyword['keyword'], 'difficulty' => $keyword['difficulty'], 'volume' => $keyword['volume'], 'url' => $keyword['url'], 'country' => $keyword['country']);
                    //print_r($data);
                }
            }
        }
        if ($totalPages > 1 && $page < $totalPages) {
            return $this->extractKeywordFromDomain($target, $filter, $page + 1, $totalPages, $data);
        }
        //$result = array('detail' => $res);
        return $data;
    }

    public function saveKeywords($data, $domain)
    {
        $time = date('Y-m-d H:i:s', time());
        foreach ($data as $value) {
            $info = DB::table('keywords')->where('keyword', $value['keyword'])->where('domain', $domain)->first();
            if (!$info) {
                DB::table('keywords')->insert(['keyword' => $value['keyword'], 'difficult' => $value['difficulty'], 'search_volumn' => (int)$value['volume'], 'statusAhref' => 1, 'status' => 1, 'statusKw' => 1, 'domain' => $domain, 'url' => $value['url'], 'created_at' => $time, 'country' => $value['country']]);
            }
            //die;
        }
    }

    public function checkKeyword($keyword = "facebook apk")
    {
        $country = 'us';
        //$keyword = $keyword;
        //$keyword="chromecast apk";
        $url = 'https://ahrefs.com/keywords-explorer';
        $file = public_path('ahref.txt');
        $response = $this->api->requestUser($url, $file);
        $html = $response['html'];
        //print_r($html);
        $dom = str_get_html($html);
        if ($dom) {
            $login_form = $dom->find('a.btn-ghost-secondary', 0);
            if ($login_form) {
                $text = $login_form->href;
                if ($text == '/user/login') {
                    $this->loginAhref();
                    $response = $this->api->requestUser($url, $file);
                    $html = $response['html'];
                }
            }
        }
        $tmp_token = explode('<meta name="_token" content="', $html);
        //print_r($tmp_token);
        $tmp_token = explode('">', $tmp_token[1]);
        //print_r($tmp_token[0]);die;
        $authenticity_token = $tmp_token[0];
        $body = '_token=' . $authenticity_token . '&keyword=' . urlencode($keyword);
        $url = 'https://ahrefs.com/keywords-explorer/overview?country=' . $country;
        $refer = "referer: https://ahrefs.com/keywords-explorer";
        $response = $this->api->postRequest($url, $body, $file, true, $refer, $authenticity_token);
        $response = json_decode($response, true);
        //print_r($response);die;
        /*$redirect_dom = str_get_html($response);

        $url_detail = $redirect_dom->find('a',0)->href;*/
        $url_detail = $response['need_redirect'];
        $keywords = str_replace('/keywords-explorer/overview?list=', '', $url_detail);
        $keywords = str_replace('&country=us', '', $keywords);
        //print_r($url_detail);
        $detail = $this->api->requestUser('https://ahrefs.com' . $url_detail, $file, false);
        $html_detail = $detail['html'];
        //print_r($html_detail);
        //$ahref_token = explode("var AhrefsToken = \"", $html_detail);
        //$ahref_token = explode('";', $ahref_token[1]);

        $tmp = explode('var CSHash = "', $html_detail);

        $key = explode('";', $tmp[1]);


        $url_data = 'https://ahrefs.com/keywords-explorer/ajax/get/volume-by-country/' . $key[0];
        $data = $this->api->requestUser($url_data, $file, true);
        print_r($data);
        if ($data != false) {
            $totalVolume = json_decode($data['html'], true)['totalVolume'];
        } else {
            $totalVolume = -1;
        }

        $url_data = 'https://ahrefs.com/keywords-explorer/ajax/get/keywords-data';
        $body_data = 'keywords=' . urlencode($keywords . '-' . $country) . '&positions=1&cshash=' . urlencode(trim($key[0]));

        $refer = "referer: https://ahrefs.com/keywords-explorer/overview?list=" . $keywords . "&country=" . $country;
        $data = $this->api->postRequest($url_data, $body_data, $file, true, $refer, $authenticity_token);
        if ($data != false) {
            $ahref_response = json_decode($data, true);
            //print_r($ahref_response);
            if (!empty($ahref_response) && isset($ahref_response['data'][$keywords . '-' . $country]['difficulty'])) {
                $difficulty = $ahref_response['data'][$keywords . '-' . $country]['difficulty'];
            } else {
                $difficulty = -1;
            }
        } else {
            $difficulty = -1;
        }

        return array('difficulty' => $difficulty, 'totalVolume' => $totalVolume);
    }

    public function linkedDomain($id, $target, $page, $login = true, $type = 'all', $filter = 'domain_to_rating_desc', $checktotal = false)
    {

        //https://archive.org/wayback/available?url=office.com
        $url = 'https://ahrefs.com/site-explorer/others/v2/linked-domains/subdomains/live/all/' . $type . '/' . $page . '/domain_to_rating_desc?target=' . $target;
        print_r($url . PHP_EOL);
        $setting = Setting::where('name', 'href')->first();
        print_r("content:" . $setting->content . PHP_EOL);
        $file = public_path('ahref.txt');
        if (($page == 1 && $login == true) || $setting->content == -1 || $setting->content == 0) {
            if (file_exists($file)) {
                unlink($file);
            }
            $authenticity_token = $this->loginAhref();
            print_r("token:" . $authenticity_token . PHP_EOL);
            if ($authenticity_token == -1) {
                $authenticity_token = $this->loginAhref();
                return -1;
            }
            Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
            //DB::table('setting')->insert(['name' => 'ahref','content' => $authenticity_token]);
        } else {
            $setting = Setting::where('name', 'href')->first();
            $authenticity_token = $setting->content;
        }
        $response = $this->api->requestUser($url, $file, true, $authenticity_token);
        if ($response == -1) {
            return -1;
        }
        $html = $response['html'];
        //print_r($html.PHP_EOL);
        $dom = str_get_html($html);
        if ($dom) {
            $login_form = $dom->find('a.btn-ghost-secondary', 0);
            if ($login_form) {
                $text = $login_form->href;
                if ($text == '/user/login') {
                    $authenticity_token = $this->loginAhref();
                    Setting::updateOrCreate(['name' => 'href'], ['content' => $authenticity_token]);
                    //$response = $this->api->requestUser($url, $file);
                    //$html = $response['html'];
                }
            }
        }
        $res = array();
        $html = json_decode($html, true);
        if ($checktotal == true) {
            if (isset($html['pager'])) {
                $totalPage = $html['pager']['totalPages'];
            } else {
                $totalPage = 0;
            }
        } else {
            $totalPage = 0;
        }
        if (isset($html['result'])) {
            foreach ($html['result'] as $page) {
                if (strpos($page['domain_to'], '.' . $target) !== false) {
                    $res[] = array('name' => $page['domain_to'], 'DR' => $page['domain_rating'], 'href' => $page['domain_to']);
                    $data = array('domain' => $page['domain_to'], 'status' => 0, 'created_at' => date('Y-m-d H:i:s'));
                    $info = DomainRequest::updateOrCreate(['domain' => $page['domain_to']], ['created_at' => date('Y-m-d H:i:s')]);
                    //DB::table('domain_requests')->insert($data);
                } else {
                    //$count = DB::table('domain')
                    //DB::table('domain_out')->insert($data);
                    //print_r($page['domain_to']);
                    $data = array('domain_name' => $page['domain_to'], 'status' => 0, 'code' => 0, 'created_at' => date('Y-m-d H:i:s'));
                    $info = Domain::updateOrCreate(['domain_name' => $page['domain_to']], ['link_out' => $target, 'dr' => $page['domain_rating'], 'created_at' => date('Y-m-d H:i:s')]);
                    $id_out = $info->domain_out_id;
                    DB::table('domain_link')->updateOrInsert(['domain_request_id' => $id, 'domain_out_id' => $id_out]);
                    //die;
                }
            }
        } elseif ($html['need_redirect']) {

            return $totalPage;
        } else {

            return -1;
        }

        return $totalPage;

    }

    public function oneMillonDomain()
    {
        $url = 'http://s3.amazonaws.com/alexa-static/top-1m.csv.zip';
    }
     public function checkTLD($domain){
        $list = array('.co','.com','.net','.org','.info','.me','.website','.io','.us','.xyz');
        $check = false;
        if(substr_count($domain, '.') == 1){
            foreach ($list as $value) { 
                
                    if(strpos($domain, $value) != false){  
                        $check = true;
                        break;
                        
                    }
                
                
            }
        } else {
            return false;
        }
        return $check;
        
    }
    public function loginAhref()
    {
        $url = 'https://ahrefs.com/user/login';
        $file = public_path('ahref.txt');
        if (file_exists($file)) {
            unlink($file);
        }
        $response = $this->api->requestUser($url, $file);
        $html = $response['html'];
        $dom = str_get_html($html);
        if (!$dom) {
            echo 'bi chan' . PHP_EOL;
            return -1;
        }
        $login_form = $dom->find('form#formLogin', 0);
        if ($login_form) {
            $authenticity_token = $login_form->find('input[name=_token]', 0)->value;
            $body = '_token=' . $authenticity_token . '&email=' . urlencode('jenamiller0202@gmail.com') . '&password=toviseo@2020&return_to=https://ahrefs.com/';
            //print_r($body);
            $html = $this->api->postRequest($url, $body, $file);
        } else {
            $authenticity_token = false;
        }
        return $authenticity_token;
    }
}
