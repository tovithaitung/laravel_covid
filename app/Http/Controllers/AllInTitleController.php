<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
require_once 'simple_html_dom.php';
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use DB;
use Facebook\WebDriver\Exception;
use App\Exports\Keywords;
use Maatwebsite\Excel\Facades\Excel;

use App\Imports\ListCheckImport;
ini_set('memory_limit',-1);
class AllInTitleController extends Controller
{
    private $api_key = '8d97fca1da2e0cf3f0fe4741fe99d9b5';
    private $site_key = '6LfwuyUTAAAAAOAmoS0fdqijC2PbbdH4kjq62Y1b';
    public function resultSearch($keyword, $proxy = false){
        $query = urlencode($keyword);
        $url = 'http://www.google.com/search?q=allintitle:' . $query."&hl=en";
        $ch = curl_init();
        curl_setopt_array(
            $ch,
            [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLINFO_SSL_VERIFYRESULT => false,
                CURLOPT_PROXYTYPE => 7,
                CURLOPT_PROXY => "195.201.192.254",
                CURLOPT_PROXYPORT => "28982",
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 5.1; rv:40.0) Gecko/20100101 Firefox/40.0',
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HEADER => 0
            ]
        );
        
        $respones = curl_exec($ch);
        $err = curl_error($ch);
        if ($err) {
            echo "cURL Error #:" . $err;
            return -1;
        }
        curl_close($ch);
        $html = str_get_html($respones);
        print_r($respones);
        $resultStats = $html->find('#resultStats', 0);
        $check = isset($resultStats) ? true : false;

