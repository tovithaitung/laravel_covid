<?php

namespace App\Http\Controllers;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Firefox\FirefoxDriver;
include 'simple_html_dom.php';
class KeyWordsPlanerController extends Controller
{
    function __construct()
    {

        set_error_handler(null);
        set_exception_handler(null);
        
    }
   
    public function getKeyWords($mail, $mailPass)
    {
        #Config browsers
        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();
        //$prefs['profile.default_content_setting_values.notifications'] = 2;
        //$prefs = array('download.default_directory' => 'c:/temp');
        //$options->setExperimentalOption("prefs", $prefs);
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
            if ($element->getAttribute('href') == "https://ads.google.com/
                    um/identity?authuser=0&dst=/um/homepage?__e%3D8457665915") {
                $element->click();
                break;
            }
        }
        sleep(5);

        #Access link planner Ads
        $driver->get('https://ads.google.com/aw/keywordplanner/home?ocid=337923043&__c=3590778507&authuser=0&__u=6391395840');
        sleep(5);
        try {
            $driver->findElement(WebDriverBy::cssSelector('div.particle-table-row'))->click();
            sleep(1);
        } catch (\Exception $e) {
            sleep(10);
            $driver->findElement(WebDriverBy::cssSelector('div.particle-table-row'))->click();
        }
        sleep(2);
        //$driver->findElement(WebDriverBy::cssSelector('div.collapsed-frame > div.ideas-content'))->click();
        $driver->findElement(WebDriverBy::cssSelector('div.card-frame'))->click();
        sleep(1);

        $keywordsFirst = DB::select(DB::raw("SELECT * FROM `keywords` WHERE `status` = 0 AND `domain` LIKE 'hieutovi' and kwplaner is null order by keyword_id desc  LIMIT 9 OFFSET 0"));
        $res = array();
        $listKey = array();
        foreach ($keywordsFirst as $key => $p) {
            $listKey[] = $p->keyword;
            if($key < count($keywordsFirst) - 1){
                $p->keyword = str_replace(',', '', $p->keyword);
                $p->keyword = $p->keyword.',';
            }
            $res[] = $p->keyword;
        }
        print_r($res);
        $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->click();
        sleep(1);
        foreach ($res as $value){

            $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->sendKeys($value);
            sleep(0.5);
        }
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

        $this->clearInputSeacrh($driver, $listKey);
        sleep(2);

