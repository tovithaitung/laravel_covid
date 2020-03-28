<?php
// top app for apkvi
namespace App\Http\Controllers;

require_once './app/helpers.php';

use Illuminate\Http\Request;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use App\App;
use App\AppRelate;
use Elasticsearch\ClientBuilder;
use App\Exports\Keywords;
use Maatwebsite\Excel\Facades\Excel;
use DB;

use App\Imports\ListCheckImport;
ini_set('memory_limit',-1);
require_once 'simple_html_dom.php';
class AppController extends Controller
{
    //
    private $client;
    private $name_index = 'apktot';
    function __construct()
    {

        $this->client = ClientBuilder::create()->setSSLVerification(false)->setHosts(["88.198.47.206:9200"])->build();
    }
    private $token_api = 'eAv8Oo04cyUKSiPWf1OZgUmAYTFyd5vznewPBn03MiTCvTw9Ca56gfTqE8OV';
    public function getTopApp(){
    	$url = 'https://domain.tovicorp.com/api/topapp?api_token='.$this->token_api;
    	$res = httprequest($url, 'GET');
    	if($res['status'] == 1){
    		$apps = json_decode($res['msg'],true);
    		foreach ($apps as $app) {
    			$data = array('appid' => $app['appid'],'title' => $app['keyword'],'kwplanner' => $app['kwplaner'], 'installs' => $app['installs'], 'releaseDate' => $app['releaseDate'],'created_at' => date('Y-m-d H:i:s',time()));
    			//print_r($data);
    			App::firstOrCreate(['appid' => $app['appid']],$data);
    		}
    	}
    }
    public function relatePlanner(){
    	#Config browsers

    	$mail = 'tovicorp.com@gmail.com';
        $mailPass = 'MKwb0MN0@';
        $host = 'http://192.168.1.3:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();
        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $prefs = array('download.default_directory' => 'c:/temp');
        $options->setExperimentalOption("prefs", $prefs);
        //$ss = array('--headless','--start-maximized');
        //$options->addArguments($ss);
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
        #Access link Google Ads
        $driver->get("https://ads.google.com/intl/vi_vn/home/");
        sleep(2);

        #Click button sign-in
        $driver->findElement(WebDriverBy::cssSelector(
            'a[href="https://ads.google.com/um/signin?subid=ALL-vi-et-g-aw-a-home-awhp_xin1_signin!o2"]'))
            ->click();
        sleep(1);
        #Fill email
        $driver->findElement(WebDriverBy::id('identifierId'))->sendKeys($mail);
        $driver->findElement(WebDriverBy::className('CwaK9'))->click();
        sleep(1);
        #Fill password
        $driver->findElement(WebDriverBy::cssSelector('input[type="password"]'))->sendKeys($mailPass);
        $driver->findElement(WebDriverBy::className('CwaK9'))->click();
        sleep(3);

        #Select account Ads
        $elements = $driver->findElements(WebDriverBy::cssSelector('a.umx-l'));
        foreach ($elements as $element) {
            if ($element->getAttribute('href') == "https://ads.google.com/um/identity?authuser=2&dst=/um/homepage?__e%3D6119414799") {
                $element->click();
                break;
            }
        }
        sleep(5);

        #Access link planner Ads
        $driver->get('https://ads.google.com/aw/keywordplanner/home?ocid=337734716&__u=6391395840&__c=8217153884&authuser=2');
        sleep(5);
        // try {
        //     $driver->findElement(WebDriverBy::cssSelector('div.particle-table-row'))->click();
        //     sleep(1);
        // } catch (\Exception $e) {
        //     sleep(10);
        //     $driver->findElement(WebDriverBy::cssSelector('div.particle-table-row'))->click();
        // }
        sleep(2);
        $driver->findElement(WebDriverBy::cssSelector('div.card-frame'))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->sendKeys('demo');
            sleep(0.5);
        #Click enter on keyboard to search
        $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->sendKeys(WebDriverKeys::ENTER);
        sleep(2);
        #Click button 'search' to redirect page resutl
        $driver->findElement(
            WebDriverBy::cssSelector('div.get-results-button-container > material-button.get-results-button')
        )->click();
        sleep(6);

        #Remove localtion in Viet Nam
        $driver->findElement(WebDriverBy::cssSelector('div.settings-bar > labeled-value'))
            ->click();
        sleep(2);
        $driver->findElement(WebDriverBy::cssSelector('td.remove'))->click();
        sleep(30);
        try {
            $driver->findElement(WebDriverBy::cssSelector('material-button.highlighted'))->click();
            sleep(3);
        } catch (\Exception $e1) {
            $driver->findElement(WebDriverBy::cssSelector('material-button.highlighted'))->click();
            sleep(3);
        }
        

        #Select language English
        $driver->findElement(WebDriverBy::cssSelector('language-selector'))->click();
        sleep(2);
        $driver->findElement(WebDriverBy::cssSelector('input[aria-label="Tìm kiếm ngôn ngữ"]'))
            ->sendKeys('Anh');
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('input[aria-label="Tìm kiếm ngôn ngữ"]'))
            ->sendKeys(WebDriverKeys::ENTER);
        sleep(3);
        $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
        sleep(2);
        $list = App::where('status',1)->get();
        //$loop = count(DB::select(DB::raw("SELECT * FROM `keywords` WHERE `status` = 0 AND `domain` LIKE 'apkvi' and kwplaner is null")));
        foreach ($list as $tmp) {
            //$listContinue = DB::select(DB::raw("SELECT * FROM `keywords` WHERE `status` = 0 AND `domain` LIKE 'apkvi' and kwplaner is null  order by keyword_id desc LIMIT 9 OFFSET ".$j*9));
            $listContinue = json_decode($tmp->relate,true);
            $res = array();
            foreach ($listContinue as $key => $p) {
                $listKey[] = $p;
                if($key < count($listContinue) - 1){
                    $p = str_replace(',', '', $p);
                    $p = $p.',';
                }
                $res[] = $p;
            }

            foreach ($res as $value){
                $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))->sendKeys($value);
                sleep(0.5);
            }

            #Click enter on keyboard to search
            //$driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))
            $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))->sendKeys(WebDriverKeys::ENTER);
            sleep(2);
            try {
                $driver->findElement(WebDriverBy::cssSelector('.get-results-button'))
                ->click();
                sleep(5);
                $this->saveKeywordsSearch($driver, $listContinue, $tmp->app_top_id);
                try {
                    $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
                    sleep(2);
                    for ($i=1; $i <= count($res); $i++) {
                        try {
                            $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))
                            ->sendKeys(WebDriverKeys::BACKSPACE);
                            sleep(1.5);
                        } catch (\Exception $e) {
                            
                        }
                        
                    }
                    sleep(3);
                } catch (\Exception $e) {
                    
                }
            } catch (\Exception $e) {
                try {
                    $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
                    sleep(2);
                    for ($i=1; $i <= count($res); $i++) {
                        try {
                            $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))
                            ->sendKeys(WebDriverKeys::BACKSPACE);
                            sleep(1.5);
                        } catch (\Exception $e) {
                            
                        }
                        
                    }
                    sleep(3);
                } catch (\Exception $e) {
                    
                }
            }
            
            //die;

        }
        /*$mail = "apkvi.com";
>>>>>>> 3aa283252a1aec41306d7f6036f9cac448782a10
        $pass = "apkvi12#";
        //$title = "How to install " . $app_name .' APK';
        $desc = "";
        $tags = "";

        $host = 'http://192.168.1.3:4444/wd/hub';
        $USE_FIREFOX = false; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();
        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $prefs = array('download.default_directory' => 'c:/temp');
        $options->setExperimentalOption("prefs", $prefs);
        //$ss = array('--headless','--start-maximized');
        //$options->addArguments($ss);
        $caps->setCapability(ChromeOptions::CAPABILITY, $options);

        $driver = RemoteWebDriver::create($host, $caps, 5000);

        $driver->get("https://www.youtube.com/upload");
        sleep(2);

        $driver->findElement(WebDriverBy::id('identifierId'))->sendKeys($mail);
        sleep(1);
        $driver->findElement((WebDriverBy::id('identifierNext')))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::id('password'))->findElement(WebDriverBy::cssSelector('input'))->sendKeys($pass);
        sleep(1);
        $driver->findElement((WebDriverBy::id('passwordNext')))->click();
        sleep(4);
        $upload = $driver->findElement((WebDriverBy::id('upload-prompt-box')))->findElement(WebDriverBy::cssSelector('input'));
        sleep(1);
        $upload->setFileDetector(new LocalFileDetector());
        $upload->sendKeys($url_video);

        sleep(10);
        $driver->findElement(WebDriverBy::className('video-settings-title'))->clear()->sendKeys($title);
        sleep(1);
        $driver->findElement(WebDriverBy::className('video-settings-description'))->sendKeys($desc);
        sleep(1);
        $driver->findElement(WebDriverBy::className('video-settings-add-tag'))->sendKeys($tags);
        sleep(120);
        $thumb_upload =  $driver->findElement(WebDriverBy::className('custom-thumb-container'))->findElement(WebDriverBy::cssSelector('input'));
        sleep(1);
        $thumb_upload->setFileDetector(new LocalFileDetector());
        $thumb_upload->sendKeys($url_image);
        var_dump("Video uploaded");
        $driver->findElement(WebDriverBy::className('save-changes-button'))->click();
        sleep(1);


        $driver->close();*/

    }
    private function saveKeywordsSearch($driver, $keywords, $id = ""){
    	$result = array();
        foreach ($keywords as $key => $p) {
            $keywordsPlanner = array();
            $i = $key + 1;
            echo $i.PHP_EOL;
            try {
               //$htmlKeyword = $driver->findElement(WebDriverBy::xpath('//div[@class="particle-table-row"]['.$i.']/ess-cell[1]'))->getAttribute('innerHTML');
                if(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath('//div[@class="particle-table-row"]['.$i.']/ess-cell'))){
                    $htmlKeyword = $driver->findElement(WebDriverBy::xpath('//div[@class="particle-table-row"]['.$i.']/ess-cell'))->getAttribute('innerHTML');
                    //echo $htmlKeyword;
                    //Get min search per month
                    $htmlMinSearch = $driver->findElement(WebDriverBy::xpath('//div[@class="particle-table-row"]['.$i.']/ess-cell[2]'))->getAttribute('innerHTML');
                    echo $htmlMinSearch.PHP_EOL;
                    $domKeyword = str_get_html($htmlKeyword);
                    $domMinSearch = str_get_html($htmlMinSearch);
                    $keyword = $domKeyword->find('span',0)->plaintext;
                    $minSearch = $domMinSearch->find('span.value-text',0)->plaintext;
                    if(strpos($minSearch, '–') !== false){
                        $strMin = explode("–", $minSearch);
                        //$strMin[0] = str_replace('&nbsp;', '', $strMin[0]);
                        $volumn = preg_replace("/[^0-9]/", '', $strMin[0]);
                        if(strpos($strMin[0], 'N')){
                            (int)$volumn = (int)$volumn*1000;
                        }
                        //if(strpos($volumn, ''))
                    } else {
                        $volumn = str_replace('.', '', $minSearch);
                    }
                    echo "kw:".$keyword.PHP_EOL;
                    //$volumn = str_replace('.', '', $minSearch);
                    echo "volumn:".$volumn.PHP_EOL;
                    if($volumn == ''){
                        $volumn = 0;
                    }
                    if($volumn == '—'){
                        $volumn = 0;
                    }
                    if(!is_numeric($volumn)){
                        $volumn = 0;
                    }
                    $keywordsPlanner['kwplaner'] = (int)$volumn;
                    $keywordsPlanner['keyword'] = $keyword;
                    //$keywordsPlanner['created_at'] = date('Y-m-d H:i:s',time());
                    $result[] = $keywordsPlanner;
                }
                sleep(0.5); 
            } catch (\Exception $e) {
                try {
                    $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
                    sleep(2);
                    for ($i=1; $i <= count($keywords); $i++) {
                        $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))
                            ->sendKeys(WebDriverKeys::BACKSPACE);
                            sleep(1.5);
                    }
                } catch (\Exception $e) {
                    
                }
                
            }
            
        }
        //print_r($result);
        App::where('app_top_id',$id)->update(['relate_planner'=>json_encode($result),'status' => 2]);
    }
    private function updateRelateApp($driver, $keywords){
        $result = array();
        foreach ($keywords as $key => $tmp) {
            $keywordsPlanner = array();
            $i = $key + 1;
            try {
                $driver->wait(10, 300)->until(
                  WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath('//div[@class="particle-table-row"]['.$i.']/ess-cell'))
                );
                if(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath('//div[@class="particle-table-row"]['.$i.']/ess-cell'))){
                    $htmlKeyword = $driver->findElement(WebDriverBy::xpath('//div[@class="particle-table-row"]['.$i.']/ess-cell'))->getAttribute('innerHTML');
                    //echo $htmlKeyword;
                    //Get min search per month
                    $htmlMinSearch = $driver->findElement(WebDriverBy::xpath('//div[@class="particle-table-row"]['.$i.']/ess-cell[2]'))->getAttribute('innerHTML');
                    //echo $htmlMinSearch.PHP_EOL;
                    $domKeyword = str_get_html($htmlKeyword);
                    $domMinSearch = str_get_html($htmlMinSearch);
                    $keyword = $domKeyword->find('span',0)->plaintext;
                    //$minSearch = $domMinSearch->find('span.value-text',0)->plaintext;
                    $minSearch = $domMinSearch->find('span',0)->plaintext;
                    echo $minSearch;
                    if(strpos($minSearch, '–') !== false){
                        $strMin = explode("–", $minSearch);
                        //$strMin[0] = str_replace('&nbsp;', '', $strMin[0]);
                        $volumn = preg_replace("/[^0-9]/", '', $strMin[0]);
                        if(strpos($strMin[0], 'N')){
                            (int)$volumn = (int)$volumn*1000;
                        }
                        //if(strpos($volumn, ''))
                    } else {
                        $volumn = str_replace('.', '', $minSearch);
                    }
                    echo "kw:".$keyword.PHP_EOL;
                    //$volumn = str_replace('.', '', $minSearch);
                    echo "volumn:".$volumn.PHP_EOL;
                    if($volumn == ''){
                        $volumn = 0;
                    }
                    if($volumn == '—'){
                        $volumn = 0;
                    }
                    if(!is_numeric($volumn)){
                        $volumn = 0;
                    }
                    $keywordsPlanner['kwplaner'] = (int)$volumn;
                    $keywordsPlanner['keyword'] = $keyword;
                    //echo $p->app_relate_id.PHP_EOL;
                    //print_r($keywordsPlanner);
                   // echo 'ID:'.$tmp->app_relate_id.PHP_EOL;
                    //AppRelate::where('appid',$tmp->appid)->update(['kwplanner'=>(int)$volumn,'keywords' => $keyword,'status' => 50]);
                    $check_tmp = false;
                    foreach ($keywords as $tmp1) {
                        similar_text($keyword, $tmp1->keyword, $percent);
                        if($percent >= 80){
                            DB::table('keywords')->where('keyword',$keyword)->where('domain','tovi_22_12')->update(['kwplaner' => (int)$volumn, 'statusKw' => 2]);
                            $check_tmp = true;
                        }
                    }
                    //similar_text($keyword, $tmp->keyword, $percent);
                    if($check_tmp == false){
                        DB::table('keywords')->where('keyword',$keyword)->where('domain','tovi_22_12')->update(['kwplaner' => (int)$volumn]);
                        DB::table('keywords')->where('keyword',$tmp->keyword)->where('domain','tovi_22_12')->update(['statusKw' => 2]);
                    }
                    //DB::table('keywords')->where('keyword',$keyword)->update(['kwplaner' => (int)$volumn]);
                    //DB::table('keywords')->where('keyword',$tmp->keyword)->update(['statusKw' => 2]);

                }
                //sleep(5); 
            } catch (\Exception $e7) {
                //print_r($e7->getMessage());
                echo 'error';
                try {
                    $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
                    sleep(2);
                    for ($i=1; $i <= count($keywords); $i++) {
                        $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))
                            ->sendKeys(WebDriverKeys::BACKSPACE);

                            sleep(1.5);
                    }
                } catch (\Exception $e) {
                    
                }
                
            }
            
        }
    }
    public function getRelateApp(){
        $listApps = App::where('statusRelate',0)->get();
        foreach ($listApps as $app) {

            $detail = $this->detailApp($app->appid);

            $title = strtolower($detail['_source']['title']);
            $result_search = $this->searchFullText($title);
            foreach ($result_search as $result) {
                if($result['appid'] != $app->appid){
                    $data = array('appid' => $result['appid'], 'title' => $result['title'], 'installs' => $result['installs'], 'releaseDate' => $result['releasedate']);
                    AppRelate::firstOrCreate(['appid' => $result['appid']],$data);
                }
            }
        }
    }
    public function detailApp($id){
        $params['index'] = 'apktot_1000';
        $params['type'] = 'apktot_1000';
        $params['id'] = $id;
        $params['client'] = ['ignore' => 404];

        $app = $this->client->get($params);
        return $app;
    }
    public function detailAppFull($id){
        $params['index'] = 'apktot';
        $params['type'] = 'apktot';
        $params['id'] = $id;
        $params['client'] = ['ignore' => 404];

        $app = $this->client->get($params);
        return $app;
    }
    public function searchFullText($keyword)
    {
        $where = [
          'index' => $this->name_index, 
          'type' => $this->name_index,
          'body' => [
            "from" => 0, "size" => 3,
            'sort' => [
            [ "installs" => ["order" =>"desc"]],
            "_score"
            ],
            'query' =>  [
              'bool' => [
                'should' => [
                  ['bool' => [
                    'filter' => [
                      'match' => [
                        'is_file' => 1
                      ]
                    ],
                    'must' => [
                      ['range' => [
                        'installs' => [
                          'gte' => 50000
                        ]
                      ]],
                      [
                        'multi_match' => [
                        "query"  =>   '*'.$keyword.'*',
                        "type"=>"phrase_prefix", 
                        "fields" => ["title"],
                        "tie_breaker"=> 0.2,
                        "minimum_should_match" => "10%",
                        "analyzer"=> "standard",
                        ],
                      ]
                    ]
                  ]],
                  ['bool' => [
                    'filter' => [
                      'match' => [
                        'is_file' => 1
                      ]
                    ],
                    'must' => [
                      ['range' => [
                        'installs' => [
                          'lt' => 50000
                        ]
                      ]],
                      ['range' => [
                          'weekly_rating' => [
                            'gte' => 2
                          ]
                      ]],
                      [
                        'multi_match' => [
                        "query"  =>   '*'.$keyword.'*',
                        "type"=>"phrase_prefix", 
                        "fields" => ["title"],
                        "tie_breaker"=> 0.2,
                        "minimum_should_match" => "10%",
                        "analyzer"=> "standard",
                        ],
                      ]
                    ]
                  ]],
                ]
              ]
            ]
          ]
        ];
        $res = $this->client->search($where);
        $result = array();
        foreach ($res['hits']['hits'] as $item) {
          
          // if($item['_source']['obb_size'] > 0){
          //   $item['_source']['urltitle'] = $item['_source']['urltitle'].'-apk-and-obb-data-download';
          // } else {
          //   $item['_source']['urltitle'] = $item['_source']['urltitle'].'-apk-download';
          // }
          // $tmp = array('appid' => $item['_source']['appid'],'title' => $item['_source']['title'],'offerby' => $item['_source']['offerby'],'cover' => $item['_source']['cover'],'dev_id' => $item['_source']['dev_id'],'urltitle' => $item['_source']['urltitle'],'score' => $item['_source']['score']);
          $result[] = $item['_source'];
        }
        return $result;
    }
    public function updateAppShow($appid, $status = 1001, $parent = ''){
        $params['index'] = 'apktot';
        $params['type'] = 'apktot';
        $params['id'] = $appid;
        $params['body']['doc']['status_full'] = $status;
        $params['body']['doc']['video'] = $parent;
        $params['body']['doc_as_upsert'] = true;
        //print_r($params);die;
        $response = $this->client->update($params);
        print_r($response);
    }
    /*
    check kw app relate
    */
    public function kwplanerRelateApp(){
        echo 'relate';
        $mail = 'tovicorp.com@gmail.com';
        $mailPass = 'MKwb0MN0@';

        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();
        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $prefs = array('download.default_directory' => 'c:/temp');
        $options->setExperimentalOption("prefs", $prefs);
       	//$ss = array('--headless','--start-maximized');
        //$options->addArguments($ss);
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
        #Access link Google Ads
        $driver->get("https://ads.google.com/intl/vi_vn/home/");
        sleep(2);

        #Click button sign-in
        $driver->findElement(WebDriverBy::cssSelector(
            'a[href="https://ads.google.com/um/signin?subid=ALL-vi-et-g-aw-a-home-awhp_xin1_signin!o2"]'))
            ->click();
        sleep(1);

        #Fill email
        $driver->findElement(WebDriverBy::id('identifierId'))->sendKeys($mail);
        $driver->findElement(WebDriverBy::className('CwaK9'))->click();
        sleep(1);
        #Fill password
        $driver->findElement(WebDriverBy::cssSelector('input[type="password"]'))->sendKeys($mailPass);
        $driver->findElement(WebDriverBy::className('CwaK9'))->click();
        sleep(3);

        #Select account Ads
        $elements = $driver->findElements(WebDriverBy::cssSelector('a.umx-l'));
        foreach ($elements as $element) {
            if ($element->getAttribute('href') == "https://ads.google.com/um/identity?authuser=2&dst=/um/homepage?__e%3D6119414799") {
                $element->click();
                break;
            }
        }
        sleep(5);

        #Access link planner Ads
        $driver->get('https://ads.google.com/aw/keywordplanner/home?ocid=337734716&__u=6391395840&__c=8217153884&authuser=2');
        sleep(5);
        // try {
        //     $driver->findElement(WebDriverBy::cssSelector('div.particle-table-row'))->click();
        //     sleep(1);
        // } catch (\Exception $e) {
        //     sleep(10);
        //     $driver->findElement(WebDriverBy::cssSelector('div.particle-table-row'))->click();
        // }
        sleep(2);
        $driver->findElement(WebDriverBy::cssSelector('div.card-frame'))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->sendKeys('demo');
            sleep(0.5);
        #Click enter on keyboard to search
        $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->sendKeys(WebDriverKeys::ENTER);
        sleep(2);
        #Click button 'search' to redirect page resutl
        $driver->findElement(
            WebDriverBy::cssSelector('div.get-results-button-container > material-button.get-results-button')
        )->click();
        sleep(6);

        #Remove localtion in Viet Nam
        // $driver->findElement(WebDriverBy::cssSelector('div.settings-bar > labeled-value'))
        //     ->click();
        // sleep(2);
        // $driver->findElement(WebDriverBy::cssSelector('td.remove'))->click();
        // sleep(20);
        // try {
        //     $driver->findElement(WebDriverBy::cssSelector('material-button.highlighted'))->click();
        //     sleep(3);
        // } catch (\Exception $e1) {
        //     $driver->findElement(WebDriverBy::cssSelector('material-button.highlighted'))->click();
        //     sleep(3);
        // }
        

        #Select language English
        $driver->findElement(WebDriverBy::cssSelector('language-selector'))->click();
        sleep(2);
        $driver->findElement(WebDriverBy::cssSelector('input[aria-label="Tìm kiếm ngôn ngữ"]'))
            ->sendKeys('Anh');
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('input[aria-label="Tìm kiếm ngôn ngữ"]'))
            ->sendKeys(WebDriverKeys::ENTER);
        sleep(3);
        $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
        sleep(2);
        $list = DB::select(DB::raw("SELECT * FROM app_relate where status = 20 ORDER BY `installs` DESC"));
        //$list = App::where('status',1)->get();
        //$count = AppRelate::where('status',0)->orderby('app_relate_id','ASC')->count();
        $count = count($list);
        echo 'count:'.$count;
        for ($i = 0;$i < $count; $i++) {
            //$listContinue = DB::select(DB::raw("SELECT * FROM `keywords` WHERE `status` = 0 AND `domain` LIKE 'apkvi' and kwplaner is null  order by keyword_id desc LIMIT 9 OFFSET ".$j*9));
            //$listContinue = json_decode($tmp->relate,true);
            //$listContinue = AppRelate::where('status',0)->limit(8)->offset($i*8)->orderby('app_relate_id','ASC')->get();
            $listContinue = DB::select(DB::raw("SELECT * FROM app_relate where status = 20 ORDER BY `installs` LIMIT 8"));
            //print_r($listContinue);
            $res = array();
            foreach ($listContinue as $key => $p) {
                $title = preg_replace("/[^A-Za-z0-9 ]/", ' ', $p->title);

                //$title = trim(preg_replace('/\s+/', ' ', $title));
                $title = $this->replaceTitle($title);
                $title = $title.' apk for pc';
                $listKey[] = $p;
                if($key < count($listContinue) - 1){
                    $title = str_replace(',', '', $title);
                    $title = $title.',';
                }

                $res[] = $title;
            }

            foreach ($res as $value){
                $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))->sendKeys($value);
                sleep(0.5);
            }

            #Click enter on keyboard to search
            //$driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))
            $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))->sendKeys(WebDriverKeys::ENTER);
            sleep(2);
            try {
                $driver->findElement(WebDriverBy::cssSelector('.get-results-button'))
                ->click();
                sleep(5);
                $this->updateRelateApp($driver, $listContinue);
                try {
                    $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
                    sleep(2);
                    for ($i=1; $i <= count($res); $i++) {
                        try {
                            $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))
                            ->sendKeys(WebDriverKeys::BACKSPACE);
                            sleep(1.5);
                        } catch (\Exception $e2) {
                            
                        }
                        
                    }
                    sleep(3);
                } catch (\Exception $e3) {
                    
                }
            } catch (\Exception $e4) {
                try {
                    $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
                    sleep(2);
                    for ($i=1; $i <= count($res); $i++) {
                        try {
                            $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))
                            ->sendKeys(WebDriverKeys::BACKSPACE);
                            sleep(1.5);
                        } catch (\Exception $e5) {
                            
                        }
                        
                    }
                    sleep(3);
                } catch (\Exception $e6) {
                    $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))
                            ->clear();
                }
            }
            
            //die;
        }
    }
    /*
    run check suggest
    */
    public function checkSuggest(){
        $list = AppRelate::where('statusKw',0)->get();
        foreach ($list as $item) {
            $title = preg_replace("/[^A-Za-z0-9 ]/", '', $item->title);

            $title = trim(preg_replace('/\s+/', ' ', $title));
            $title = $title.' apk';
            $test = $this->getSugget($title);
            print_r($test);
            if($test['status'] == 1){
                $xml = $test['msg'];
                if($xml != ''){
                    $xmldata = simplexml_load_string($xml);
                    
                    $res = array();
                    foreach ($xmldata->CompleteSuggestion as $p) {
                        $res[] = $p->suggestion['data'];
                    }
                    AppRelate::where('app_relate_id',$item->app_relate_id)->update(['relate' => json_encode($res),'statusKw' => 1]);
                    //echo $xmldata->CompleteSuggestion[0]->suggestion['data'];
                }
            }
        }
       
        //print_r($test);
    }
    public function replaceTitle($str){
        //$str = "I      am a PHP   Developer";
        $str_length = strlen($str);
        $str_arr = str_split($str);
        for ($i = 0; $i < $str_length; $i++) {
           if (isset($str_arr[$i + 1])  && $str_arr[$i] == ' ' && $str_arr[$i] == $str_arr[$i + 1]) {
               unset($str_arr[$i]);
           } 
           else {
             continue;
           }
        }
        return implode("", $str_arr);
    }
    /*
    get suggest gg search
    */
    public function getSugget($title){
        $url = 'http://suggestqueries.google.com/complete/search?output=toolbar&hl=en&q='.urlencode($title).'&gl=us';
        $res = httprequest($url, 'GET');
        return $res;
    }
    /**/
    public function checkApp(){
        $list = $this->getTopInstalls();
        foreach ($list as $item) {
            if(DB::table('app_relate')->where('appid',$item['appid'])->count() == 0 && isset($item['title'])){
            DB::table('app_relate')->insert(['title' => $item['title'], 'appid' => $item['appid'], 'status' => 20,'installs' => $item['installs'],'releaseDate' => $item['releasedate']]);
            } else {
                DB::table('app_relate')->where('appid',$item['appid'])->update(['status' =>20]);
            }
        }
       /*$list = AppRelate::where('kwplanner','>',0)->get();
       $total = 0;
       $res = array();
       foreach ($list as $item) {
            if(strpos($item->keywords, ' apk') !== false){
                if(strpos($item->keywords,'mod') == false){
                    if(!in_array($item->keywords,$res)){
                        $total += 1;
                        echo $item->keywords.PHP_EOL;
                        $res[] = $item->keywords;
                    }
                }
            }
       }*/
       /*$list_check = AppRelate::orderby('installs','DESC')->get();
       $list_appid = array();
       foreach ($list_check as $app) {
           $title = preg_replace("/[^A-Za-z0-9 ]/", '', $app->title);

            $title = trim(preg_replace('/\s+/', ' ', $title));
            $title = $title.' apk';
            foreach ($res as $tmp) {
                similar_text($tmp, $title, $perc);
                if($perc >= 80){
                    $list_appid[] = $app->appid;
                }
            }
       }
       foreach ($list_appid as $appid) {
           AppRelate::where('appid',$appid)->update(['status' => 10]);
       }*/
       //echo $total;
       //$list = DB::select(DB::raw("SELECT DISTINCT title from app_relate where status = 10"));
       /*$list = App::get();
       $total = 0;
       foreach ($list as $item) {
           //$info = AppRelate::where('title',$item->title)->orderby('installs','DESC')->first();
           //echo $item->title.'-'.$info->appid.PHP_EOL;
           $total += 1;
           //$this->updateAppShow($info->appid);
           $this->updateAppShow($item->appid);
       }
       echo $total;*/
    }
    public function checkKeyAppTop(){
    	$list = $this->getTopInstalls();
        //print_r(count($list));die;
		// $app = explode(',', $app);
		// //DB::table('apk')->
		// foreach ($app as $tmp) {
		// 	//echo $tmp;die;
		// 	if($tmp != ''){
		// 		DB::table('apk')->where('appid',$tmp)->update(['status_full' => 30]);
		// 	}
		// 	//$this->updateAppShow('com.ss.android.ugc.trill', 10);
		// }
  //       die;
        //$this->updateAppShow('com.fingersoft.hillclimb',50);die;
        //$list = App::orderby('kwplanner','DESC')->limit(35)->get();
        $res = array();
        $res[] = array('title','appid','search','keyword');
        // foreach ($list as $item) {
        //     //echo $item->title.PHP_EOL;
        //     if($item->relate != '[]'){
        //         $relate = json_decode($item->relate_planner,true);
        //         foreach ($relate as $key) {
        //             //print_r($key);die;
        //             $count = App::where('title',$key['keyword'])->count();
        //             if($key['kwplaner'] > 0 && $count == 0){
        //                 if(strpos($key['keyword'], ' apk') !== false && (strpos($key['keyword'],'mod') == false && strpos($key['keyword'],'hack') == false) && (trim($key['keyword']) != trim($item->title))){
        //                     if(!in_array($key['keyword'], $res)){
                                
        //                     }
        //                 }
        //             } elseif($count > 0){
        //                 $res[] = $key['keyword'];
        //                 echo $key['keyword'].'-'.$key['kwplaner'].PHP_EOL;
        //                 App::where('title',$key['keyword'])->update(['kwplanner' => $key['kwplaner']]);
        //             }
        //         }
        //         $relate = json_decode($item->relate,true);
                
        //         $keyword ="";
        //         foreach ($relate as $p) {
        //             $keyword .= $p.',';
        //         }
        //         $res[] = array($item->title, $item->appid, $item->kwplanner, $keyword);
                
        //     }
        // }
        
        //$ = AppRelate::where('status','=',10)->orderby('installs','DESC')->get();
        $list_appid = array();
        $total = 0;
        $tmp_res = array();
        foreach ($list as $tmp) {
            $item = App::where('appid',$tmp['appid'])->first();
            if($item){
                if(strpos($item->title, ' apk') !== false){
                    if(strpos($item->title,'mod') == false){
                        if(!in_array($item->title,$tmp_res)){
                            $total += 1;
                            $list_appid[] = $item->appid;
                            //echo $item->keywords.PHP_EOL;
                            $tmp_res[] = array('appid' => $tmp['appid'], 'key' =>$item->title,'volumn'=> $item->kwplanner,'installs' => $tmp['installs']);
                        }
                    }
                }
            }
        }
        //$res = $tmp_res;
        //print_r($tmp_res);die;
        // $list_check = AppRelate::where('status','=', 50)->orderby('installs','DESC')->get();
        
        $res = array();
        $keys = array();
        foreach ($list as $tmp) {
            $app = AppRelate::where('appid', $tmp['appid'])->first();
            if($app && !in_array($tmp['appid'] , $list_appid)){
            // $title = preg_replace("/[^A-Za-z0-9 ]/", ' ', $app->title);
            // $title = $this->replaceTitle($title);
            // $title = $title.' apk';
            // //foreach ($tmp_res as $tmp) {
            //     similar_text(strtolower($app->keywords), strtolower($title), $perc);
            //     if($perc >= 95){
            //         if(!in_array($app->appid, $list_appid) && !in_array($app->keywords, $keys)){
            //             $list_appid[] = $app->appid;
            //             $keys[] = $app->keywords;
            //             $res[] = array($app->keywords, $app->title, $app->appid, $app->kwplanner,$tmp['installs']);
            //         }
            //     }
            $res[] = array('',$tmp['title'], $tmp['appid'],-1,$tmp['installs']);
            }
            //}*/
           

        }
        
        $export = new Keywords($res);
        echo 'total:'.count($res);
        //$list_check = AppRelate::where('status','=', 50)->orderby('installs','DESC')->get();
        //$list_appid = array();
        //$res = array();
        //foreach ($list_check as $app) {
        //   $res[] = array('keyword' => $app->keywords,'kwplanner' => $app->kwplanner);
        //}
        //$export = new Keywords($res);
        return Excel::store($export,'public/4567.xlsx');
        
        //echo 'total:'.count($res);
        
        // $list = DB::table('appid_store')->where('status3',50)->get();
        // foreach ($apps as $app) {
        //     $info = DB::table('app_relate')->where('appid',$app)->first();
        //     if($info){
        //         $res[] = array('appid' => $app,'keyword' => $info->title,'search' => $info->kwplanner);
        //     }
          
        // }
        // $export = new Keywords($res);
        // return Excel::store($export,'public/topapp_list_2.xlsx');
    }
    public function relateApp(){

        //$domain = DB::table('domain_out')->first();
        //print_r($domain->domain_name);
        /*$i = 0;
        $list = DB::table('app_top')->orderBy('kwplanner','DESC')->get();
        foreach ($list as $tmp) {
            $check = $this->checkAbuse($tmp->appid);
            if($check == false){
                $detail = $this->detailApp($tmp->appid);
                if(isset($detail['_source']['title'])){
                    //$this->updateAppShow($tmp->appid,1002);
                    //$detail = $this->detailApp($tmp->appid);
                    //echo $tmp->appid.PHP_EOL;
                    $title = strtolower($detail['_source']['title']);
                    echo $title.'-'.$tmp->appid.PHP_EOL;
                    $result_search = $this->searchFullText($title);
                    foreach ($result_search as $result) {
                        $a = $this->detailApp($result['appid']);
                        if($result['appid'] != $tmp->appid && isset($a['_source']['title'])){
                            $data = array('appid' => $result['appid'], 'title' => $result['title'], 'installs' => $result['installs'], 'releaseDate' => $result['releasedate']);
                            echo 'index-'.$result['appid'].PHP_EOL;
                            $this->updateAppShow($result['appid'],1002, $tmp->appid);
                            //AppRelate::firstOrCreate(['appid' => $result['appid']],$data);
                        }
                        // } elseif($result['appid'] != $tmp->appid) {
                        //     $info = $this->detailAppFull($result['appid']);
                        //     unset($info['@version']);
                        //     unset($info['@timestamp']);
                        //     //print_r($info['_source']);
                        //     $params = [
                        //         'index' => 'apktot_1000',
                        //         'type' => 'apktot_1000',
                        //         'id' => $result['appid'],
                        //         'body'  => $info['_source']
                        //     ];
                        //     $response = $this->client->index($params);
                        //     print_r($response);
                        //     echo 'noindex-'.$result['appid'].PHP_EOL;
                        // }
                    }
                    //die;
                    $i++;
                }
            }
            if($i == 34){
                break;
            }
        }*/
    }
    public function checkAbuse($appid){
        $params['index'] = 'apktot_abuse';
        $params['type'] = 'apktot_file';
        $params['id'] = $appid;
        $params['client'] = ['ignore' => 404];
        $app = $this->client->get($params);
        if($app['found'] == true){
          return true;
        } else {
          return false;
        }
    }
    public function getTopInstalls(){
        $where = [
          'index' => $this->name_index, 
          'type' => $this->name_index,
          'body' => [
            "from" => 0, "size" => 2000,
            'sort' => [
            [ "installs" => ["order" =>"desc"]],
            "_score"
            ],
            'query' =>  [
                'match' => ['is_file' => 1]
            ] 
          ]
        ];
        $res = $this->client->search($where);
        $result = array();
        foreach ($res['hits']['hits'] as $item) {
          
          // if($item['_source']['obb_size'] > 0){
          //   $item['_source']['urltitle'] = $item['_source']['urltitle'].'-apk-and-obb-data-download';
          // } else {
          //   $item['_source']['urltitle'] = $item['_source']['urltitle'].'-apk-download';
          // }
          // $tmp = array('appid' => $item['_source']['appid'],'title' => $item['_source']['title'],'offerby' => $item['_source']['offerby'],'cover' => $item['_source']['cover'],'dev_id' => $item['_source']['dev_id'],'urltitle' => $item['_source']['urltitle'],'score' => $item['_source']['score']);
          $result[] = $item['_source'];
        }
        return $result;
    }
    public function kwplanerKeyword(){
        
            echo 'relate';
            $mail = 'thanhtoanlc104';
            $mailPass = 'Toan1234@';

            $host = 'http://localhost:4444/wd/hub'; // this is the default
            $USE_FIREFOX = true; // if false, will use chrome.
            $caps = DesiredCapabilities::chrome();
            $prefs = array();
            $options = new ChromeOptions();
            $prefs['profile.default_content_setting_values.notifications'] = 2;
            $prefs = array('download.default_directory' => 'c:/temp');
            $options->setExperimentalOption("prefs", $prefs);
            //$ss = array('--headless','--start-maximized');
            //$options->addArguments($ss);
            $caps->setCapability(ChromeOptions::CAPABILITY, $options);
            $capabilities = DesiredCapabilities::firefox();
            /*$capabilities->setCapability(
                'moz:firefoxOptions',
               ['args' => ['-headless']]
            );*/
            if ($USE_FIREFOX)
            {
                $driver = RemoteWebDriver::create(
                    $host,
                    $capabilities
                );
            }
            else
            {
                $driver = RemoteWebDriver::create(
                    $host,
                    $caps
                );
            }
            $driver->get("https://ads.google.com/intl/vi_vn/home/");
            sleep(2);

            $cookies = file_get_contents(public_path('gg.txt'));
            $cookies = explode(PHP_EOL,$cookies);
            foreach ($cookies as $cookie) {

                $tmp = explode(':',$cookie);
                if(isset($tmp[1])){
                    $tmp_ck = array('name' => $tmp[0], 'value' => $tmp[1]);
                    $driver->manage()->addCookie($tmp_ck);
                }

            }
            
            
            #Access link planner Ads
            $driver->get('https://ads.google.com/aw/keywordplanner/home?ocid=225239472&__u=2132400575&__c=1531271728&authuser=0');
            sleep(5);
            // try {
            //     $driver->findElement(WebDriverBy::cssSelector('div.particle-table-row'))->click();
            //     sleep(1);
            // } catch (\Exception $e) {
            //     sleep(10);
            //     $driver->findElement(WebDriverBy::cssSelector('div.particle-table-row'))->click();
            // }
            sleep(2);
            $driver->findElement(WebDriverBy::cssSelector('div.card-frame'))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->sendKeys('demo');
                sleep(0.5);
            #Click enter on keyboard to search
            $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->sendKeys(WebDriverKeys::ENTER);
            sleep(2);
            #Click button 'search' to redirect page resutl
            $driver->findElement(
                WebDriverBy::cssSelector('div.get-results-button-container > material-button.get-results-button')
            )->click();
            sleep(6);

            #Remove localtion in Viet Nam
            $driver->findElement(WebDriverBy::className('location-button'))->click();
            sleep(2);
            $driver->findElement(WebDriverBy::cssSelector('td.remove'))->click();
            sleep(20);
            try {
                $driver->findElement(WebDriverBy::cssSelector('material-button.highlighted'))->click();
                sleep(3);
            } catch (\Exception $e1) {
                $driver->findElement(WebDriverBy::cssSelector('material-button.highlighted'))->click();
                sleep(3);
            }
            

            #Select language English
            $driver->findElement(WebDriverBy::cssSelector('language-selector'))->click();
            sleep(2);
            $driver->findElement(WebDriverBy::cssSelector('input[aria-label="Tìm kiếm ngôn ngữ"]'))
                ->sendKeys('Anh');
            sleep(1);
            $driver->findElement(WebDriverBy::cssSelector('input[aria-label="Tìm kiếm ngôn ngữ"]'))
                ->sendKeys(WebDriverKeys::ENTER);
            sleep(3);
            $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
            sleep(2);
            $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))->sendKeys(WebDriverKeys::BACKSPACE);
            //$sql = "SELECT * FROM `keywords` WHERE `domain` LIKE 'shopee.vn' and position <= 5 and statusKw = 1 and kwplaner is null";
            $sql = "SELECT * FROM `keywords` WHERE `domain` LIKE 'a_hieu_3_12' and kwplaner is null";
            // if($min != 0 && $max != 0){
            //     $sql = $sql.= ' and keyword_id >= '.$min. ' and keyword_id < '.$max;
            // }
            $list = DB::select(DB::raw($sql));
            //$list = App::where('status',1)->get();
            //$count = AppRelate::where('status',0)->orderby('app_relate_id','ASC')->count();
            $count = count($list)/5;
            echo 'count:'.$count;
            for ($i = 0;$i < $count; $i = $i + 5) {
                try {

                    $listContinue = DB::select(DB::raw($sql." LIMIT ".$i.",5"));
                    echo $sql." LIMIT ".$i.",5".PHP_EOL;
                    //$listContinue = DB::select(DB::raw("SELECT * FROM `keywords` WHERE `domain` LIKE 'support.office.com' and search_volumn >= 1000 and position <= 10 ORDER BY `kwplaner` DESC"));
                    //print_r($listContinue);
                    $res = array();
                    foreach ($listContinue as $key => $p) {
                        $title = $p->keyword;
                        $title = str_replace('/','',$title);
                        if($key < 4 && strlen($title) < 80){
                            $title = $title.',';
                        }
                        if(strlen($title) < 80){
                            $res[] = $title;
                        }
                    }

                    foreach ($res as $value){
                        $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))->sendKeys($value);
                        sleep(0.5);
                    }

                    #Click enter on keyboard to search
                    //$driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))
                    $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))->sendKeys(WebDriverKeys::ENTER);
                    sleep(2);
                    try {
                        $driver->findElement(WebDriverBy::cssSelector('.get-results-button'))
                        ->click();
                        sleep(5);
                        $this->updateRelateApp($driver, $listContinue);
                        try {
                            $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
                            sleep(2);
                            for ($j=1; $j <= count($res); $j++) {
                                try {
                                    $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))
                                    ->sendKeys(WebDriverKeys::BACKSPACE);
                                    sleep(1.5);
                                } catch (\Exception $e2) {
                                    
                                }
                                
                            }
                            sleep(3);
                        } catch (\Exception $e3) {
                            
                        }
                    } catch (\Exception $e4) {
                        try {
                            $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
                            sleep(2);
                            for ($j=1; $j <= count($res); $j++) {
                                try {
                                    $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))
                                    ->sendKeys(WebDriverKeys::BACKSPACE);
                                    sleep(1.5);
                                } catch (\Exception $e5) {
                                    
                                }
                                
                            }
                            sleep(3);
                        } catch (\Exception $e6) {
                            $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))
                                    ->clear();
                        }
                    }
                } catch (\Exception $error) {
                
                }
                //die;
            }
        
        
    }
    public function loginGG(){
        echo 'login';
        $mail = 'thanhtoanlc104';
        $mailPass = 'Toan1234@';

        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();
        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $prefs = array('download.default_directory' => 'c:/temp');
        $options->setExperimentalOption("prefs", $prefs);
        $caps->setCapability(
            'moz:firefoxOptions',
           ['args' => ['-headless']]
        );
        //$ss = array('--headless','--start-maximized');
        //$options->addArguments($ss);
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
        #Access link Google Ads
        $driver->get("https://ads.google.com/intl/vi_vn/home/");
        sleep(2);

        #Click button sign-in
        $driver->findElement(WebDriverBy::linkText(
            'Đăng nhập'))
            ->click();
        sleep(1);

        #Fill email
        $driver->findElement(WebDriverBy::id('identifierId'))->sendKeys($mail);
        $driver->findElement(WebDriverBy::className('CwaK9'))->click();
        sleep(1);
        #Fill password
        $driver->findElement(WebDriverBy::cssSelector('input[type="password"]'))->sendKeys($mailPass);
        $driver->findElement(WebDriverBy::className('CwaK9'))->click();
        sleep(3);

        #Select account Ads
        /*$elements = $driver->findElements(WebDriverBy::cssSelector('a.umx-l'));
        foreach ($elements as $element) {
            if ($element->getAttribute('href') == "https://ads.google.com/um/identity?authuser=2&dst=/um/homepage?__e%3D6119414799") {
                $element->click();
                break;
            }
        }*/
        try {
            $driver->findElement(WebDriverBy::partialLinkText(
            'bui quoc toan'))->click();sleep(5);
        } catch (\Exception $e) {
            sleep(5);
        }
        $list = $driver->manage()->getCookies();
        foreach ($list as $cookie) {
            //$domain = $cookie->getDomain();
            print_r($cookie);
            $tmp = $cookie['name'].':'.$cookie['value'];
            file_put_contents(public_path('gg.txt'), $tmp.PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        
    }
}
