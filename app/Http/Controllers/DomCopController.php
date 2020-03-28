<?php

namespace App\Http\Controllers;
include '/var/www/html/testapktot/keywords/app/helpers.php';
//include 'C:/xampp/htdocs/keywords/app/helpers.php';
require_once 'simple_html_dom.php';

use Illuminate\Http\Request;
use DB;
ini_set('memory_limit', -1);
class DomCopController extends Controller
{
    //
    public function index($bot = 1)
    {
        // chay tu 200
        // chay tu 3000
        //for ($i = 240; $i <= 6150; $i++) {
        //$i = 2047;
        // $i = 5000;
        if($bot == 1){
            $type = 'expired';
            $setting = DB::table('setting')->where('name','expired')->first();
        } else {
            $type = 'expiring';
            $setting = DB::table('setting')->where('name','expiring')->first();
        }
        $i = 0;
        // $i = 
        //$i = 1;
        $totalPage = 20;
        while ($i <= $totalPage) {
            try {
            if($bot == 1){
                $res = $this->getDomain(5000 * $i, $i + 1);
            } else {
                $res = $this->getDomain(5000 * $i, $i + 1, 2);
            }
            if($i == $totalPage){
               die;
            }
            //print_r($res);
            $data = $res['msg'];

            $data = json_decode($data, true);
            print_r('total:'.$data['iTotalDisplayRecords'].'-').PHP_EOL;
            //print_r($data);
                $totalPage = ceil($data['iTotalDisplayRecords']/5000);
                if (isset($data['aaData']) && !empty($data['aaData'])) {
                    foreach ($data['aaData'] as $key => $item) {

                       // print_r($item);die;

                        if ($key == 5000) {
                            continue;
                        }
                        
                        $html = str_get_html($item[1]);
                        $name = $html->find('.big-domain a', 0)->plaintext;
                        $buy = $html->find('.big-domain a', 0)->href;
                        if($html->find('.inline', 0)){
                            $index_raw = $html->find('.inline', 0)->attr['title'];
                        } else {
                            $index_raw = -1;
                        }
                        //var_dump($index_raw);
                        $index = -1;
                        if(strpos($index_raw,'No Paged')) {
                            $index = -1;
                        }else {
                            $index_string  = str_replace('Indexed by Google','', $index_raw);
                            $index_string = trim($index_string);
                    
                            $index_string = str_replace("(", "", $index_string);
                            $index = substr($index_string,0,strpos($index_string,"page"));
                            $index = intval($index);
                        }
                        

                        $html = str_get_html($item[2]);
                        if($html->find('a', 0)){
                            $age = $html->find('a', 0)->plaintext;
                            if ($age == '-') {
                                $age = -1;
                            }
                        } else {
                            $age = -1;
                        }
                        $html = str_get_html($item[3]);
                        if($html->find('a', 0)){
                            $moz_da = $html->find('a', 0)->plaintext;
                            if ($moz_da == '-') {
                                $moz_da = -1;
                            }
                        } else {
                            $moz_da = -1;
                        }
                        
                        $html = str_get_html($item[4]);
                        if($html->find('a', 0)){
                            $moz_pa = $html->find('a', 0)->plaintext;
                            if ($moz_pa == '-') {
                                $moz_pa = -1;
                            }
                        } else {
                            $moz_pa = -1;
                        }

                        $html = str_get_html($item[5]);
                        if($html->find('a', 0)){
                            $moz_rank = $html->find('a', 0)->plaintext;
                            if ($moz_rank == '-') {
                                $moz_rank = -1;
                            }
                        } else {
                            $moz_rank = -1;
                        }
                        $html = str_get_html($item[6]);
                        if($html->find('a', 0)){
                            $moz_trust = $html->find('a', 0)->plaintext;
                            if ($moz_trust == '-') {
                                $moz_trust = -1;
                            }
                        } else {
                            $moz_trust = -1;
                        }
                        $html = str_get_html($item[7]);
                        if($html->find('a', 0)){
                            $maj_cf = $html->find('a', 0)->plaintext;
                            if ($maj_cf == '-') {
                                $maj_cf = -1;
                            }
                        } else {
                            $maj_cf = -1;
                        }
                        $html = str_get_html($item[8]);
                        if($html->find('a', 0)){
                            $maj_tf = $html->find('a', 0)->plaintext;
                            if ($maj_tf == '-') {
                                $maj_tf = -1;
                            }
                        } else {
                            $maj_tf = -1;
                        }
                        $html = str_get_html($item[9]);
                        if($html->find('a', 0)){
                            $alexa_rank = $html->find('a', 0)->plaintext;
                            if ($alexa_rank == '-') {
                                $alexa_rank = -1;
                            }
                        } else {
                            $alexa_rank = -1;
                        }
                        $html = str_get_html($item[10]);
                        if($html->find('a', 0)){
                            $moz_dom = $html->find('a', 0)->plaintext;
                            if ($moz_dom == '-') {
                                $moz_dom = -1;
                            }
                        } else {
                            $moz_dom = -1;
                        }
                        $html = str_get_html($item[11]);
                        if($html->find('a', 0)){
                            $maj_dom = $html->find('a', 0)->plaintext;
                            if ($maj_dom == '-') {
                                $maj_dom = -1;
                            }
                        } else {
                            $maj_dom = -1;
                        }
                        $html = str_get_html($item[12]);
                        if($html->find('a', 0)){
                            $page_rank = $html->find('a', 0)->plaintext;
                            if ($page_rank == '-') {
                                $page_rank = -1;
                            }
                        } else {
                            $page_rank = -1;
                        }
                        $html = str_get_html($item[13]);
                        if($html->find('a', 0)){
                            $similar_web = $html->find('a', 0)->plaintext;
                            if ($similar_web == '-') {
                                $similar_web = -1;
                            }
                        } else {
                            $similar_web = -1;
                        }
                        $html = str_get_html($item[14]);
                        if($html->find('a', 0)){
                            $sem_traf = $html->find('a', 0)->plaintext;
                            if ($sem_traf == '-') {
                                $sem_traf = -1;
                            }
                        } else {
                            $sem_traf = -1;
                        }
                        //$html = str_get_html($item[14]);
                        if($bot == 1){
                            $price = $item[15];
                            if ($price == '-') {
                                $price = -1;
                            }
                            $price = str_replace('$', '', $price);
                            $price = str_replace('', '', $price);
                            //print_r($price);
                            if (strpos($price, 'K')) {
                                $price = str_replace('.0', '', $price);
                                $price = (float)$price * 1000;
                            }
                            if (strpos($price, 'M')) {
                                $price = str_replace('.', '', $price);
                                $price = (float)$price * 100000;
                            }
                        } else {
                            $price = $item[15];
                            if ($price == '-') {
                                $price = -1;
                            }
                            $price = str_replace('$', '', $price);
                            preg_match_all('!\d+!', $price, $matches);
                            if (strpos($price, 'K')) {
                                $price = str_replace('.0', '', $price);
                                $price = floatval($matches[0]) * 1000;
                            }
                            if (strpos($price, 'M')) {
                                $price = str_replace('.', '', $price);
                                $price = floatval($matches[0]) * 100000;
                            }
                        }
                        $age = intval($this->convert($age));
                        $moz_da = intval($this->convert($moz_da));
                        $moz_pa = intval($this->convert($moz_pa));
                        $moz_rank = round((float)$moz_rank, 2);
                        $moz_trust = round((float)$moz_trust, 2);
                        $maj_cf = $this->convert($maj_cf);
                        $maj_tf = $this->convert($maj_tf);
                        $alexa_rank = $this->convert($alexa_rank);
                        $moz_dom = $this->convert($moz_dom);
                        $maj_dom = $this->convert($maj_dom);
                        $page_rank = $this->convert($page_rank);
                        $similar_web = $this->convert($similar_web);
                        $sem_traf = $this->convert($sem_traf);
                        $price = round((float)$this->convert($price, 2));
                        if($bot == 1){
                            if(strpos($item[16], 'ago') !== false){
                                $expire = explode(' ago', $item[16]);
                                $expire_time = $this->timeAgoToTime($expire[0]);
                                $insert_data = array(
                                    'domain' => $name,
                                    'age_year' => $age,
                                    'moz_da' => $moz_da,
                                    'moz_pa' => $moz_pa,
                                    'moz_rank' => $moz_rank,
                                    'moz_trust' => $moz_trust,
                                    'maj_cf' => $maj_cf,
                                    'maj_tf' => $maj_tf,
                                    'alexa_rank' => $alexa_rank,
                                    'moz_dom' => $moz_dom,
                                    'maj_dom' => $maj_dom,
                                    'page_rank' => $page_rank,
                                    'similar_web' => $similar_web,
                                    'sem_traf' => $sem_traf,
                                    'price' => $price,
                                    'expiring_in' => date('Y-m-d',$expire_time),
                                    'creat_at' => date('Y-m-d',time()),
                                    'index' => $index,
                                    //'buy' => $buy
                                );
                                //echo $name.PHP_EOL;
                                //echo date('Y-m-d',$expire_time).PHP_EOL;
                            } else {
                                $expire_time = time();
                                $insert_data = array(
                                    'domain' => $name,
                                    'age_year' => $age,
                                    'moz_da' => $moz_da,
                                    'moz_pa' => $moz_pa,
                                    'moz_rank' => $moz_rank,
                                    'moz_trust' => $moz_trust,
                                    'maj_cf' => $maj_cf,
                                    'maj_tf' => $maj_tf,
                                    'alexa_rank' => $alexa_rank,
                                    'moz_dom' => $moz_dom,
                                    'maj_dom' => $maj_dom,
                                    'page_rank' => $page_rank,
                                    'similar_web' => $similar_web,
                                    'sem_traf' => $sem_traf,
                                    'price' => $price,
                                    'expiring_in' => date('Y-m-d',$expire_time),
                                    'creat_at' => date('Y-m-d',time()),
                                    'index' => $index,
                                    //'buy' => $buy
                                );
                                //echo $name.PHP_EOL;
                                //echo date('Y-m-d',$expire_time).PHP_EOL;
                            }
                        } else {
                            $html = str_get_html($item[16]);
                            $expire_time = $html->plaintext;
                            $expire_time = str_replace('&nbsp;', '', $expire_time);
                            //print_r($item);die;
                            $expire_time = $this->timeAgoToTimeEx($expire_time);
                            $insert_data = array(
                                'domain' => $name,
                                'age_year' => $age,
                                'moz_da' => $moz_da,
                                'moz_pa' => $moz_pa,
                                'moz_rank' => $moz_rank,
                                'moz_trust' => $moz_trust,
                                'maj_cf' => $maj_cf,
                                'maj_tf' => $maj_tf,
                                'alexa_rank' => $alexa_rank,
                                'moz_dom' => $moz_dom,
                                'maj_dom' => $maj_dom,
                                'page_rank' => $page_rank,
                                'similar_web' => $similar_web,
                                'sem_traf' => $sem_traf,
                                'price' => $price,
                                'expiring_in' => date('Y-m-d H:i:s',$expire_time),
                                'creat_at' => date('Y-m-d',time()),
                                'index' => $index,
                                //'buy' => $buy
                            );
                            //echo $name.PHP_EOL;
                            //echo date('Y-m-d',$expire_time).PHP_EOL;die;
                        }
                        if($bot != 1){
                            $insert_data['statusSE'] = 2;
                        }
                        //var_dump($insert_data);
                        //$count = DB::table('domain_domcop_checked')->where('domain',$name)->count();
                        //if($count == 0){
                        
                        echo $name.PHP_EOL;
                        if($this->checkTLD($insert_data['domain'])){
                            $insert_data['is_del'] = 0;
                            //if($insert_data['maj_tf'] > 0){
                                $info = DB::table('domain_domcop_select')->where('domain',$insert_data['domain'])->first();
                                $insert_data['buy'] = $buy;
                                /*if($info){
                                   DB::table('domain_domcop_select')->where('domain',$insert_data['domain'])->update($insert_data);
                                } else {
                                    DB::table('domain_domcop_select')->insertOrIgnore($insert_data);
                                }*/
                                DB::table('domain_domcop_select')->insertOrIgnore($insert_data);
                                unset($insert_data['buy']);
                           // }
                        } else {
                            $insert_data['is_del'] = 1;
                        }
                        DB::table('domain_domcop_checked')->insertOrIgnore($insert_data);
                        //}
                    }
                    $i++;
                    DB::table('setting')->where('name',$type)->update(['content' => $i]);
                } 
            
            
            echo $i;
            sleep(5);
            } catch (\Exception $e) {
                sleep(10);
            }
        }

    }