        $loop = count(DB::select(DB::raw("SELECT * FROM `keywords` WHERE `status` = 0 AND `domain` LIKE 'hieutovi' and kwplaner is null")));
        for ($j = 0; $j < $loop/9; $j++) {
            /*$listContinue = DB::table('requests')->where('status',0)->offset($j*9)->limit(9)->get();
            $res = array();
            foreach ($listContinue as $key => $p) {
                $listKey[] = $p->nameCheck;
                if($key < count($listContinue) - 1){
                    $p->nameCheck = str_replace(',', '', $p->nameCheck);
                    $p->nameCheck = $p->nameCheck.',';
                }
                $res[] = $p->nameCheck;
            }*/
            //$listContinue = DB::table('keywords')->where('status',0)->where('kwplaner','is',null)->where('domain','antovi')->offset($j*9)->limit(9)->get();
            $listContinue = DB::select(DB::raw("SELECT * FROM `keywords` WHERE `status` = 0 AND `domain` LIKE 'hieutovi' and kwplaner is null  order by keyword_id desc LIMIT 9 OFFSET ".$j*9));

            $res = array();
            foreach ($listContinue as $key => $p) {
                $listKey[] = $p->keyword;
                if($key < count($listContinue) - 1){
                    $p->keyword = str_replace(',', '', $p->keyword);
                    $p->keyword = $p->keyword.',';
                }
                $res[] = $p->keyword;
            }
            //print_r($res);
            //$driver->findElement(WebDriverBy::cssSelector('div.summary'))->click();
            //sleep(5);
            foreach ($res as $value){
                /*if($key < count($keywordsFirst) - 1){
                    $value->keyword = $value->keyword.',';
                }*/
                //$value = 'demo';
                //$driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))->sendKeys($value);
                $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))->sendKeys($value);
                sleep(0.5);
            }
            #Click enter on keyboard to search
            //$driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))
            $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))->sendKeys(WebDriverKeys::ENTER);
            sleep(2);
            //$element = $driver->findElement(WebDriverBy::cssSelector('material-button.blue-button'));
            //$driver->executeScript('return window.location',$element);
            //sleep(4);
            //$driver->findElement(WebDriverBy::cssSelector('material-button.blue-button'))
               // ->click();
            //sleep(2);
            $driver->findElement(WebDriverBy::cssSelector('.get-results-button'))
                ->click();
            sleep(5);
            $this->saveKeywordsSearch($driver, $listContinue);
            //$this->clearInputSeacrh($driver, $listContinue);
            try {
                $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
                sleep(2);
                for ($i=1; $i <= count($res); $i++) {
                    try {
                        $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))
                        ->sendKeys(WebDriverKeys::BACKSPACE);
                        sleep(0.8);
                    } catch (\Exception $e) {
                        
                    }
                    
                }
                sleep(3);
            } catch (\Exception $e) {
                
            }
            
        }
    }
    private function saveKeywordsSearch($driver, $keywords){
        foreach ($keywords as $key => $p) {
            $keywordsPlanner = array();
            $i = $key + 1;
            //echo $i.PHP_EOL;
            try {
               //$htmlKeyword = $driver->findElement(WebDriverBy::xpath('//div[@class="particle-table-row"]['.$i.']/ess-cell[1]'))->getAttribute('innerHTML');
                if(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath('//div[@class="particle-table-row"]['.$i.']/ess-cell'))){
                    $htmlKeyword = $driver->findElement(WebDriverBy::xpath('//div[@class="particle-table-row"]['.$i.']/ess-cell'))->getAttribute('innerHTML');
                    //Get min search per month
                    $htmlMinSearch = $driver->findElement(WebDriverBy::xpath('//div[@class="particle-table-row"]['.$i.']/ess-cell[2]'))->getAttribute('innerHTML');
                    $domKeyword = str_get_html($htmlKeyword);
                    $domMinSearch = str_get_html($htmlMinSearch);
                    $keyword = $domKeyword->find('span',0)->plaintext;
                    $minSearch = $domMinSearch->find('span',0)->plaintext;
                    $strMin = explode("–", $minSearch);
                    $volumn = preg_replace("/[^0-9 ]/", '', $strMin[0]);
                    if(strpos($strMin[0], 'N')){
                        (int)$volumn = (int)$volumn*1000;
                    }
                    echo $volumn.PHP_EOL;
                    $keywordsPlanner['kwplaner'] = (int)$volumn;
                    $keywordsPlanner['statusKw'] = 1;
                    $keywordsPlanner['created_at'] = date('Y-m-d H:i:s',time());
                    $checkKeyword = DB::table('keywords')->where('keyword',$keyword)->count();
                    if($checkKeyword > 0){
                        DB::table('keywords')->where('keyword',$keyword)->update($keywordsPlanner);
                    } else{
                        $keywordsPlanner['keyword'] = $keyword;
                        $keywordsPlanner['domain'] = 'antovi';
                        DB::table('keywords')->insert([$keywordsPlanner]);
                        DB::table('keywords')->where('keyword_id',$p->keyword_id)->update(['status' => 1]);
                    }
                }
                sleep(0.5); 
            } catch (\Exception $e) {
                try {
                    $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
                    sleep(2);
                    for ($i=1; $i <= count($keywords); $i++) {
                        $driver->findElement(WebDriverBy::cssSelector('.seeds-underline .search-input'))
                            ->sendKeys(WebDriverKeys::BACKSPACE);
                            sleep(0.8);
                    }
                } catch (\Exception $e) {
                    
                }
                
            }
            
        }
    }
    private function getAppid($keywords, $check){
        $appid = "";
        foreach ($keywords as $item) {
            if(strpos(strtolower($item->nameCheck), strtolower(trim($check))) !== false){
                $appid = $item->appid;
                break;
            }
        }
        return $appid;
    }
    private function getKeyword($keywords, $check){
        $appid = "";
        foreach ($keywords as $item) {
            if(strpos(strtolower($item->nameCheck), strtolower(trim($check))) !== false){
                $appid = $item->appid;
                break;
            }
        }
        return $appid;
    }
    /**
     * @param $driver
     * @param $keywords
     */
    private function clearInputSeacrh($driver, $keywords)
    {
        try {
             $driver->findElement(WebDriverBy::cssSelector('search-chips-summary.enable-background > div.summary'))->click();
        sleep(2);
        } catch (\Exception $e) {
            
        }
       
        // for ($i=1; $i <= count($keywords); $i++) {
        //     $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))
        //         ->sendKeys(WebDriverKeys::BACKSPACE);
        //         sleep(0.8);
        // }
    }
    /*

    */
    private function addInputSearch($driver, $keywords){
        
        foreach ($keywords as $value){
            /*if($key < count($keywordsFirst) - 1){
                $value->keyword = $value->keyword.',';
            }*/
            $driver->findElement(WebDriverBy::cssSelector('div.input-container > input.search-input'))
                ->sendKeys($value);
            sleep(0.5);
        }
    }
    public function checkKeyword($keywords){
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
            $caps = DesiredCapabilities::firefox();
            $profile = new FirefoxProfile();
            $profile->setPreference('general.useragent.override', 'Mozilla/5.0 (X11; Linux x86_64; rv:60.0) Gecko/20100101 Firefox/60.0');
            
            $caps->setCapability(FirefoxDriver::PROFILE, $profile);
            $driver = RemoteWebDriver::create(
                $host,
                $caps
            );
            $driver->manage()->addCookie('S=adwords-frontend-shard-manager=0z00IJfbTAygoExN10kXV24xOMYfDtw2; NID=190=E4Rr6-OSbc8CU8AQ33fAcOOodnKEGmISBcgTqgNNXULufhZ7djaW8bZgUZhEe6wr8BtwM-iNfsuy-2bU8eL_MKiqaQO_tY_rBGHEK-pikFPngs3u_3DZeAXcW2VzgGZBxuxydvVbNygauY8ysu34YDgzGdb6XmpNdHJ9iMxTTCXEXHPidtpQ0zGDqs_DXbxYdek_ePGdlYL3LD2Fle0uRRs33B_zN63EiJNc; CONSENT=YES+DE.en+V9; 1P_JAR=2019-09-25-11; ANID=AHWqTUmM75k9eF0txoUvPLYOVdon1ttc5ZnMk08_YX-Mr_r_p5S5GwYXcRYpcxn1; _ga=GA1.3.1071326919.1571969858; _gid=GA1.3.1676005054.1571969858; SID=pwedgXlbSM_3QD6EXCb-aEwq-bsiu8Xgeec2qZupd7vLOLvCifn89TgS_M1v1B6dKEGgqA.; HSID=Al_eI2jgNSeR9lXDW; SSID=AH4qrI7HR9IjIaguO; APISID=IU3NIhG5P845940G/AHukkhxsFcz3hAsbb; SAPISID=-ZhZLtaRl6IMs9RL/ACfNM0rFoseMXUHnD; SIDCC=AN0-TYuteymGz59-fX8tqONFvPvi8cr07QpT59bRpPF-_8d3ADrKk_in8KlqV1qldaTLmjr8_g; SAG=pwedgejQnzj8OfNQRiCzvS1rH5dqvJkSAXPXMeJHxxdmNAwsBEDpI-5cAqcZqLSQ3UsTWA.; adwordsReferralSource=sourceid=emp&subid=ALL-vi-et-g-aw-a-home-awhp_xin1_signin!o2&clickid=; AdsUserLocale=en_US; S=adwords-navi=4ABOmTyAZiCVuM2g33n21AW8Qv858xLU');
        }
        else
        {
            $driver = RemoteWebDriver::create(
                $host,
                $caps
            );
        }
        #Access link Google Ads
        $driver->get("https://ads.google.com/aw/keywordplanner/home");
        sleep(2);

        #Click button sign-in
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
        $driver->get('https://ads.google.com/aw/keywordplanner/home');
        sleep(5);
        // try {
        //     $driver->findElement(WebDriverBy::cssSelector('div.particle-table-row'))->click();
        //     sleep(1);
        // } catch (\Exception $e) {
        //     sleep(10);
        //     $driver->findElement(WebDriverBy::cssSelector('div.particle-table-row'))->click();
        // }
        sleep(2);
        $driver->findElement(WebDriverBy::cssSelector('div.ideas-content'))->click();
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
    }
    public function loginGG(){

    }
}
