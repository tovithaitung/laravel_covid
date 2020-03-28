<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\KeywordsImport;
use App\Imports\ListCheckImport;

use Maatwebsite\Excel\Facades\Excel;
require_once 'simple_html_dom.php';
use Elasticsearch\ClientBuilder;
use DB;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\Remote;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use App\Exports\Keywords;
use App\Http\Controllers\AhrefController;
use App\Http\Controllers\AllInTitleController;
ini_set('memory_limit',-1);
class AnswerPublicController extends Controller
{
    //
    public function readExcel(){
      $list = Excel::toArray(new ListCheckImport, public_path('apkvi.csv'),null, \Maatwebsite\Excel\Excel::CSV);
		  foreach ($list[0] as $key => $tmp) {
        if($key > 0){
            //$a = explode(",", $tmp[0]);
            //print_r($tmp);die;
          $time = date('Y-m-d H:i:s',time());
            DB::table('keywords')->insert(['keyword' => $tmp[1],'difficult' => $tmp[7],'search_volumn' => (int)$tmp[5],'statusAhref' => 1,'status' => 1,'statusKw' => 1,'domain'=> 'xda','created_at' => $time,'country' => 'us']);
        }
      }
    }
    public function importTopPage(){
      $files = scandir(public_path('page_sp'));
      unset($files[0]);
      unset($files[1]);

      foreach ($files as $file) {
        $tmp_file = explode('-',$file);
        $regex = "/:\/\/@(.*?)\//s";
        //print_r($tmp_file);
        preg_match($regex, $tmp_file[0], $matches);
        if(count($matches) > 1){
          $domain = $matches[1];
        } else {
          $domain = $tmp_file[0];
        }
        //print_r($domain);die;
        $list = Excel::toArray(new ListCheckImport, public_path('page_sp/'.$file),null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($list[0] as $key => $tmp) {
          if($key > 0){
              //$a = explode(",", $tmp[0]);
              //print_r($tmp);die;
            $time = date('Y-m-d H:i:s',time());
            DB::table('keywords')->insertOrIgnore(['keyword' => $tmp[6],'difficult' => -1,'search_volumn' => (int)$tmp[8],'statusAhref' => 1,'status' => 1,'statusKw' => 1,'domain'=> 'tovi_22_12','created_at' => $time,'country' => 'us','url' => $tmp[5]]);
            //die;
          }
        }
      }
      
    }
    public function getKeyWord(){
      /*$list = scandir(storage_path('app/apkmonk'));
      unset($list[0]);
      unset($list[1]);
      $list_appid = array();
      $list_extract = array();
      $list_key = array();
      $list_all = array();
      foreach ($list as $file) {
        $tmp_cn = explode('-',$file);
        $country = $tmp_cn[4];
        //echo $country;die;
        $keywords = Excel::toArray(new ListCheckImport, storage_path('app/apkmonk/'.$file),null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($keywords[0] as $key => $tmp) {
          if($key > 0){
            $url = $tmp[6];
            $url = str_replace('https://www.apkmonk.com/', '', $url);
            $tmp_url = explode('/', $url);
            if(count($tmp_url) >= 2){
              $appid = $tmp_url[count($tmp_url) - 2];
              
              if(strpos($appid, ' ')===false && strpos($appid, '.') && strpos($appid, '-') === false){
                if(in_array($appid, $list_appid) == false){
                  $list_appid[] = $appid;
                  $list_extract[] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  //$list_all[$appid][] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                  echo $appid.PHP_EOL;
                } else {
                  //$list_all[$appid][] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                }
                DB::table('key_cn')->insertOrIgnore(['keyword' => $tmp[1], 'country' => $country,'volume' => (int)$tmp[5]]);
                // if(in_array($tmp[1], $list_key) == false){
                //   $list_key[] = $tmp[1];
                // }
              }
            }
          }
        }
      }
      $list = scandir(storage_path('app/demo'));
      foreach ($list as $file) {
        $keywords = Excel::toArray(new ListCheckImport, storage_path('app/demo/'.$file),null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($keywords[0] as $key => $tmp) {
          if($key > 0){
            $url = $tmp[6];
            $url = str_replace('https://www.apkpure.com/', '', $url);
            $tmp_url = explode('/', $url);
            if(count($tmp_url) >= 2){
              $appid = $tmp_url[count($tmp_url) - 1];
              if(strpos($appid, ' ')===false && strpos($appid, '.') && strpos($appid, '-') === false){
                if(in_array($appid, $list_appid) == false){
                  $list_appid[] = $appid;
                  $list_extract[] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                  echo $appid.PHP_EOL;
                } else {
                  $list_all[$appid][] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                }
                // if(in_array($tmp[1], $list_key) == false){
                //   $list_key[] = $tmp[1];
                // }
                DB::table('key_cn')->insertOrIgnore(['keyword' => $tmp[1], 'country' => $country,'volume' => (int)$tmp[5]]);
              }
            }
          }
        }
      } 

      $list = scandir(storage_path('app/appapk'));
      foreach ($list as $file) {
        $keywords = Excel::toArray(new ListCheckImport, storage_path('app/appapk/'.$file),null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($keywords[0] as $key => $tmp) {
          if($key > 0){
            $url = $tmp[6];
            $url = str_replace('https://androidappsapk.co/', '', $url);
            $tmp_url = explode('/', $url);
            if(count($tmp_url) >= 2){
              $appid = $tmp_url[count($tmp_url) - 2];
              if(strpos($appid, ' ')===false && strpos($appid, '.') && strpos($appid, '-') === false){
                if(in_array($appid, $list_appid) == false){
                  $list_appid[] = $appid;
                  $list_extract[] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                  echo $appid.PHP_EOL;
                } else {
                  $list_all[$appid][] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                }
                // if(in_array($tmp[1], $list_key) == false){
                //   $list_key[] = $tmp[1];
                // }
                DB::table('key_cn')->insertOrIgnore(['keyword' => $tmp[1], 'country' => $country,'volume' => (int)$tmp[5]]);
              }
            }
          }
        }
      }
      $list = scandir(storage_path('app/apkplz'));
      foreach ($list as $file) {
        $keywords = Excel::toArray(new ListCheckImport, storage_path('app/apkplz/'.$file),null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($keywords[0] as $key => $tmp) {
          if($key > 0){
            $url = $tmp[6];
            $url = str_replace('https://apkplz.net/', '', $url);
            $tmp_url = explode('/', $url);
            if(count($tmp_url) >= 2){
              $appid = $tmp_url[count($tmp_url) - 1];
              if(strpos($appid, ' ')===false && strpos($appid, '.') && strpos($appid, '-') === false){
                if(in_array($appid, $list_appid) == false){
                  $list_appid[] = $appid;
                  $list_extract[] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                  echo $appid.PHP_EOL;
                } else {
                  $list_all[$appid][] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                }
                // if(in_array($tmp[1], $list_key) == false){
                //   $list_key[] = $tmp[1];
                // }
                DB::table('key_cn')->insertOrIgnore(['keyword' => $tmp[1], 'country' => $country,'volume' => (int)$tmp[5]]);
              }
            }
          }
        }
      }
      $list = scandir(storage_path('app/apksp'));
      foreach ($list as $file) {
        $keywords = Excel::toArray(new ListCheckImport, storage_path('app/apksp/'.$file),null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($keywords[0] as $key => $tmp) {
          if($key > 0){
            $url = $tmp[6];
            $url = str_replace('https://apk.support/', '', $url);
            $tmp_url = explode('/', $url);
            if(count($tmp_url) >= 2){
              $appid = $tmp_url[count($tmp_url) - 1];
              if(strpos($appid, ' ')===false && strpos($appid, '.') && strpos($appid, '-') === false){
                if(in_array($appid, $list_appid) == false){
                  $list_appid[] = $appid;
                  $list_extract[] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                  echo $appid.PHP_EOL;
                } else {
                  $list_all[$appid][] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                }
                // if(in_array($tmp[1], $list_key) == false){
                //   $list_key[] = $tmp[1];
                // }
                DB::table('key_cn')->insertOrIgnore(['keyword' => $tmp[1], 'country' => $country,'volume' => (int)$tmp[5]]);
              }
            }
          }
        }
      }
      $list = scandir(storage_path('app/apkcombo'));
      foreach ($list as $file) {
        $keywords = Excel::toArray(new ListCheckImport, storage_path('app/apkcombo/'.$file),null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($keywords[0] as $key => $tmp) {
          if($key > 0){
            $url = $tmp[6];
            $url = str_replace('https://apkcombo.com/', '', $url);
            $tmp_url = explode('/', $url);
            if(count($tmp_url) >= 2){
              $appid = $tmp_url[count($tmp_url) - 2];
              if(strpos($appid, ' ')===false && strpos($appid, '.') && strpos($appid, '-') === false){
                if(in_array($appid, $list_appid) == false){
                  $list_appid[] = $appid;
                  $list_extract[] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                  echo $appid.PHP_EOL;
                } else {
                  $list_all[$appid][] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                }
                // if(in_array($tmp[1], $list_key) == false){
                //   $list_key[] = $tmp[1];
                // }
                DB::table('key_cn')->insertOrIgnore(['keyword' => $tmp[1], 'country' => $country,'volume' => (int)$tmp[5]]);
              }
            }
          }
        }
      }
      $list = scandir(storage_path('app/apkdl'));
      foreach ($list as $file) {
        $keywords = Excel::toArray(new ListCheckImport, storage_path('app/apkdl/'.$file),null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($keywords[0] as $key => $tmp) {
          if($key > 0){
            $url = $tmp[6];
            $url = str_replace('https://apk-dl.com.com/', '', $url);
            $tmp_url = explode('/', $url);
            if(count($tmp_url) >= 2){
              $appid = $tmp_url[count($tmp_url) - 1];
              if(strpos($appid, ' ')===false && strpos($appid, '.') && strpos($appid, '-') === false){
                if(in_array($appid, $list_appid) == false){
                  $list_appid[] = $appid;
                  $list_extract[] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                  echo $appid.PHP_EOL;
                } else {
                  $list_all[$appid][] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                }
                // if(in_array($tmp[1], $list_key) == false){
                //   $list_key[] = $tmp[1];
                // }
                DB::table('key_cn')->insertOrIgnore(['keyword' => $tmp[1], 'country' => $country,'volume' => (int)$tmp[5]]);
              }
            }
          }
        }
      }
      $list = scandir(storage_path('app/apkfl'));
      foreach ($list as $file) {
        $keywords = Excel::toArray(new ListCheckImport, storage_path('app/apkfl/'.$file),null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($keywords[0] as $key => $tmp) {
          if($key > 0){
            $url = $tmp[6];
            $url = str_replace('https://www.apkfollow.com/', '', $url);
            $tmp_url = explode('/', $url);
            if(count($tmp_url) >= 2){
              $appid = $tmp_url[count($tmp_url) - 2];
              if(strpos($appid, ' ')===false && strpos($appid, '.') && strpos($appid, '-') === false){
                if(in_array($appid, $list_appid) == false){
                  $list_appid[] = $appid;
                  $list_extract[] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1], 'volume' => (int)$tmp[5]]);
                  echo $appid.PHP_EOL;
                } else {
                  $list_all[$appid][] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1], 'volume' => (int)$tmp[5]]);
                }
                // if(in_array($tmp[1], $list_key) == false){
                //   $list_key[] = $tmp[1];
                // }
                DB::table('key_cn')->insertOrIgnore(['keyword' => $tmp[1], 'country' => $country,'volume' => (int)$tmp[5]]);
              }
            }
          }
        }
      }
      $list = scandir(storage_path('app/apkgk'));
      foreach ($list as $file) {
        $keywords = Excel::toArray(new ListCheckImport, storage_path('app/apkgk/'.$file),null, \Maatwebsite\Excel\Excel::CSV);
        foreach ($keywords[0] as $key => $tmp) {
          if($key > 0){
            $url = $tmp[6];
            $url = str_replace('https://apkgk.com/', '', $url);
            $tmp_url = explode('/', $url);
            if(count($tmp_url) >= 2){
              $appid = $tmp_url[count($tmp_url) - 1];
              if(strpos($appid, ' ')===false && strpos($appid, '.') && strpos($appid, '-') === false){
                if(in_array($appid, $list_appid) == false){
                  $list_appid[] = $appid;
                  $list_extract[] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                  echo $appid.PHP_EOL;
                } else {
                  $list_all[$appid][] = array($tmp[1], $appid, (int)$tmp[5], 'apkmonk.com');
                  DB::table('key_app')->insertOrIgnore(['appid' => $appid, 'keyword' => $tmp[1],'volume' => (int)$tmp[5]]);
                }
                // if(in_array($tmp[1], $list_key) == false){
                //   $list_key[] = $tmp[1];
                // }
                DB::table('key_cn')->insertOrIgnore(['keyword' => $tmp[1], 'country' => $country,'volume' => (int)$tmp[5]]);
              }
            }
          }
        }
      } 
      */
      // $api = new AnswerPublicController();
      // $allintitle = new AllInTitleControllerl();
      // $ahref = new AhrefController($api, $allintitle);
      // foreach ($list_all as $key => $detail) {
      //   foreach ($detail as $keyword) {
      //     $info = $ahref->checkKeyword($keyword[0]);
      //     print_r($info);
      //   }
      // }
      $list_extract = array();
      //$listKey = DB::select(DB::raw('SELECT key_app.appid, key_all.keyword, key_all.volume FROM key_app, (SELECT key_cn.keyword, sum(key_cn.volume) as volume FROM key_cn group by keyword) as key_all where key_app.keyword = key_all.keyword'));
      $listKey = DB::select(DB::raw('SELECT key_app.appid, sum(key_all.volume) as total FROM key_app, (SELECT key_cn.keyword, sum(key_cn.volume) as volume FROM key_cn group by keyword) as key_all where key_app.keyword = key_all.keyword group by key_app.appid'));
      $export = new Keywords($listKey);
      return Excel::store($export,'public/new_key.xlsx');
    }
    public function getApp(){
        $list = DB::table('keywords')->where('appid','!=','')->where('kwplaner','>=','100')->get();
        foreach ($list as $item) {
          $url = "http://dl.tovicorp.com/info?appid=".$item->appid;
          echo $url;
          $res = $this->requestUser($url,'test.txt');
          $data = json_decode($res['html'],true);
          DB::table('keywords')->where('keyword_id',$item->keyword_id)->update(['installs' => $data['installs'],'domain' => -1]);
          //die;

        }
        //print_r(count($list));
    }
    public function keyword(){
      $apps = $this->topGrossing();
      //print_r($apps['data']);
      $listApp = array();
      foreach ($apps['data'] as $app) {
        //print_r($app);
        if(isset($app['title']) && (strpos($app['title'], 'Download') == false || strpos($app['title'], 'download') == false)){
          $title = preg_replace("/[^A-Za-z0-9 ]/", '', $app['title']);

         // $title = $app['title'];
          $title = trim(preg_replace('/\s+/', ' ', $title));
          if(!empty($title)){
            $listApp[] = array('title' => $title." apk",'appid' => $app['appid']);
          }
        }
      }
      foreach ($listApp as $key => $app) {
        if($app['title'] == ' apk'){
          unset($listApp[$key]);
        }
      }
      foreach ($listApp as $app) {
        $created_at = date('Y-m-d H:i:s',time());
        DB::table('keywords')->insert(['keyword' => $app['title'],'appid' => $app['appid'],'status' => 0,'created_at' => $created_at,'domain' => 'apkvi']);
      }
      //print_r($listApp);
      //print_r(count($listApp));die;
      //$list = $this->report($listApp);
      //  $list = $this->requestReport();die;
        //print_r($app);
        //$list = $this->requestReport("Download ".$app['title']." apk");
        //print_r($app);die;
        /*if(!empty($app['title'])){

          $data = array('keyword' => "Download ".$app['title']." apk", 'appid' => $app['appid']);
          DB::table('keywords')->insert($data);
        }*/
       
        //print_r($list);die;
        /*foreach ($list as $p) {
          $data = array('keyword' => $p[3])
          DB::table('keywords')->insert($data);
        }*/
      // $list = $this->requestReport("Download Tiktok apk");
      // foreach ($list as $p) {
      //   $data = array('keyword' => $p[2],'appid' => 'com.ss.android.ugc.trill');
      //   DB::table('keywords')->insert($data);
      // }
    }
    public function topGrossing(){
      $size = 10100;
      $from = 0;
      $client = ClientBuilder::create()->setSSLVerification(false)
        ->setHosts(["88.198.47.206:9200"])->build();
      $params['index'] = 'apktot';
      $params['type'] = 'apktot';
      $params['body'] = [
        'size' => $size,
        'from' => $from,
        'sort' => [ 'installs' => ['order' => 'DESC']],
        'query' => [
          'bool' => [
            'filter' => [
              'match' => [
                'is_file' => 1
              ]
            ],
            'must' => [
              ['range' => [
                'installs' => [
                  'gte' => 100000
                ]
              ]],
              ['range' => [
                'releasedate' => [
                  'gte' => time() - 86400*30*9
                ]
              ]],
            ]
          ]
        ],
        '_source' => ['title','appid','tag']
      ];
      //print_r($params);die;
      $res = $client->search($params);
      //print_r($res);die;
      $a = array();
      foreach ($res['hits']['hits'] as $key => $app) {
        unset($app['_source']['@version']);
        unset($app['_source']['@timestamp']);
        $a[] = $app['_source'];
      }
      $result = array('total' => $res['hits']['total'],'data' => $a);
      return $result;
    }
    public function requestReport($keyword){
      //$this->login();
      print_r($keyword);
      $url = 'https://answerthepublic.com/pbkhzq/reports/new';
      $file = public_path('cookie.txt');
      $response = $this->requestUser($url, $file);
      print_r($response);
      $html = $response['html'];
      $dom = str_get_html($html);
      $form = $dom->find('.show-for-medium form',0);
      if(!$form){
        $this->login();
        $response = $this->requestUser($url, $file);
        //print_r($response);
        $html = $response['html'];
        $dom = str_get_html($html);
        $form = $dom->find('.show-for-medium form',0);
      }
      $charset = $form->find('input[name=utf8]',0)->value;
      $authenticity_token = $form->find('input[name=authenticity_token]',0)->value;

      $body = 'utf8='.$charset.'&authenticity_token='.$authenticity_token.'&'.urlencode('report[lang]').'=en&'.urlencode('report[region]').'=US&'.urlencode('report[keyword]').'='.urlencode($keyword).'&button=';
      $url = 'https://answerthepublic.com/pbkhzq/reports';
      print_r($body.PHP_EOL);
      $html = $this->postRequest($url, $body, $file);
      if(strpos($html, '<h1>The change you wanted was rejected.</h1>') !== false){
        echo 'fail';die;
        //$html = $this->postRequest($url, $body, $file);
      }
      //print_r($html);
      
      $href = str_replace("<html><body>You are being <a href=\"", "", $html);
      $href = str_replace("\">redirected</a>.</body></html>", "", $href);
      print_r($href);
      $content = $this->requestUser($href, $file, false);
      sleep(60);
      $file_name = str_replace('https://answerthepublic.com/reports/', '',$href);
      //print_r($content);
      $file = file_get_contents($href.'.csv');
      file_put_contents(public_path($file_name.'.csv'), $file);
      //Excel::import(new KeywordsImport, public_path($file_name.'.csv'));]
      $list = Excel::toArray(new ListCheckImport, public_path($file_name.'.csv'));
      return $list;
    }
    public function extractFile($path){
      $list = Excel::toArray(new ListCheckImport, $path);
      return $list;
    }
    public function login(){
      $url = 'https://answerthepublic.com/users/sign_in';
      $file = public_path('cookie.txt');
      $response = $this->requestUser($url, $file);
      $html = $response['html'];
      $dom = str_get_html($html);
      $login_form = $dom->find('form.new_user',0);
      //print_r($html);
      $charset = $login_form->find('input[name=utf8]',0)->value;
      $authenticity_token = $login_form->find('input[name=authenticity_token]',0)->value;
      $body = 'utf8='.$charset.'&authenticity_token='.$authenticity_token.'&user[email]='.urlencode('tovicorp.com@gmail.com').'&user[password]=MKwb0MN0&user[remember_me]=0&commit='.urlencode("Sign in");
      //print_r($body);
      $html = $this->postRequest($url, $body, $file, false);
      print_r($html);
     
    }
    public function runReport(){
      $string = "hungry shark evolution";

      $keywords = explode(PHP_EOL, $string);
      print_r($keywords);
      $this->report($keywords);
    }
    public function report($keywords){
      $host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();
        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $options->setExperimentalOption("prefs", $prefs);
        $caps->setCapability(ChromeOptions::CAPABILITY, $options);
        if ($USE_FIREFOX)
        {
            $driver = RemoteWebDriver::create(
                $host, 
                DesiredCapabilities::firefox()
            );
        }
        else
        {
            $driver = RemoteWebDriver::create(
                $host, 
                $caps
            );
        }
        $driver->get("https://answerthepublic.com/users/sign_in");

        # enter text into the search field
        $driver->findElement(WebDriverBy::id('user_email'))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::id('user_email'))->sendKeys('tovicorp.com@gmail.com');
        sleep(1);
        $driver->findElement(WebDriverBy::id('user_password'))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::id('user_password'))->sendKeys('MKwb0MN0');
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('div.actions .brand-primary'))->click();
        sleep(2);
        $key = $keywords[0];
        //try {
          $selectElement = $driver->findElement(WebDriverBy::id('region'));

          // Now pass it to WebDriverSelect constructor
          $select = new WebDriverSelect($selectElement);
          $select->selectByValue('US');
          sleep(1);
          $driver->findElement(WebDriverBy::id('report_keyword'))->click();
          sleep(1);
          $driver->findElement(WebDriverBy::id('report_keyword'))->sendKeys($key);
          sleep(1);
          $driver->findElement(WebDriverBy::cssSelector('div.actions .inline-submit-button'))->click();
          sleep(10);
          unset($keywords[0]);
          $urlAry = $driver->executeScript('return window.location',array());
          $currentURL = $urlAry['href'];
          $listUrl[] = $currentURL;
          $file = file_get_contents($currentURL.'.csv');
          $file_name = str_replace('https://answerthepublic.com/reports/', '', $currentURL);
          file_put_contents(public_path($file_name.'.csv'), $file);
          $list = $this->extractFile(public_path($file_name.'.csv'));
          //print_r($currentURL);die;
          foreach ($list[0] as $key) {
            if(!empty($key[2]) && $key[2] != 'Suggestion'){
              $created_at = date('Y-m-d H:i:s',time());
              print_r($key[2]);
              DB::table('keywords')->insert(['keyword' => $key[2],'status' => 0,'created_at' => $created_at,'domain' => 'antovi']);
            }
          }
          $listUrl = array();
          foreach ($keywords as $keyword) {
            // if($key == 0){
            //   continue;
            // }
            $driver->get('https://answerthepublic.com/?seeker=false');
            $selectElement = $driver->findElement(WebDriverBy::id('region'));

            // Now pass it to WebDriverSelect constructor
            $select = new WebDriverSelect($selectElement);
            $select->selectByValue('US');
            sleep(1);
            $driver->findElement(WebDriverBy::id('report_keyword'))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::id('report_keyword'))->sendKeys($keyword);
            sleep(1);
            $driver->findElement(WebDriverBy::cssSelector('section.show-for-medium .brand-primary-gradient'))->click();
            sleep(10);
            $urlAry = $driver->executeScript('return window.location',array());
            $currentURL = $urlAry['href'];
            $listUrl[] = $currentURL;
            sleep(1);
            $file = file_get_contents($currentURL.'.csv');
            $file_name = str_replace('https://answerthepublic.com/reports/', '', $currentURL);
            file_put_contents(public_path($file_name.'.csv'), $file);
            $list = $this->extractFile(public_path($file_name.'.csv'));
            //print_r($currentURL);die;
            foreach ($list[0] as $key) {
              if(!empty($key[2]) && $key[2] != 'Suggestion'){
                $created_at = date('Y-m-d H:i:s',time());
                print_r($key[2]);
                DB::table('keywords')->insert(['keyword' => $key[2],'status' => 0,'created_at' => $created_at,'domain' => 'antovi']);
              }
            }
          }
          //print_r($listUrl);
          //sleep(30);
          /*foreach ($listUrl as $url) {
            $file = file_get_contents($url.'.csv');
            $file_name = str_replace('https://answerthepublic.com/reports/', '', $url);
            file_put_contents(public_path($file_name.'.csv'), $file);
            $list = $this->extractFile(public_path($file_name.'.csv'));
            //print_r($list);die;
            foreach ($list as $key) {
              $created_at = date('Y-m-d H:i:s',time());
              DB::table('keywords')->insert(['kewyord' => $key[2],'status' => 0,'created_at' => $created_at,'domain' => 'antovi']);
            }
          }*/
        // } catch (\Exception $e) {
        //   print_r($e);
        // }
        //$driver->quit();
    }
    public function requestUser($url, $file, $xmlRequest = false, $token = 0){
      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 300,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_COOKIEJAR => $file,
        CURLOPT_COOKIEFILE => $file,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HEADER => 0,
        
        // CURLOPT_PROXYTYPE => 7,
        // CURLOPT_PROXY => "159.69.2.57",
        // CURLOPT_PROXYPORT => "28982"
      ));
      if($xmlRequest !== false){
        if($token == 0){
          curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest"));
        } else {
          curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest",'x-csrf-token:'.$token));
        }

      }
      $response = curl_exec($curl);
      $err = curl_error($curl);
      if ($err) {
        echo "cURL Error #:" . $err;
        return -1;
      }
      curl_close($curl);
      $res = array('html' => $response);
      return $res;
    }
    public function postRequest($url, $body, $file, $xmlRequest = false, $referer = "", $token = ""){
      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 300,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_COOKIEJAR => $file,
        CURLOPT_COOKIEFILE => $file,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_FOLLOWLOCATION => true,
        
/*        CURLOPT_PROXYTYPE => 7,
        CURLOPT_PROXY => "159.69.2.57",
        CURLOPT_PROXYPORT => "28982"*/


      ));
      if($xmlRequest !== false){
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest","content-length: ".strlen($body),
"content-type: application/x-www-form-urlencoded; charset=UTF-8","referer: ".$referer,'x-csrf-token:'.$token));
      }
      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);
      if ($err) {
        echo "cURL Error #:" . $err;
        return -1;
      }
      return $response;
    }
}