    public function convert($value)
    {

        if (strpos($value, 'K')) {
            $value = str_replace('K', '', $value);

            $value = floatval($value) * 1000;
        } else {
            $value = floatval($value);
        }
        if (strpos($value, 'k')) {
            $value = str_replace('k', '', $value);
            $value = floatval($value) * 1000;
        } else {
            $value = floatval($value);
        }
        if (strpos($value, 'm')) {
            $value = str_replace('m', '', $value);
            $value = floatval($value) * 1000000;
        } else {
            $value = floatval($value);
        }
        if (strpos($value, 'M')) {
            $value = str_replace('M', '', $value);
            $value = floatval($value) * 1000000;
        } else {
            $value = floatval($value);
        }
        return $value;
    }

    public function timeAgoToTime($time)
    {
        if (strpos($time, ' ')) {
            $tmp = explode(' ', $time);
            $tt = 0;
            foreach ($tmp as $p) {
                if (strpos($p, 'd')) {
                    $p = str_replace('d', '', $p);
                    $p = $p * 86400;
                }
                if (strpos($p, 'h')) {
                    $p = str_replace('h', '', $p);
                    $p = $p * 3600;
                }
                if (strpos($p, 'm')) {
                    $p = str_replace('m', '', $p);
                    $p = $p * 60;
                }
                if (strpos($p, 's')) {
                    $p = 0;
                }
                $tt += (int)$p;
            }
            $time = time() - $tt;
        }
        if (strpos($time, 's')) {
            $time = str_replace('s', '', $time);
            $time = str_replace(' ', '', $time);
            $time = time() - (int)$time;
        }
        if (strpos($time, 'd')) {
            $time = str_replace('d', '', $time);
            $time = time() - $time * 86400;
        }
        if (strpos($time, 'm')) {
            $time = str_replace('m', '', $time);
            $time = time() - $time * 60;
        }
        if (strpos($time, 'y')) {
            $time = str_replace('y', '', $time);
            $time = time() - $time * 86400 * 365;
        }
        if (strpos($time, 'h')) {
            $time = str_replace('h', '', $time);
            $time = time() - $time * 3600;
        }

        return $time;

    }
    public function timeAgoToTimeEx($time)
    {
        //echo $time;
        $ss = 0;

        if (strpos($time, ' ') != false) {
            $tmp = explode(' ', $time);
            $tt = 0;
            //print_r($tmp);
            foreach ($tmp as $p) {
                if (strpos($p, 'd')) {
                    $p = str_replace('d', '', $p);
                    preg_match_all('!\d+!', $p, $matches);
                    
                    $p = (int)$matches[0][0] * 86400;
                }
                if (strpos($p, 'h')) {
                    $p = str_replace('h', '', $p);
                    preg_match_all('!\d+!', $p, $matches);
                    //print_r($matches);
                    $p = (int)$matches[0][0] * 3600;
                }
                if (strpos($p, 'm')) {
                    $p = str_replace('m', '', $p);
                    preg_match_all('!\d+!', $p, $matches);
                    $p = (int)$matches[0][0] * 60;
                }
                if (strpos($p, 's')) {
                    $p = 0;
                }
                $tt += (int)$p;
            }
            $ss = time() + $tt;
        }
        if (strpos($time, 's') != false && $ss == 0) {
            $time = str_replace('s', '', $time);
            $time = str_replace(' ', '', $time);
            preg_match_all('!\d+!', $time, $matches);
            $ss = time() + (int)$matches[0][0];
        }
        if (strpos($time, 'd') != false && $ss == 0) {
            $time = str_replace('d', '', $time);
            preg_match_all('!\d+!', $time, $matches);
            $ss = time() + (int)$matches[0][0] * 86400;
        }
        if (strpos($time, 'm') != false && $ss == 0) {
            $time = str_replace('m', '', $time);
            preg_match_all('!\d+!', $time, $matches);
            $ss = time() + (int)$matches[0][0] * 60;
        }
        if (strpos($time, 'y') != false && $ss == 0) {
            $time = str_replace('y', '', $time);
            preg_match_all('!\d+!', $time, $matches);
            $ss = time() + (int)$matches[0][0] * 86400 * 365;
        }
        if (strpos($time, 'h') != false && $ss == 0) {
            $time = str_replace('h', '', $time);
            preg_match_all('!\d+!', $time, $matches);
            $ss = time() + (int)$matches[0][0] * 3600;
        }
        return $ss;

    }
    public function value_attr(){
        $array = array(
            'User-Agent' => "Mozilla/5.0 (X11; Linux x86_64; rv:60.0) Gecko/20100101 Firefox/60.0",
            'cookie' => 'PHPSESSID=10cu0r2u54a6i9gom725ci32v8; dcbrowsertoken=ba8849c8b95a29d6f5af4c9977b6b5236cd544c8ac4042ad6f61d0ff31d4642d005bf65a749480e9656f56eff05f00587cb9ca3558547489e8cf5ec2a819b780; dv=1; vt=1583227759; __utma=199295163.2003276087.1583227760.1583227760.1583227760.1; __utmb=199295163.2.10.1583227760; __utmc=199295163; __utmz=199295163.1583227760.1.1.utmcsr=(direct)|utmccn=(direct)|utmcmd=(none); __utmt=1; _fbp=fb.1.1583227760217.174175220; RecordsPerPage=5000; _utmt=1;searchBoxStatus=shown;SrchMode=advanced',
        );
        return $array;
    }