        $result = $check ? $resultStats->plaintext : "";
        //print_r("dad".$result);
        if($result == ""){
            return -2;
        }
        $results = explode('(', $result);
        unset($results[1]);
        $str = implode('', $results);
//            var_dump($str);
        $int = (int)filter_var($str, FILTER_SANITIZE_NUMBER_INT);
        return $int;
    }
    public function exportAppid(){

        //$list = DB::table('keywords')->where('created_at','>=','2019-06-12')->get();
        //DB::table('orders')->selectRaw('price * ? as price_with_tax', [1.0825])->get();
        //$list = DB::select(DB::raw("SELECT DISTINCT(keyword),keyword_id,allintitle,search_volumn,kwplaner,difficult,appid,domain,url FROM `keywords` WHERE `kwplaner` >= 1000 and difficult <= 1 and domain is null ORDER BY `keywords`.`difficult` ASC,search_volumn DESC"));
        //$list = DB::table('keywords')->where('releaseDate','>=',time()-86400*30*12*3)->where('appid','!=','')->get();
        //$list = DB::table('keywords')->where('domain','keywd_10_9')->where('allintitle','>',0)->get();
        $list = DB::table('keywords')->where('domain','tovi_22_12')->where('allintitle','>',0)->get();
        $list = json_encode($list);
        $list = json_decode($list,true);
        /*for ($i = 0; $i < count($list); $i++) {
            for ($j = $i + 1; $j < count($list); $j++) {
                if($list[$i]['search_volumn'] < $list[$j]['search_volumn']){
                    $tmp = $list[$i]['search_volumn'];
                    $list[$i]['search_volumn'] = $list[$j]['search_volumn'];
                    $list[$j]['search_volumn'] = $tmp;
                }
            }
        }*/
        //print_r($list);die;
        $res = array();
        $res[] = array('keyword','url','allintitle','difficult','search_volumn');
        foreach ($list as $p) {
            //$p['difficult'] = $p
            $res[] = array($p['keyword'], $p['url'], $p['allintitle'], $p['difficult'], $p['kwplaner']);
        }
        $export = new Keywords($res);
        
        // Excel::create('ahieu.xlsx', function($excel) {

        // $excel->sheet('demo', function($sheet) {

        //     $sheet->fromArray($res);

        // });
        // $export->setColumnFormat(array(
        //     'B' => '0',
        //     'C' => '0',
        //     'D' => '0'
        // ));

        // })->store('xls','public/');
        return Excel::store($export,'public/tovi_22_12.xlsx',);
    }
    public function runSearch($offset){
        //$keywords = DB::table('keywords')->where('difficult','<=',1)->where('domain','xda')->where('status',1)->orderby('keyword_id','DESC')->get();
        while(1){
            $keywords = DB::table('keywords')->where('domain','tovi_22_12')->where('kwplaner','>=',10)->orderby('keyword_id','DESC')->get();
            echo count($keywords);
            $res = array();
            foreach ($keywords as $key => $keyword) {
                $res[] = array('keyword' => $keyword->keyword,'keyword_id' => $keyword->keyword_id);
                if($key %15 ==0){
                    $this->seachSelenium($res);
                    $res = array();
                }
            }
        }
        /*if(count($res) > 0){
            $this->seachSelenium($res);
        } else {
            echo 'no keyword';
        }*/
    }
    public function releateSearch($listKey){
        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();
        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $options->setExperimentalOption("prefs", $prefs);
        // firefox
        $profile = new FirefoxProfile();
        $profile->setPreference('network.proxy.type', 1);
        # Set proxy to Tor client on localhost
        $profile->setPreference('network.proxy.socks', '67.205.180.86');
        $profile->setPreference('network.proxy.socks_port', 28982);
        
        $caps = DesiredCapabilities::firefox();
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);
        //$caps->setCapability(ChromeOptions::CAPABILITY, $options);
        // $capabilities = [
        //     WebDriverCapabilityType::BROWSER_NAME => 'firefox',
        //     WebDriverCapabilityType::PROXY => [
        //         'proxyType' => 'manual',
        //         'socksProxy' => '104.248.64.188:28982',
        //         //'sslProxy' => '127.0.0.1:2043',
        //     ],
        // ];
        if ($USE_FIREFOX)
        {
            $driver = RemoteWebDriver::create(
                $host, 
                $caps
            );
        }
        else
        {
            $driver = RemoteWebDriver::create(
                $host, 
                $caps
            );
        }
        $driver->get("https://www.google.com?hl=en");
        //$driver->get('https://whatismyip.com');die;
        # enter text into the search field
        foreach ($listKey as $key) {
            try {
                $title = preg_replace("/[^A-Za-z0-9 ]/", '', $key['title']);

                $title = trim(preg_replace('/\s+/', ' ', $title));
                $title = $title.' apk';
                $key['title'] = $title;
                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                sleep(1);
                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($key['title']);
                sleep(3);
                $list = $driver->findElement(WebDriverBy::cssSelector('ul.erkvQe'))->getAttribute('innerHTML');
                $dom = str_get_html($list);
                $listRelate = array();
                if($dom){
                    $list_relate = $dom->find('li');
                    
                    foreach ($list_relate as $p) {
                        $text = $p->find('span',0)->innertext;
                        $text = str_replace('<b>','', $text);
                        $text = str_replace('</b>','', $text);
                        //echo $text.PHP_EOL;
                        $listRelate[] = $text;
                    }
                } else {
                    continue;
                    // $driver->findElement(WebDriverBy::cssSelector('div.FPdoLc .gNO89b'))->click();
                    // sleep(5);
                }
                
                //App::where('app_top_id',$key['app_top_id'])->update(['relate'=> json_encode($listRelate),'status' => 1]);
                AppRelate::where('app_relate_id',$key['app_relate_id'])->update(['relate'=> json_encode($listRelate),'statusKW' => 1]);
                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();

            } catch (\Exception $captcha) {
                print_r($captcha);
                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                sleep(5);
                die;
            }
        }
        //return $listRelate;
        
        //$driver->quit();
    }
    public function seachSelenium($listKey){
        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();
        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $options->setExperimentalOption("prefs", $prefs);
        // firefox
        $profile = new FirefoxProfile();
        $profile->setPreference('network.proxy.type', 1);
        # Set proxy to Tor client on localhost
        $profile->setPreference('network.proxy.socks', '67.205.180.86');
        $profile->setPreference('network.proxy.socks_port', 28982);
        
        $caps = DesiredCapabilities::firefox();
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);
        //$caps->setCapability(ChromeOptions::CAPABILITY, $options);
        // $capabilities = [
        //     WebDriverCapabilityType::BROWSER_NAME => 'firefox',
        //     WebDriverCapabilityType::PROXY => [
        //         'proxyType' => 'manual',
        //         'socksProxy' => '104.248.64.188:28982',
        //         //'sslProxy' => '127.0.0.1:2043',
        //     ],
        // ];
        // $caps->setCapability(
        //     'moz:firefoxOptions',
        //    ['args' => ['-headless']]
        // );
        if ($USE_FIREFOX)
        {
            $driver = RemoteWebDriver::create(
                $host, 
                $caps
            );
        }
        else
        {
            $driver = RemoteWebDriver::create(
                $host, 
                $caps
            );
        }
        $driver->get("https://www.google.com?hl=en");
        //$driver->get('https://whatismyip.com');die;
        # enter text into the search field
        try {
            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys('allintitle:'.$listKey[0]['keyword']);
            sleep(1);
            $driver->findElement(WebDriverBy::cssSelector('div.FPdoLc .gNO89b'))->click();
            sleep(5);

            try {
                $id = $driver->findElement(WebDriverBy::id("resultStats"))->getAttribute('innerHTML');
                $tmp = explode('About',$id);
                $tmp = explode('results', $tmp[1]);
                $result = $tmp[0];
                $result = str_replace('.', '', $result);
                $this->updateKey($listKey[0]['keyword_id'], $result);
                //print_r($result.PHP_EOL);
                unset($listKey[0]);
                foreach ($listKey as $key => &$value) {
                    if($key ==0){
                        continue;
                    }
                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                    sleep(1);
                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys('allintitle:'.$value['keyword']);
                    sleep(1);
                    $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                    sleep(2);
                    try {
                        $id =$driver->findElement(WebDriverBy::id("resultStats"))->getAttribute('innerHTML');
                        //print_r($id.PHP_EOL);
                        $tmp = explode('About',$id);
                        $tmp = explode('results', $tmp[1]);
                        $result = $tmp[0];
                        $result = str_replace('.', '', $result);
                        $this->updateKey($value['keyword_id'], $result);
                        unset($listKey[$key]);
                    } catch (\Exception $e) {
                        echo 'fail';
                    }
                }
            } catch (\Exception $e) {
                $urlAry = $driver->executeScript('return window.location',array());
                $currentURL = $urlAry['href'];
                $recaptchaToken = $this->recaptcha($currentURL);
                if($recaptchaToken != false){
                    $driver->executeScript('document.getElementById("g-recaptcha-response").innerHTML = "'.$recaptchaToken.'"');
                    $driver->executeScript('document.getElementById("captcha-form").submit()');
                    sleep(10);
                    try {
                        $id =$driver->findElement(WebDriverBy::id("resultStats"))->getAttribute('innerHTML');
                        //print_r($id.PHP_EOL);
                        $tmp = explode('About',$id);
                        $tmp = explode('results', $tmp[1]);
                        $result = $tmp[0];
                        $result = str_replace('.', '', $result);
                        $this->updateKey($listKey[0]['keyword_id'], $result);
                        print_r($result.PHP_EOL);
                        unset($listKey[0]);
                        print_r($listKey);
                        foreach ($listKey as $key => $value) {
                            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                            sleep(1);
                            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys('allintitle:'.$value['keyword']);
                            sleep(1);
                            $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                            sleep(2);
                            try {
                                $id =$driver->findElement(WebDriverBy::id("resultStats"))->getAttribute('innerHTML');
                                //print_r($id.PHP_EOL);
                                if(strpos($id, 'About') !== false){
                                    $tmp = explode('About',$id);
                                    $tmp = explode('results', $tmp[1]);
                                    $result = $tmp[0];
                                    $result = str_replace('.', '', $result);
                                    $result = str_replace(',', '', $result);
                                    $this->updateKey($value['keyword_id'], $result);
                                    print_r($result.PHP_EOL);
                                    unset($listKey[$key]);
                                } else {
                                    $tmp = explode('result', $id);
                                    $result = $tmp[0];
                                    $result = str_replace('.', '', $result);
                                    $result = str_replace(',', '', $result);
                                    $this->updateKey($value['keyword_id'], $result);
                                    print_r($result.PHP_EOL);
                                    unset($listKey[$key]);
                                }
                            } catch (\Exception $e3) {
                                $urlAry = $driver->executeScript('return window.location',array());
                                $currentURL = $urlAry['href'];
                                if($currentURL == 'https://www.google.com/sorry/index'){
                                    $this->updateKey($listKey[$key]['keyword_id'], -2);
                                } else {
                                    $this->updateKey($listKey[$key]['keyword_id'], -1);
                                }
                                unset($listKey[$key]);
                            }
                            
                        }
                    } catch (\Exception $e1) {
                        $urlAry = $driver->executeScript('return window.location',array());
                        $currentURL = $urlAry['href'];
                        if($currentURL == 'https://www.google.com/sorry/index'){
                            $this->updateKey($listKey[0]['keyword_id'], -2);
                        } else {

                            $this->updateKey($listKey[0]['keyword_id'], -1);
                            foreach ($listKey as $key => $value) {
                                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                                sleep(1);
                                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys('allintitle:'.$value['keyword']);
                                sleep(1);
                                $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                                sleep(2);
                                try {
                                    $id =$driver->findElement(WebDriverBy::id("resultStats"))->getAttribute('innerHTML');
                                    //print_r($id.PHP_EOL);
                                    if(strpos($id, 'About') !== false){
                                        $tmp = explode('About',$id);
                                        $tmp = explode('results', $tmp[1]);
                                        $result = $tmp[0];
                                        $result = str_replace('.', '', $result);
                                        $result = str_replace(',', '', $result);
                                        $this->updateKey($value['keyword_id'], $result);
                                        print_r($result.PHP_EOL);
                                        unset($listKey[$key]);
                                    } else {
                                        $tmp = explode('result', $id);
                                        $result = $tmp[0];
                                        $result = str_replace('.', '', $result);
                                        $result = str_replace(',', '', $result);
                                        $this->updateKey($value['keyword_id'], $result);
                                        print_r($result.PHP_EOL);
                                        unset($listKey[$key]);
                                    }
                                } catch (\Exception $e3) {
                                    $urlAry = $driver->executeScript('return window.location',array());
                                    $currentURL = $urlAry['href'];
                                    if($currentURL == 'https://www.google.com/sorry/index'){
                                        $this->updateKey($listKey[$key]['keyword_id'], -2);
                                    } else {
                                        $this->updateKey($listKey[$key]['keyword_id'], -1);
                                    }
                                    unset($listKey[$key]);
                                }
                            
                            }
                        }
                        unset($listKey[0]);
                    }
                }
            }
        } catch (\Exception $captcha) {
            
        }
        
        $driver->quit();
    }
    public function runcheckIndex(){
        //$keywords = DB::table('keywords')->where('difficult','<=',1)->where('domain','xda')->where('status',1)->orderby('keyword_id','DESC')->get();
        $list = DB::table('domain_requests')->where('code',1)->pluck('domain_request_id');
        $domains = DB::table('domain_link')->whereIn('domain_request_id',$list)->distinct('domain_out_id')->pluck('domain_out_id');
        //print_r($domains);
        $tmp = array();
        foreach ($domains as $domain) {
            $tmp[] = $domain;
        }
        $list_check = DB::table('domain_out')->where('statusWayback',0)->where('price','>',0)->where('price','<=',13)->get();
        //print_r(count($domains));
        $res = array();
        foreach ($list_check as $id) {
            if(in_array($id->domain_out_id, $tmp)){
                $res[] = $id;
                //DB::table('domain_out')->where('domain_name',$id->domain_name)->update(['check_status' => 8]);
            }
        }
        $listDomain = array();
        foreach($res as $key => $domain){
            $listDomain[] = array('domain' => $domain->domain_name);
            if($key %15 ==0){ 
                $this->getIndex($listDomain);
                $listDomain = array();
            }
        }
        print_r(count($res));
    }
    public function getIndex($listKey){
        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();
        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $options->setExperimentalOption("prefs", $prefs);
        // firefox
        $profile = new FirefoxProfile();
        $profile->setPreference('network.proxy.type', 1);
        # Set proxy to Tor client on localhost
        $profile->setPreference('network.proxy.socks', '67.205.180.86');
        $profile->setPreference('network.proxy.socks_port', 28982);
        
        $caps = DesiredCapabilities::firefox();
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);
        //$caps->setCapability(ChromeOptions::CAPABILITY, $options);
        // $capabilities = [
        //     WebDriverCapabilityType::BROWSER_NAME => 'firefox',
        //     WebDriverCapabilityType::PROXY => [
        //         'proxyType' => 'manual',
        //         'socksProxy' => '104.248.64.188:28982',
        //         //'sslProxy' => '127.0.0.1:2043',
        //     ],
        // ];
        if ($USE_FIREFOX)
        {
            $driver = RemoteWebDriver::create(
                $host, 
                $caps
            );
        }
        else
        {
            $driver = RemoteWebDriver::create(
                $host, 
                $caps
            );
        }
        $driver->get("https://www.google.com?hl=en");
        //$driver->get('https://whatismyip.com');die;
        # enter text into the search field
        try {
            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys('site:'.$listKey[0]['domain']);
            sleep(1);
            $driver->findElement(WebDriverBy::cssSelector('div.FPdoLc .gNO89b'))->click();
            sleep(5);

            try {
                $id = $driver->findElement(WebDriverBy::id("resultStats"))->getAttribute('innerHTML');
                if(strpos('results', $id)){
                    $tmp = explode('About',$id);
                    $tmp = explode('results', $tmp[1]);
                
                    $result = $tmp[0];
                    $result = str_replace('.', '', $result);
                } else {
                    $tmp = explode('result', $id);
                
                    $result = $tmp[0];
                    $result = str_replace(' ', '', $result);
                }
                //$this->updateKey($listKey[0]['domain'], $result);
                //print_r($result);die;
                DB::table('domain_out')->where('domain_name',$listKey[0]['domain'])->update(['statusWayback' => 1, 'total_index' => $result]);
                //print_r($result.PHP_EOL);
                unset($listKey[0]);
                foreach ($listKey as $key => &$value) {
                    // if($key ==0){
                    //     continue;
                    // }
                    echo '1';
                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                    sleep(1);
                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys('site:'.$value['domain']);
                    sleep(1);
                    $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                    sleep(2);
                    try {
                        $id = $driver->findElement(WebDriverBy::id("resultStats"))->getAttribute('innerHTML');
                        if(strpos('results', $id)){
                            $tmp = explode('About',$id);
                            $tmp = explode('results', $tmp[1]);
                        
                            $result = $tmp[0];
                            $result = str_replace('.', '', $result);
                        } else {
                            $tmp = explode('result', $id);
                        
                            $result = $tmp[0];
                            $result = str_replace(' ', '', $result);
                        }
                        print_r($result);
                        DB::table('domain_out')->where('domain_name',$value['domain'])->update(['statusWayback' => 1, 'total_index' => $result]);
                        //$this->updateKey($value['keyword_id'], $result);
                        unset($listKey[$key]);
                    } catch (\Exception $dd) {
                        DB::table('domain_out')->where('domain_name',$value['domain'])->update(['statusWayback' => 1, 'total_index' => 0]);
                        echo 'ko co';
                    }
                }
            } catch (\Exception $e) {
                //print_r($e);die;
                $urlAry = $driver->executeScript('return window.location',array());
                $currentURL = $urlAry['href'];
                echo $currentURL;
                if(strpos($currentURL, 'sorry/index') !== false){
                    $recaptchaToken = $this->recaptcha($currentURL);
                    if($recaptchaToken != false){
                        $driver->executeScript('document.getElementById("g-recaptcha-response").innerHTML = "'.$recaptchaToken.'"');
                        $driver->executeScript('document.getElementById("captcha-form").submit()');
                        sleep(10);
                        try {
                        	$id = $driver->findElement(WebDriverBy::id("resultStats"))->getAttribute('innerHTML');
                            if(strpos($id, 'results')){
                                $tmp = explode('About',$id);
                                $tmp = explode('results', $tmp[1]);
                            
                                $result = $tmp[0];
                                $result = str_replace('.', '', $result);
                            } else {
                                $tmp = explode('result', $id);
                            
                                $result = $tmp[0];
                                $result = str_replace(' ', '', $result);
                            }
                            $result = $tmp[0];
                            $result = str_replace('.', '', $result);
                            DB::table('domain_out')->where('domain_name',$listKey[0]['domain'])->update(['statusWayback' => 1, 'total_index' => $result]);
                            print_r($result.PHP_EOL);
                            unset($listKey[0]);
                            print_r($listKey);
                            foreach ($listKey as $key => &$value) {
                                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                                sleep(1);
                                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys('site:'.$value['domain']);
                                sleep(1);
                                $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                                sleep(2);
                                try {
                                    $id =$driver->findElement(WebDriverBy::id("resultStats"))->getAttribute('innerHTML');
                                    //print_r($id.PHP_EOL);
                                    if(strpos($id, 'About') !== false){
                                        $tmp = explode('About',$id);
                                        $tmp = explode('results', $tmp[1]);
                                        $result = $tmp[0];
                                        $result = str_replace('.', '', $result);
                                        $result = str_replace(',', '', $result);
                                        DB::table('domain_out')->where('domain_name',$value['domain'])->update(['statusWayback' => 1, 'total_index' => $result]);
                                        print_r($result.PHP_EOL);
                                        unset($listKey[$key]);
                                    } else {
                                        $tmp = explode('result', $id);
                                        $result = $tmp[0];
                                        $result = str_replace('.', '', $result);
                                        $result = str_replace(',', '', $result);
                                        DB::table('domain_out')->where('domain_name',$value['domain'])->update(['statusWayback' => 1, 'total_index' => $result]);
                                        print_r($result.PHP_EOL);
                                        unset($listKey[$key]);
                                    }
                                } catch (\Exception $e3) {
                                    $urlAry = $driver->executeScript('return window.location',array());
                                    $currentURL = $urlAry['href'];
                                    if($currentURL == 'https://www.google.com/sorry/index'){
                                        DB::table('domain_out')->where('domain_name',$value['domain'])->update(['statusWayback' => 1, 'total_index' => -2]);
                                    } else {
                                        DB::table('domain_out')->where('domain_name',$value['domain'])->update(['statusWayback' => 1, 'total_index' => -1]);
                                    }
                                    unset($listKey[$key]);
                                }
                                
                            }
                        } catch (\Exception $e1) {
                            $urlAry = $driver->executeScript('return window.location',array());
                            $currentURL = $urlAry['href'];
                            echo '2-'.$currentURL;
                            if($currentURL == 'https://www.google.com/sorry/index'){
                               DB::table('domain_out')->where('domain_name',$listKey[0]['domain'])->update(['statusWayback' => 1, 'total_index' => -2]);
                            } else {
                            	echo $listKey[0]['domain'];
                            	DB::table('domain_out')->where('domain_name',$listKey[0]['domain'])->update(['statusWayback' => 1, 'total_index' => -1]);
                            	unset($listKey[0]);
                                foreach ($listKey as $key => $value) {
                                	echo $value['domain'];
                                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                                    sleep(1);
                                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys('site:'.$value['domain']);
                                    sleep(1);
                                    $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                                    sleep(2);
                                    try {
                                        $id =$driver->findElement(WebDriverBy::id("resultStats"))->getAttribute('innerHTML');
                                        //print_r($id.PHP_EOL);
                                        if(strpos($id, 'About') !== false){
                                            $tmp = explode('About',$id);
                                            $tmp = explode('results', $tmp[1]);
                                            $result = $tmp[0];
                                            $result = str_replace('.', '', $result);
                                            $result = str_replace(',', '', $result);
                                            DB::table('domain_out')->where('domain_name',$value['domain'])->update(['statusWayback' => 1, 'total_index' => $result]);
                                            print_r($result.PHP_EOL);
                                            unset($listKey[$key]);
                                        } else {
                                            $tmp = explode('result', $id);
                                            $result = $tmp[0];
                                            $result = str_replace('.', '', $result);
                                            $result = str_replace(',', '', $result);
                                            DB::table('domain_out')->where('domain_name',$value['domain'])->update(['statusWayback' => 1, 'total_index' => $result]);
                                            print_r($result.PHP_EOL);
                                            unset($listKey[$key]);
                                        }
                                    } catch (\Exception $e3) {
                                        $urlAry = $driver->executeScript('return window.location',array());
                                        $currentURL = $urlAry['href'];
                                        if($currentURL == 'https://www.google.com/sorry/index'){
                                            DB::table('domain_out')->where('domain_name',$value['domain'])->update(['statusWayback' => 1, 'total_index' => -2]);
                                        } else {
                                            DB::table('domain_out')->where('domain_name',$value['domain'])->update(['statusWayback' => 1, 'total_index' => -1]);
                                        }
                                        unset($listKey[$key]);
                                    }
                                
                                }
                            }
                           
                        }
                    }
                } else {
                    DB::table('domain_out')->where('domain_name',$listKey[0]['domain'])->update(['statusWayback' => 1, 'total_index' => 0]);
                    unset($listKey[0]);
                    foreach ($listKey as $key => &$value) {
                        // if($key ==0){
                        //     continue;
                        // }
                        echo '1';
                        $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                        $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                        sleep(1);
                        $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys('site:'.$value['domain']);
                        sleep(1);
                        $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                        sleep(2);
                        try {
                            $id = $driver->findElement(WebDriverBy::id("resultStats"))->getAttribute('innerHTML');
                            if(strpos('results', $id)){
                                $tmp = explode('About',$id);
                                $tmp = explode('results', $tmp[1]);
                            
                                $result = $tmp[0];
                                $result = str_replace('.', '', $result);
                            } else {
                                $tmp = explode('result', $id);
                            
                                $result = $tmp[0];
                                $result = str_replace(' ', '', $result);
                            }
                            print_r($result);
                            DB::table('domain_out')->where('domain_name',$value['domain'])->update(['statusWayback' => 1, 'total_index' => $result]);
                            //$this->updateKey($value['keyword_id'], $result);
                            unset($listKey[$key]);
                        } catch (\Exception $dd) {
                            DB::table('domain_out')->where('domain_name',$value['domain'])->update(['statusWayback' => 1, 'total_index' => 0]);
                            echo 'ko co';
                        }
                    }
                }
            }
        } catch (\Exception $captcha) {
            
        }
        
        $driver->quit();
    }
    public function recaptcha($url){
        $api = new NoCaptchaProxyless();
        $api->setVerboseMode(true);
                
        //your anti-captcha.com account key
        $api->setKey($this->api_key);
         
        //recaptcha key from target website
        $api->setWebsiteURL($url);
        $api->setWebsiteKey($this->site_key);

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
            $recaptchaToken =   $api->getTaskSolution();
            //echo "\ntoken result: $recaptchaToken\n\n";
            return $recaptchaToken;
        }
    }
    public function updateKey($id, $search){
        $search = str_replace(',', '', $search);
        DB::table('keywords')->where('keyword_id',$id)->update(['allintitle' => $search,'status' => 2]);
    }
    public function insertKey(){
        $list = Excel::toArray(new ListCheckImport, public_path('key_25_9.csv'));
        foreach ($list[0] as $key => $item) {
        	echo $key;
        	if($key > 0){
	            $time = date('Y-m-d H:i:s',time());
	            //print_r($item);die;
	            if(DB::table('keywords')->where('keyword',$item[0])->count() == 0){
	                echo 1;
	            DB::table('keywords')->insert(['keyword' => $item[1],'difficult' => $item[7],'search_volumn' => (int)$item[5],'statusAhref' => 1,'status' => 1,'statusKw' => 1,'domain'=> 'keywd_25_9','created_at' => $time,'country' => 'us']);
	            }
        	}
  
        }
    }
    public function insertKeyword(){
        $list = scandir(storage_path('app/dw'));
        unset($list[0]);
        unset($list[1]);
        //print_r($list);

        foreach ($list as $file) {
            echo storage_path('app/dw/'.$file);
            $keywords = Excel::toArray(new ListCheckImport,storage_path('app/dw/'.$file));
            foreach ($keywords[0] as $key => $item) {
                if($key > 0){
                    $time = date('Y-m-d H:i:s',time());
                    //print_r($item);die;
                    try {
                        if(DB::table('keywords_domain')->where('url',$item[6])->count() == 0){
                            DB::table('keywords_domain')->insert(['keyword' => $item[1],'kd' => $item[7],'volume' => (int)$item[5],'url'=> $item[6],'created_at' => $time,'domain' => parse_url($item[6])['host']]);
                        }
                    } catch (\Exception $e) {
                        
                    }
                    
                    //die;
                }
            }
        }
        $list = scandir(storage_path('app/kw'));
        unset($list[0]);
        unset($list[1]);
        //print_r($list);

        foreach ($list as $file) {
            echo storage_path('app/kw/'.$file);
            $keywords = Excel::toArray(new ListCheckImport,storage_path('app/kw/'.$file));
            foreach ($keywords[0] as $key => $item) {
                if($key > 0){
                    $time = date('Y-m-d H:i:s',time());
                    //print_r($item);die;
                    try {
                        if(DB::table('keywords_domain')->where('url',$item[6])->count() == 0){
                            DB::table('keywords_domain')->insert(['keyword' => $item[1],'kd' => $item[7],'volume' => (int)$item[5],'url'=> $item[6],'created_at' => $time,'domain' => parse_url($item[6])['host']]);
                        }
                    } catch (\Exception $e) {
                        
                    }
                    
                    //die;
                }
            }
        }
    }
}