    public function login()
    {
        $urlLogin = 'https://www.domcop.com/login';
        $header = array(
            'User-Agent' => $this->value_attr()['User-Agent'],
        );
        // loc
        $filter = array();
        $filter['file'] = public_path('domcop.txt');
        $filter['statuscode'] = false;
        $proxy = array();
        $res = httprequest($urlLogin, 'GET', 30, $header, $proxy, $filter);
        print_r($res);
        $body = 'email=tovicorp.com@gmail.com&password=MKwb0MN0';
        $filter['body'] = $body;
        $header = array(
            'User-Agent' => $this->value_attr()['User-Agent'],
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Content-Length' => strlen($body)
        );
        print_r($header);
        $res = httprequest($urlLogin, 'POST', 30, $header, $proxy, $filter);
        print_r($res);
    }

    public function getDomain($start, $page, $status = 1)
    {
        $header = array(

            'User-Agent' => $this->value_attr()['User-Agent'],
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Host' => 'www.domcop.com',
            'Connection' => 'keep-alive',
            'Accept' => '*/*',
            'Accept-Language' => 'en-US,en;q=0.5',
            'Referer' => 'https://www.domcop.com/domains',
            'Cookie' => $this->value_attr()['cookie'],
        );
        $captcha = httprequest('https://www.domcop.com/service/recaptchaRevalidationCheck.php', 'GET', 30, $header);
        /*print_r($captcha);*/        
        $captcha = json_decode($captcha['msg'], true);
        $code_cap = '';
        if ($captcha["proveNotRobot"] == true) {

            $res_cap = $this->recaptcha('https://www.domcop.com/domains');
            if ($res_cap != false) {
                $code_cap = $res_cap;
            }

        }
        $url = 'https://www.domcop.com/domains';
        $header = array(
            'User-Agent' => $this->value_attr()['User-Agent'],
            'Cookie' => $this->value_attr()['cookie'],
           );
        $filter = array();
        //$filter['file'] = public_path('domcop.txt');
        $filter['statuscode'] = false;
        //$body = 'email=tovicorp.com@gmail.com&password=MKwb0MN0';
        //$filter['body'] = $body;
        $proxy = array();
        $res = httprequest($url, 'GET', 30, $header, $proxy, $filter);
        //print_r($res);
        $html = $res['msg'];
        echo $code_cap.PHP_EOL;
        $tmp = explode('aoData.push( { "name": "dt_sg", "value": \'', $html);
        $tmp = explode('\' } );', $tmp[1]);
        $code = $tmp[0];
        //echo $code;
        if($status == 1){
            //$body = 'sEcho=' . $page . '&iColumns=17&sColumns=&iDisplayStart=' . $start . '&iDisplayLength=500&mDataProp_0=0&mDataProp_1=1&mDataProp_2=2&mDataProp_3=3&mDataProp_4=4&mDataProp_5=5&mDataProp_6=6&mDataProp_7=7&mDataProp_8=8&mDataProp_9=9&mDataProp_10=10&mDataProp_11=11&mDataProp_12=12&mDataProp_13=13&mDataProp_14=14&mDataProp_15=15&mDataProp_16=16&iSortCol_0=8&sSortDir_0=desc&iSortingCols=1&bSortable_0=false&bSortable_1=true&bSortable_2=true&bSortable_3=true&bSortable_4=true&bSortable_5=true&bSortable_6=true&bSortable_7=true&bSortable_8=true&bSortable_9=true&bSortable_10=true&bSortable_11=true&bSortable_12=true&bSortable_13=true&bSortable_14=true&bSortable_15=true&bSortable_16=true&g-recaptcha-response=' . $code_cap . '&screen_width=1903&data_source=expired&dt_time=' . time() . '&dt_sg=' . $code . '&page_rank_upper=10&page_rank_lower=0&moz_rank_upper=10&moz_rank_lower=0&domain_authority_upper=-1&domain_authority_lower=0&page_authority_upper=100&page_authority_lower=0&citation_flow_upper=100&citation_flow_lower=0&trust_flow_upper=100&trust_flow_lower=0&price_upper=10000&price_lower=500&back_links_upper=NaN&back_links_lower=0&maj_links_upper=NaN&maj_links_lower=0&char_length_upper=NaN&char_length_lower=0&ends_in_lower=0&ends_in_upper=17&age_upper=NaN&age_lower=0&parent_category=&child_category_2=&child_category_3=&child_category_4=&child_category_5=&fake_pr=false&google_links=false&google_index=false&alexa_rank=false&dmoz=false&edu_links=false&gov_links=false&allow_dashes=true&allow_digits=true&saletype_auction=true&saletype_buynow=true&saletype_closeout=true&saletype_pending=true&saletype_offer=true&saletype_bargain=true&ext_com=true&ext_net=true&ext_org=true&ext_others=true&require_semrush_rank=false&require_semrush_traffic=false&search_type=keyword&keyword=&keyword_search_type=contains&pattern=&patternType=&type_godaddy=true&type_namejet=true&type_snapnames=true&type_sedo=true&type_dynadot=true&type_droplists=true&require_available=';
            $body = 'sEcho=' . $page . '&iColumns=17&sColumns=&iDisplayStart=' . $start . '&iDisplayLength=5000&mDataProp_0=0&mDataProp_1=1&mDataProp_2=2&mDataProp_3=3&mDataProp_4=4&mDataProp_5=5&mDataProp_6=6&mDataProp_7=7&mDataProp_8=8&mDataProp_9=9&mDataProp_10=10&mDataProp_11=11&mDataProp_12=12&mDataProp_13=13&mDataProp_14=14&mDataProp_15=15&mDataProp_16=16&iSortCol_0=7&sSortDir_0=asc&iSortingCols=1&bSortable_0=false&bSortable_1=true&bSortable_2=true&bSortable_3=true&bSortable_4=true&bSortable_5=true&bSortable_6=true&bSortable_7=true&bSortable_8=true&bSortable_9=true&bSortable_10=true&bSortable_11=true&bSortable_12=true&bSortable_13=true&bSortable_14=true&bSortable_15=true&bSortable_16=true&g-recaptcha-response='.$code_cap.'&screen_width=1903&data_source=live&dt_time='.time().'&dt_sg='.$code.'&chkRequireGeoDomain=false&chkRequireMatchGeoDomain=false&geoName=&fake_pr=false&require_pr=false&fake_alexa=false&require_semrush_keywords=false&require_semrush_traffic=false&adv_ctf_upper=NaN&adv_ctf_lower=NaN&trust_flow_lower=1&maj_million=false&quantcast_million=false&alexa_rank=false&require_semrush_rank=false&require_similarweb_rank=false&require_available=false&hide_adult=true&hide_spammy=true&hide_brand=false&soc_total_upper=NaN&soc_total_lower=NaN&soc_fb_total_upper=NaN&soc_fb_total_lower=NaN&soc_fb_like_upper=NaN&soc_fb_like_lower=NaN&soc_fb_share_upper=NaN&soc_fb_share_lower=NaN&soc_twitter_upper=NaN&soc_twitter_lower=NaN&soc_reditt_upper=NaN&soc_reditt_lower=NaN&soc_linked_in_upper=NaN&soc_linked_in_lower=NaN&soc_digg_upper=NaN&soc_digg_lower=NaN&soc_delicious_upper=NaN&soc_delicious_lower=NaN&soc_stumble_upon_upper=NaN&soc_stumble_upon_lower=NaN&soc_pinterest_upper=NaN&soc_pinterest_lower=NaN&soc_google_upper=NaN&soc_google_lower=NaN&ends_in_lower_domain=0&ends_in_upper_domain=1&ends_in_lower=0&ends_in_upper=14&parent_category=&child_category_2=&child_category_3=&child_category_4=&child_category_5=&parent_topical_category=&child_topical_category=&google_index=true&google_news=false&dmoz=false&allow_dashes=false&allow_digits=false&only_digits=false&saletype_auction=true&saletype_buynow=true&saletype_closeout=true&saletype_pending=true&saletype_offer=true&saletype_bargain=true&search_type=keyword&anchor_text_keyword=&keyword=&keyword_search_type=contains&pattern=&patternType=&ext_38=true&ext_55=true&ext_36=true&ext_6=true&ext_17=true&ext_23=true&ext_18=true&ext_56=true&ext_5=true&ext_57=true&ext_10=true&ext_25=true&ext_16=true&ext_77=true&ext_58=false&ext_27=true&ext_1=true&ext_39=true&ext_8=true&ext_40=true&ext_59=true&ext_33=true&ext_21=true&ext_41=true&ext_60=true&ext_11=true&ext_42=true&ext_61=true&ext_43=true&ext_32=true&ext_19=true&ext_4=true&ext_37=true&ext_62=true&ext_15=true&ext_44=true&ext_63=false&ext_71=true&ext_67=true&ext_30=true&ext_64=true&ext_68=true&ext_45=true&ext_69=true&ext_28=true&ext_70=true&ext_7=true&ext_22=true&ext_2=true&ext_46=true&ext_24=true&ext_47=true&ext_12=true&ext_48=true&ext_3=true&ext_31=true&ext_49=true&ext_72=true&ext_73=true&ext_65=true&ext_20=false&ext_74=true&ext_13=true&ext_75=true&ext_50=true&ext_66=true&ext_29=true&ext_51=true&ext_26=true&ext_14=true&ext_9=true&ext_52=true&ext_76=true&ext_53=true&chkMajLangen=true&chkMajLangru=false&chkMajLangja=false&chkMajLangde=true&chkMajLanges=true&chkMajLangfr=true&chkMajLangpt=true&chkMajLangit=true&chkMajLangzh=false&chkMajLangpl=true&chkMajLangtr=true&chkMajLangfa=true&chkMajBlLangen=true&chkMajBlLangru=true&chkMajBlLangja=true&chkMajBlLangde=true&chkMajBlLanges=true&chkMajBlLangfr=true&chkMajBlLangpt=true&chkMajBlLangit=true&chkMajBlLangzh=true&chkMajBlLangpl=true&chkMajBlLangtr=true&chkMajBlLangfa=true&chkShowTypeCroatian=false&chkShowTypeDanish=false&chkShowTypeDutch=false&chkShowTypeEnglish=false&chkShowTypeFinnish=false&chkShowTypeFrench=false&chkShowTypeGerman=false&chkShowTypeHungarian=false&chkShowTypeIndonesian=false&chkShowTypeItalian=false&chkShowTypeMalay=false&chkShowTypeNorwegian=false&chkShowTypePolish=false&chkShowTypePortuguese=false&chkShowTypeRomanian=false&chkShowTypeSlovak=false&chkShowTypeSpanish=false&chkShowTypeSwedish=false&chkShowTypeTurkish=false&ext_misc=true&type_godaddy=true&type_namejet=true&type_snapnames=true&type_sedo=true&type_dynadot=true&type_namesilo=true&type_namepal=true&type_huntingmoon=true&type_domainmarket=true&type_paarkio=true&type_dropcatch=true&type_flippa=true';
        } else {
            $body = 'sEcho='.$page.'&iColumns=17&sColumns=&iDisplayStart=' . $start . '&iDisplayLength=5000&mDataProp_0=0&mDataProp_1=1&mDataProp_2=2&mDataProp_3=3&mDataProp_4=4&mDataProp_5=5&mDataProp_6=6&mDataProp_7=7&mDataProp_8=8&mDataProp_9=9&mDataProp_10=10&mDataProp_11=11&mDataProp_12=12&mDataProp_13=13&mDataProp_14=14&mDataProp_15=15&mDataProp_16=16&iSortCol_0=7&sSortDir_0=asc&iSortingCols=1&bSortable_0=false&bSortable_1=true&bSortable_2=true&bSortable_3=true&bSortable_4=true&bSortable_5=true&bSortable_6=true&bSortable_7=true&bSortable_8=true&bSortable_9=true&bSortable_10=true&bSortable_11=true&bSortable_12=true&bSortable_13=true&bSortable_14=true&bSortable_15=true&bSortable_16=true&g-recaptcha-response='.$code_cap.'&screen_width=1903&data_source=live&dt_time='.time().'&dt_sg='.$code.'&chkRequireGeoDomain=false&chkRequireMatchGeoDomain=false&geoName=&fake_pr=false&require_pr=false&fake_alexa=false&require_semrush_keywords=false&require_semrush_traffic=false&adv_ctf_upper=NaN&adv_ctf_lower=NaN&trust_flow_lower=1&maj_million=false&quantcast_million=false&alexa_rank=false&require_semrush_rank=false&require_similarweb_rank=false&require_available=false&hide_adult=true&hide_spammy=true&hide_brand=false&soc_total_upper=NaN&soc_total_lower=NaN&soc_fb_total_upper=NaN&soc_fb_total_lower=NaN&soc_fb_like_upper=NaN&soc_fb_like_lower=NaN&soc_fb_share_upper=NaN&soc_fb_share_lower=NaN&soc_twitter_upper=NaN&soc_twitter_lower=NaN&soc_reditt_upper=NaN&soc_reditt_lower=NaN&soc_linked_in_upper=NaN&soc_linked_in_lower=NaN&soc_digg_upper=NaN&soc_digg_lower=NaN&soc_delicious_upper=NaN&soc_delicious_lower=NaN&soc_stumble_upon_upper=NaN&soc_stumble_upon_lower=NaN&soc_pinterest_upper=NaN&soc_pinterest_lower=NaN&soc_google_upper=NaN&soc_google_lower=NaN&ends_in_lower_domain=0&ends_in_upper_domain=1&ends_in_lower=0&ends_in_upper=14&parent_category=&child_category_2=&child_category_3=&child_category_4=&child_category_5=&parent_topical_category=&child_topical_category=&google_index=true&google_news=false&dmoz=false&allow_dashes=false&allow_digits=false&only_digits=false&saletype_auction=true&saletype_buynow=true&saletype_closeout=true&saletype_pending=true&saletype_offer=true&saletype_bargain=true&search_type=keyword&anchor_text_keyword=&keyword=&keyword_search_type=contains&pattern=&patternType=&ext_38=true&ext_55=true&ext_36=true&ext_6=true&ext_17=true&ext_23=true&ext_18=true&ext_56=true&ext_5=true&ext_57=true&ext_10=true&ext_25=true&ext_16=true&ext_77=true&ext_58=false&ext_27=true&ext_1=true&ext_39=true&ext_8=true&ext_40=true&ext_59=true&ext_33=true&ext_21=true&ext_41=true&ext_60=true&ext_11=true&ext_42=true&ext_61=true&ext_43=true&ext_32=true&ext_19=true&ext_4=true&ext_37=true&ext_62=true&ext_15=true&ext_44=true&ext_63=false&ext_71=true&ext_67=true&ext_30=true&ext_64=true&ext_68=true&ext_45=true&ext_69=true&ext_28=true&ext_70=true&ext_7=true&ext_22=true&ext_2=true&ext_46=true&ext_24=true&ext_47=true&ext_12=true&ext_48=true&ext_3=true&ext_31=true&ext_49=true&ext_72=true&ext_73=true&ext_65=true&ext_20=false&ext_74=true&ext_13=true&ext_75=true&ext_50=true&ext_66=true&ext_29=true&ext_51=true&ext_26=true&ext_14=true&ext_9=true&ext_52=true&ext_76=true&ext_53=true&chkMajLangen=true&chkMajLangru=false&chkMajLangja=false&chkMajLangde=true&chkMajLanges=true&chkMajLangfr=true&chkMajLangpt=true&chkMajLangit=true&chkMajLangzh=false&chkMajLangpl=true&chkMajLangtr=true&chkMajLangfa=true&chkMajBlLangen=true&chkMajBlLangru=true&chkMajBlLangja=true&chkMajBlLangde=true&chkMajBlLanges=true&chkMajBlLangfr=true&chkMajBlLangpt=true&chkMajBlLangit=true&chkMajBlLangzh=true&chkMajBlLangpl=true&chkMajBlLangtr=true&chkMajBlLangfa=true&chkShowTypeCroatian=false&chkShowTypeDanish=false&chkShowTypeDutch=false&chkShowTypeEnglish=false&chkShowTypeFinnish=false&chkShowTypeFrench=false&chkShowTypeGerman=false&chkShowTypeHungarian=false&chkShowTypeIndonesian=false&chkShowTypeItalian=false&chkShowTypeMalay=false&chkShowTypeNorwegian=false&chkShowTypePolish=false&chkShowTypePortuguese=false&chkShowTypeRomanian=false&chkShowTypeSlovak=false&chkShowTypeSpanish=false&chkShowTypeSwedish=false&chkShowTypeTurkish=false&ext_misc=true&type_godaddy=true&type_namejet=true&type_snapnames=true&type_sedo=true&type_dynadot=true&type_namesilo=true&type_namepal=true&type_huntingmoon=true&type_domainmarket=true&type_paarkio=true&type_dropcatch=true&type_flippa=true';

        }
        //print_r($tmp[0]);
        $filter['body'] = $body;
        $header = array(

            'User-Agent' => $this->value_attr()['User-Agent'],
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Host' => 'www.domcop.com',
            'Connection' => 'keep-alive',
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            'DNT' => 1,
            'Accept' => 'application/json, text/javascript,*/*',
            'Accept-Language' => 'vi-VN,vi;q=0.9,fr-FR;q=0.8,fr;q=0.7,en-US;q=0.6,en;q=0.5',
            'Cookie' => $this->value_attr()['cookie'],
            'Content-Length' => strlen($body)
        );
        //print_r($filter);
        $urlData = 'https://www.domcop.com/getDomains.php';
        $data = httprequest($urlData, 'POST', 30, $header, array(), $filter);
        //print_r($data);

        return $data;
    }
    public function checkTLD($domain){
        // .com, .net, org, info, de, fr, uk, us, me, co, tv, es, ca, eu, au, nz, it, mobi, tech, io, today, guide, 
        /*$list = array('.co','.com','.net','.org','.info','.me','.website','.io','.us','.xyz','.de','.fr','.ca','.es','.tv','.eu','.au','.nz','.it','.mobile','.tech','.today','.guide');
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
        return $check;*/
        return true;
        
    }
    public function recaptcha($url)
    {
        $api = new NoCaptchaProxyless();
        $api->setVerboseMode(true);

        //your anti-captcha.com account key
        $api->setKey('6ba9fa00afd3332946d144219ac0c149');

        //recaptcha key from target website
        $api->setWebsiteURL($url);
        $api->setWebsiteKey('6LfLwI4UAAAAADRoaQZs3hBl9xzsmwqz126iqYEq');

        if (!$api->createTask()) {
            //$api->debout("API v2 send failed - ".$api->getErrorMessage(), "red");
            return false;
        }

        $taskId = $api->getTaskId();


        if (!$api->waitForResult()) {
            //$api->debout("could not solve captcha", "red");
            //$api->debout($api->getErrorMessage());
            return false;
        } else {
            $recaptchaToken = $api->getTaskSolution();
            //echo "\ntoken result: $recaptchaToken\n\n";
            return $recaptchaToken;
        }
    }
}
