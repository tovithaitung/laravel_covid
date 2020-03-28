<?php

namespace App\Http\Controllers;
ini_set('memory_limit',-1);
use Illuminate\Http\Request;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Firefox\FirefoxProfile;
use Facebook\WebDriver\Firefox\FirefoxDriver;
use DB;
include 'simple_html_dom.php';
class TrafficBotController extends Controller
{
    //
    public function search(){
        //echo 1;die;
        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.

        $profile = new FirefoxProfile();
        $profile->setPreference('general.useragent.override', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0');

       
        $caps = DesiredCapabilities::firefox();
        //$profile = new FirefoxProfile();
        //$profile->setPreference('network.proxy.type', 1);
        //$profile->setPreference('network.proxy.socks', 'localhost');
        //$profile->setPreference('network.proxy.socks_port', 9050);
        // Disable accepting SSL certificates
        //$caps->setCapability('acceptSslCerts', false);
        $profile->setPreference("dom.webdriver.enabled", false);
        $profile->setPreference('useAutomationExtension', false);
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);
       /* $caps->setCapability(
            'moz:firefoxOptions',
           ['args' => ['-headless']]
        );*/
        
        $driver = RemoteWebDriver::create($host, $caps);
        $height = 1080;
        $width = 1920;
        $d = new WebDriverDimension($width,$height);
        $driver->manage()->window()->setSize($d);
        $driver->get("https://www.couponbirds.com/");
        $list = $driver->manage()->getCookies();
        foreach ($list as $cookie) {
            //$domain = $cookie->getDomain();
            print_r($cookie);
            $tmp = $cookie['name'].':'.$cookie['value'];
            //file_put_contents(public_path('cc.txt'), $tmp.PHP_EOL, FILE_APPEND | LOCK_EX);
        }

       /* $cookies = file_get_contents(public_path('tt.txt'));
        $cookies = explode(PHP_EOL,$cookies);
        foreach ($cookies as $cookie) {

            $tmp = explode('=',$cookie);
            if(isset($tmp[1])){
                $tmp_ck = array('name' => $tmp[0], 'value' => $tmp[1]);
                $driver->manage()->addCookie($tmp_ck);
            }

        }*/
        //$driver->get("https://www.blogger.com/blogger.g#welcome");
        
        //echo "The title is '" . $driver->getTitle() . "'\n";
        /*$driver->findElement(WebDriverBy::cssSelector('.sign-in.ga-header-sign-in'))->click();
        sleep(5);
        $driver->findElement(WebDriverBy::id('identifierId'))->sendKeys('tovi250980@gmail.com');
        $driver->findElement(WebDriverBy::className('CwaK9'))->click();
        sleep(5);
        $driver->findElement(WebDriverBy::cssSelector('input[type="password"]'))->sendKeys("tovi092019");
        $driver->findElement(WebDriverBy::className('CwaK9'))->click();
        sleep(3);
        $urlAry = $driver->executeScript('return window.location',array());
        $currentURL = $urlAry['href'];
        print_r($currentURL);
        $driver->quit();*/
        
    }
    public function visitFromGoogle($keyword, $domain, $proxy){
        // parameter for action
        $userAgent = file_get_contents(public_path('UserAgent-Desktop.txt'));
        $userAgent = explode(PHP_EOL, $userAgent);
        $userAgentMobile = file_get_contents(public_path('UserAgent-Mobile.txt'));
        $userAgentMobile = explode(PHP_EOL, $userAgentMobile);

        $size = file_get_contents(public_path('Size-Desktop.txt'));
        $size = explode(PHP_EOL, $size);

        $sizeMobile = file_get_contents(public_path('Size-Mobile.txt'));
        $sizeMobile = explode(PHP_EOL, $sizeMobile);

        $timeSleep = random_int(5, 7);
        $timeSleep = $timeSleep*60;

        $actionScroll = random_int(3, 5);

        $check = random_int(1, 2);
        print_r('check:'.$check);
        if($check == 1){
            $postionUserAgentChange = random_int(0, count($userAgent) -1);
            $userAgentChange = $userAgent[$postionUserAgentChange];

            $postionSizeChange = random_int(0, count($size) -1);
            $sizeChange = $size[$postionSizeChange];
        } else {
            $postionUserAgentChange = random_int(0, count($userAgentMobile) -1);
            $userAgentChange = $userAgentMobile[$postionUserAgentChange];

            $postionSizeChange = random_int(0, count($sizeMobile) -1);
            $sizeChange = $sizeMobile[$postionSizeChange];
        }

        $sizeChange = explode(',', $sizeChange);
        $height = (int)$sizeChange[1];
        $width = (int)$sizeChange[0];

        //print_r($sizeChange);die;
    	$host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();
        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $options->setExperimentalOption("prefs", $prefs);
        // firefox
        $profile = new FirefoxProfile();
        $caps = DesiredCapabilities::firefox();
        if($proxy != false){
            
            $profile->setPreference('network.proxy.type', 1);
            # Set proxy to Tor client on localhost
            $profile->setPreference('network.proxy.socks', '104.248.64.188');
            $profile->setPreference('network.proxy.socks_port', 28982);
            
        }
        $profile->setPreference('general.useragent.override', $userAgentChange);
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
            $d = new WebDriverDimension($width,$height);
            $driver->manage()->window()->setSize($d);
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
        $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys(WebDriverKeys::ESCAPE);
        $driver->findElement(WebDriverBy::cssSelector('div.FPdoLc .gNO89b'))->click();
        sleep(5);
        $list = $driver->findElements(WebDriverBy::cssSelector('cite.iUh30'));
        $urls = array();
        $check = true;
        $driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');
        foreach ($list as $item) {
        	if(strpos($item->getText(), $domain) !== false){
        		print_r($item->getText());
        		$check = false;
                //$this->clickLink($driver, $item, $timeSleep, $actionScroll, $domain);
                die;
        		break;
        	}
        }
        if($check == true){
	        $pages = $driver->findElements(WebDriverBy::cssSelector('#navcnt .fl'));
	        foreach ($pages as $page) {
	        	$page->click();
	        	sleep(2);
	        	$list = $driver->findElements(WebDriverBy::cssSelector('cite.iUh30'));
                $urls = array();
                $check = true;
                foreach ($list as $item) {
                    if(strpos($item->getText(), $domain) !== false){
                        print_r($item->getText());
                        $this->clickLink($driver, $item, $timeSleep, $actionScroll, $domain);
                        $check = false;
                        break;
                    }
                }
	        }
    	}

    }
    public function checkIndex(){
        //$list =  DB::table('apktovi_index')->pluck('url');
        $list = DB::table('appid_video')->where('type',5)->where('checkIndex',-1)->pluck('appid');
        $tmp_list = array();
        $ss_tmp = array();
        $tmp_appid = array();
        foreach ($list as $ss) {
            $ss_tmp[] = $ss;
        }
        for ($i = 1; $i <= 30; $i++) {
            $urlListApp = 'http://api.tovicorp.com/listAppImage?page='.$i.'&size=1000';
            $listApp = $this->getRequest($urlListApp);
            //print_r($listApp);
            $listApp = json_decode($listApp, true);
            $listApp = $listApp['data'];
            

            foreach ($listApp as $tmp) {
                //echo $tmp['appid'];
                if(in_array($tmp['appid'], $ss_tmp) && !in_array($tmp['appid'], $tmp_appid)){
                    $tmp_list[] = $tmp;
                    $tmp_appid[] = $tmp['appid'];
                }
            }
        }
        //print_r(count($tmp_list));
        $listApp = $tmp_list;
        foreach ($listApp as $key => $app) {

            $res[] = $app;
            if($key %15 ==0){
                $this->checkIndexByTitle($res);
                $res = array();
            }
        }

    }
    public function checkIndexByTitle($listApp){
        //print_r(count($listApp));die;
        $time = date('Y-m-d H:i:s',time());
        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        // firefox
        $profile = new FirefoxProfile();
        $caps = DesiredCapabilities::firefox();
        $profile->setPreference('general.useragent.override', 'Mozilla/5.0 (Linux; Android 8.0; Pixel 2 Build/OPD3.170816.012) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Mobile Safari/537.36');
        $profile->setPreference('dom.webnotifications.enabled',false);
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);
        $proxy = true;
        $width = 500;
        $height = 1000;
        if($proxy != false){
            
            $profile->setPreference('network.proxy.type', 1);
            # Set proxy to Tor client on localhost
            $profile->setPreference('network.proxy.socks', '104.248.64.188');
            $profile->setPreference('network.proxy.socks_port', 28982);
            
        }
        if ($USE_FIREFOX)
        {
            $driver = RemoteWebDriver::create(
                $host, 
                $caps
            );
            $d = new WebDriverDimension($width,$height);
            $driver->manage()->window()->setSize($d);
        } else {
            $driver = RemoteWebDriver::create(
                $host, 
                $caps
            );
        }
        $driver->get("https://www.google.com?hl=en");
        $keyword = 'demo';
        $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys(WebDriverKeys::ENTER);
        //$driver->findElement(WebDriverBy::cssSelector('div.FPdoLc .gNO89b'))->click();
        //$driver->findElement(WebDriverBy::cssSelector('div.FPdoLc .gNO89b'))->click();
        sleep(5);
        try {
            //$html = $driver->findElement(WebDriverBy::id('main'))->getAttribute('innerHTML');
            $driver->findElement(WebDriverBy::id('main'))->getAttribute('innerHTML');
            foreach ($listApp as $app) {
                $path = public_path('gg_index/'.$app['appid'].'.png');
                $urlcheck = 'https://apktovi.com/'.$app['urltitle'].'-'.$app['appid'];
                //echo $urlcheck;
                $keyword = $app['title'].' apktovi';
                $info  = DB::table('appid_video')->where('appid',$app['appid'])->where('type',5)->first();
                try {
                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                    sleep(1);
                    $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
                    sleep(1);
                    $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                    sleep(5);
                    $html = $driver->findElement(WebDriverBy::id('main'))->getAttribute('innerHTML');
                    $dom = str_get_html($html);
                    $list = $dom->find('.mnr-c');
                    if($list){
                        $this->takeScreenshotImage($driver->findElement(WebDriverBy::cssSelector('body')), $driver, $path);
                        $urls = array();
                        $check = true;
                        //$driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');
                        $po = -2;
                        foreach ($list as $key => $item) {
                            //print_r($item->find('a',0)->href.PHP_EOL);

                            if($item->find('a',0)){
                                $url = $item->find('a',0)->href;
                                
                                if($url == $urlcheck){
                                    echo $url.PHP_EOL;
                                    $check = false;
                                    $po = $key + 1;
                                    break;
                                }
                            }
                        }
                        if($check == false){
                            $status = $po;
                        } else {
                            $status = -3;
                        }
                        
                    } else {
                        $status = -3;
                    }
                    if($info){
                        //DB::table('apktovi_index')->where('url',$keyword)->update(['status' => $status, 'updated_at' => $time]);
                        DB::table('appid_video')->where('appid',$app['appid'])->update(['checkIndex' => $status]);
                    } else {
                        //DB::table('apktovi_index')->insert(['status' => $status, 'created_at' => $time, 'updated_at' => $time,'url' => $keyword]);
                        DB::table('appid_video')->insertOrIngore(['type' => 5, 'created_at' => $time, 'updated_at' => $time,'státus' => 0,'error'=> 0, 'checkIndex' => $checkIndex]);
                    }
                } catch (\Exception $e2) {
                    echo 'e2'.$e2->getMessage();
                }
            }
                //die;
        } catch (\Exception $e) {
            echo 'e'.$e->getMessage();
            $urlAry = $driver->executeScript('return window.location',array());
            $currentURL = $urlAry['href'];
            $captcha = new AllInTitleController();
            $recaptchaToken = $captcha->recaptcha($currentURL);
            if($recaptchaToken != false){
                $driver->executeScript('document.getElementById("g-recaptcha-response").innerHTML = "'.$recaptchaToken.'"');
                $driver->executeScript('document.getElementById("captcha-form").submit()');
                sleep(10);
                try {
                    $html = $driver->findElement(WebDriverBy::id('main'))->getAttribute('innerHTML');
                    foreach ($listApp as $app) {
                        //$keyword = 'https://apktovi.com/'.$app['urltitle'].'-'.$app['appid'];
                        $keyword = $app['title'].' apktovi';
                        $info  = DB::table('appid_video')->where('url',$app['appid'])->where('type',5)->first();
                        try {
                            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                            sleep(1);
                            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
                            sleep(1);
                            $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                            sleep(5);
                            $html = $driver->findElement(WebDriverBy::id('main'))->getAttribute('innerHTML');
                            $dom = str_get_html($html);
                            $list = $dom->find('.r');
                            if($list){
                                $urls = array();
                                $check = true;
                                $driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');
                                foreach ($list as $key => $item) {
                                    //print_r($item->find('a',0)->href.PHP_EOL);
                                    $url = $item->find('a',0)->href;
                                    echo $url;
                                    if($url == $keyword && $key == 0){
                                        $check = false;
                                        break;
                                    }
                                    /*if(strpos($item->getText(), $domain) !== false){
                                        
                                        $check = false;
                                        //$this->clickLink($driver, $item, $timeSleep, $actionScroll, $domain);
                                        break;
                                    }*/
                                }
                                if($check == false){
                                    $status = 1;
                                } else {
                                    $status = 0;
                                }
                                
                            } else {
                                $status = 0;
                            }
                            if($info){
                                //DB::table('apktovi_index')->where('url',$keyword)->update(['status' => $status, 'updated_at' => $time]);
                                DB::table('appid_video')->where('appid',$app['appid'])->update(['checkIndex' => $status]);
                            } else {
                                //DB::table('apktovi_index')->insert(['status' => $status, 'created_at' => $time, 'updated_at' => $time,'url' => $keyword]);
                                DB::table('appid_video')->where('appid',$app['appid'])->update(['checkIndex' => $status]);
                            }
                        } catch (\Exception $e) {
                            
                        }
                        
                        //die;
                    }
                } catch (\Exception $e1) {
                    $urlAry = $driver->executeScript('return window.location',array());
                    $currentURL = $urlAry['href'];
                    if($currentURL == 'https://www.google.com/sorry/index'){
                        //$this->updateKey($listKey[0]['keyword_id'], -2);
                    } else {
                        foreach ($listApp as $app) {
                            $path = public_path('gg_index/'.$app['appid'].'.png');
                            $urlcheck = 'https://apktovi.com/'.$app['urltitle'].'-'.$app['appid'];
                            //echo $urlcheck;
                            $keyword = $app['title'].' apktovi';
                            $info  = DB::table('appid_video')->where('appid',$app['appid'])->where('type',5)->first();
                            try {
                                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
                                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
                                sleep(1);
                                $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
                                sleep(1);
                                $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
                                sleep(5);
                                $html = $driver->findElement(WebDriverBy::id('main'))->getAttribute('innerHTML');
                                $dom = str_get_html($html);
                                $list = $dom->find('.mnr-c');
                                if($list){
                                    $this->takeScreenshotImage($driver->findElement(WebDriverBy::cssSelector('body')), $driver, $path);
                                    $urls = array();
                                    $check = true;
                                    //$driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');
                                    $po = -2;
                                    foreach ($list as $key => $item) {
                                        //print_r($item->find('a',0)->href.PHP_EOL);
                                        if($item->find('a',0)){
                                            $url = $item->find('a',0)->href;
                                            
                                            if($url == $urlcheck){
                                                echo $url.PHP_EOL;
                                                $check = false;
                                                $po = $key + 1;
                                                break;
                                            }
                                        }
                                    }
                                    if($check == false){
                                        $status = $po;
                                    } else {
                                        $status = -3;
                                    }
                                    
                                } else {
                                    $status = -3;
                                }
                                if($info){
                                    //DB::table('apktovi_index')->where('url',$keyword)->update(['status' => $status, 'updated_at' => $time]);
                                    DB::table('appid_video')->where('appid',$app['appid'])->update(['checkIndex' => $status]);
                                } else {
                                    //DB::table('apktovi_index')->insert(['status' => $status, 'created_at' => $time, 'updated_at' => $time,'url' => $keyword]);
                                    DB::table('appid_video')->insertOrIngore(['type' => 5, 'created_at' => $time, 'updated_at' => $time,'státus' => 0,'error'=> 0, 'checkIndex' => $checkIndex]);
                                }
                            } catch (\Exception $e3) {
                                
                            }
                            
                            //die;
                        }
                    }
                    // unset($listKey[0]);
                }
            }
        }
    }
    public function takeScreenshotImage($element=null, $driver, $path, $mobile = true){
        $screenshot = $path;



        if( ! (bool) $element) {
            return $screenshot;
        }


        $element_screenshot = $path; // Change the path here as well

        
        $element_width = $element->getSize()->getWidth();
        $element_height = $element->getSize()->getHeight();
        
        $element_src_x = $element->getLocation()->getX();
        $element_src_y = $element->getLocation()->getY();

        // Change the driver instance
        $driver->takeScreenshot($screenshot);
        if(! file_exists($screenshot)) {
            throw new Exception('Could not save screenshot');
        }
        
        // Create image instances
        $src = imagecreatefrompng($screenshot);
        if($mobile == true){
            $dest = imagecreatetruecolor(482, 854);
        } else {
            $dest = imagecreatetruecolor(1920, 1000);
        }
        // Copy
        if($mobile == true){
            imagecopy($dest, $src, 0, 0, (int) ceil($element_src_x), (int) ceil($element_src_y), (int) ceil(482), (int) ceil(854));
        } else {
            imagecopy($dest, $src, 0, 0, ceil($element_src_x), (int) ceil($element_src_y), (int) ceil(1920), (int) ceil(1000));
        }
        //imagecopy($dest, $src, 0, 0, (int) ceil($element_src_x), (int) ceil($element_src_y), (int) ceil($element_width), (int) ceil($element_height));
        imagepng($dest, $element_screenshot);
        
        // unlink($screenshot); // unlink function might be restricted in mac os x.
        
        if( ! file_exists($element_screenshot)) {
            throw new Exception('Could not save element screenshot');
        }
        
        return $element_screenshot;
    }
    // public function runcheckIndex($listApp){
        
    //     $host = 'http://localhost:4444/wd/hub'; // this is the default
    //     $USE_FIREFOX = true; // if false, will use chrome.
    //     $caps = DesiredCapabilities::chrome();
    //     $prefs = array();
    //     $options = new ChromeOptions();
    //     $prefs['profile.default_content_setting_values.notifications'] = 2;
    //     $options->setExperimentalOption("prefs", $prefs);
    //     // firefox
    //     $profile = new FirefoxProfile();
    //     $caps = DesiredCapabilities::firefox();
    //     $proxy = true;
    //     if($proxy != false){
            
    //         $profile->setPreference('network.proxy.type', 1);
    //         # Set proxy to Tor client on localhost
    //         $profile->setPreference('network.proxy.socks', '104.248.64.188');
    //         $profile->setPreference('network.proxy.socks_port', 28982);
            
    //     }
    //     //$profile->setPreference('general.useragent.override', $userAgentChange);
    //     $caps->setCapability(FirefoxDriver::PROFILE, $profile);
    //     //$caps->setCapability(ChromeOptions::CAPABILITY, $options);
    //     // $capabilities = [
    //     //     WebDriverCapabilityType::BROWSER_NAME => 'firefox',
    //     //     WebDriverCapabilityType::PROXY => [
    //     //         'proxyType' => 'manual',
    //     //         'socksProxy' => '104.248.64.188:28982',
    //     //         //'sslProxy' => '127.0.0.1:2043',
    //     //     ],
    //     // ];
    //     if ($USE_FIREFOX)
    //     {
    //         $driver = RemoteWebDriver::create(
    //             $host, 
    //             $caps
    //         );
    //         //$d = new WebDriverDimension($width,$height);
    //         //$driver->manage()->window()->setSize($d);
    //     }
    //     else
    //     {
    //         $driver = RemoteWebDriver::create(
    //             $host, 
    //             $caps
    //         );
    //     }
       
    //     //$driver->get('https://whatismyip.com');die;
    //     # enter text into the search field
    //     $time = date('Y-m-d H:i:s');
    //     $driver->get("https://www.google.com?hl=en");
    //     //$keyword = 'https://apktovi.com/'.$listApp[0]['urltitle'].'-'.$listApp[0]['appid'];
    //     $keyword = $listApp[0]['title'].' apktovi';
    //     $info  = DB::table('apktovi_index')->where('url',$keyword)->first();
        
    //     $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
    //     sleep(1);
    //     $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
    //     sleep(1);
    //     $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys(WebDriverKeys::ESCAPE);
    //     $driver->findElement(WebDriverBy::cssSelector('div.FPdoLc .gNO89b'))->click();
    //     sleep(5);
    //     try {
    //         $html = $driver->findElement(WebDriverBy::id('main'))->getAttribute('innerHTML');
    //         sleep(3);
    //         $dom = str_get_html($html);
    //         $list = $dom->find('.r');
    //         if($list){
    //             $urls = array();
    //             $check = true;
    //             $driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');
    //             foreach ($list as $key => $item) {
    //                 //print_r($item->find('a',0)->href.PHP_EOL);
    //                 $url = $item->find('a',0)->href;
    //                 echo $url;
    //                 if($url == $keyword && $key == 0){
    //                     $check = false;
    //                     break;
    //                 }
    //                 /*if(strpos($item->getText(), $domain) !== false){
                        
    //                     $check = false;
    //                     //$this->clickLink($driver, $item, $timeSleep, $actionScroll, $domain);
    //                     break;
    //                 }*/
    //             }
    //             if($check == false){
    //                 $status = 1;
    //             } else {
    //                 $status = 0;
    //             }
    //             if($info){
    //                 //DB::table('apktovi_index')->where('url',$keyword)->update(['status' => $status, 'updated_at' => $time]);
    //                 DB::table('appid_video')->where('appid',$listApp[0]['appid'])->update(['checkIndex' => $status]);
    //             } else {
    //                 //DB::table('apktovi_index')->insert(['status' => $status, 'created_at' => $time, 'updated_at' => $time,'url' => $keyword]);
    //                 DB::table('appid_video')->where('appid',$listApp[0]['appid'])->update(['checkIndex' => $status]);
    //             }
    //         }
    //         unset($listApp[0]);
    //         foreach ($listApp as $app) {
    //             //$keyword = 'https://apktovi.com/'.$app['urltitle'].'-'.$app['appid'];
    //             $keyword = $app['title'].' apktovi';
    //             $info  = DB::table('apktovi_index')->where('url',$keyword)->first();
    //             try {
    //                 $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
    //                 $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
    //                 sleep(1);
    //                 $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
    //                 sleep(1);
    //                 $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
    //                 sleep(5);
    //                 $html = $driver->findElement(WebDriverBy::id('main'))->getAttribute('innerHTML');
    //                 $dom = str_get_html($html);
    //                 $list = $dom->find('.r');
    //                 if($list){
    //                     $urls = array();
    //                     $check = true;
    //                     $driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');
    //                     foreach ($list as $key => $item) {
    //                         //print_r($item->find('a',0)->href.PHP_EOL);
    //                         $url = $item->find('a',0)->href;
    //                         echo $url;
    //                         if($url == $keyword && $key == 0){
    //                             $check = false;
    //                             break;
    //                         }
    //                         /*if(strpos($item->getText(), $domain) !== false){
                                
    //                             $check = false;
    //                             //$this->clickLink($driver, $item, $timeSleep, $actionScroll, $domain);
    //                             break;
    //                         }*/
    //                     }
    //                     if($check == false){
    //                         $status = 1;
    //                     } else {
    //                         $status = 0;
    //                     }
                        
    //                 } else {
    //                     $status = 0;
    //                 }
    //                 if($info){
    //                     //DB::table('apktovi_index')->where('url',$keyword)->update(['status' => $status, 'updated_at' => $time]);
    //                     DB::table('appid_video')->where('appid',$app['appid'])->update(['checkIndex' => $status]);
    //                 } else {
    //                     //DB::table('apktovi_index')->insert(['status' => $status, 'created_at' => $time, 'updated_at' => $time,'url' => $keyword]);
    //                     DB::table('appid_video')->where('appid',$app['appid'])->update(['checkIndex' => $status]);
    //                 }
    //             } catch (\Exception $e) {
                    
    //             }
                
    //             //die;
    //         }
    //     } catch (\Exception $e) {
    //         $urlAry = $driver->executeScript('return window.location',array());
    //         $currentURL = $urlAry['href'];
    //         $captcha = new AllInTitleController();
    //         $recaptchaToken = $captcha->recaptcha($currentURL);
    //         if($recaptchaToken != false){
    //             $driver->executeScript('document.getElementById("g-recaptcha-response").innerHTML = "'.$recaptchaToken.'"');
    //             $driver->executeScript('document.getElementById("captcha-form").submit()');
    //             sleep(10);
    //             try {
    //                 $html = $driver->findElement(WebDriverBy::id('main'))->getAttribute('innerHTML');
    //                 $dom = str_get_html($html);
    //                 $list = $dom->find('.r');
    //                 if($list){
    //                     $urls = array();
    //                     $check = true;
    //                     $driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');
    //                     foreach ($list as $key => $item) {
    //                         //print_r($item->find('a',0)->href.PHP_EOL);
    //                         $url = $item->find('a',0)->href;
    //                         echo $url;
    //                         if($url == $keyword && $key == 0){
    //                             $check = false;
    //                             break;
    //                         }
    //                         /*if(strpos($item->getText(), $domain) !== false){
                                
    //                             $check = false;
    //                             //$this->clickLink($driver, $item, $timeSleep, $actionScroll, $domain);
    //                             break;
    //                         }*/
    //                     }
    //                     if($check == false){
    //                         $status = 1;
    //                     } else {
    //                         $status = 0;
    //                     }
    //                     if($info){
    //                         DB::table('appid_video')->where('appid',$listApp[0]['appid'])->update(['checkIndex' => $status]);
    //                         //DB::table('apktovi_index')->where('url',$keyword)->update(['status' => $status, 'updated_at' => $time]);
    //                     } else {
    //                         //DB::table('apktovi_index')->insert(['status' => $status, 'created_at' => $time, 'updated_at' => $time,'url' => $keyword]);
    //                         DB::table('appid_video')->where('appid',$listApp[0]['appid'])->update(['checkIndex' => $status]);
    //                     }
    //                 }
    //                 foreach ($listApp as $app) {
    //                     //$keyword = 'https://apktovi.com/'.$app['urltitle'].'-'.$app['appid'];
    //                     $keyword = $app['title'].' apktovi';
    //                     $info  = DB::table('apktovi_index')->where('url',$keyword)->first();
    //                     try {
    //                         $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
    //                         $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
    //                         sleep(1);
    //                         $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
    //                         sleep(1);
    //                         $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
    //                         sleep(5);
    //                         $html = $driver->findElement(WebDriverBy::id('main'))->getAttribute('innerHTML');
    //                         $dom = str_get_html($html);
    //                         $list = $dom->find('.r');
    //                         if($list){
    //                             $urls = array();
    //                             $check = true;
    //                             $driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');
    //                             foreach ($list as $key => $item) {
    //                                 //print_r($item->find('a',0)->href.PHP_EOL);
    //                                 $url = $item->find('a',0)->href;
    //                                 echo $url;
    //                                 if($url == $keyword && $key == 0){
    //                                     $check = false;
    //                                     break;
    //                                 }
    //                                 /*if(strpos($item->getText(), $domain) !== false){
                                        
    //                                     $check = false;
    //                                     //$this->clickLink($driver, $item, $timeSleep, $actionScroll, $domain);
    //                                     break;
    //                                 }*/
    //                             }
    //                             if($check == false){
    //                                 $status = 1;
    //                             } else {
    //                                 $status = 0;
    //                             }
                                
    //                         } else {
    //                             $status = 0;
    //                         }
    //                         if($info){
    //                             //DB::table('apktovi_index')->where('url',$keyword)->update(['status' => $status, 'updated_at' => $time]);
    //                             DB::table('appid_video')->where('appid',$app['appid'])->update(['checkIndex' => $status]);
    //                         } else {
    //                             //DB::table('apktovi_index')->insert(['status' => $status, 'created_at' => $time, 'updated_at' => $time,'url' => $keyword]);
    //                             DB::table('appid_video')->where('appid',$app['appid'])->update(['checkIndex' => $status]);
    //                         }
    //                     } catch (\Exception $e) {
                            
    //                     }
                        
    //                     //die;
    //                 }
    //             } catch (\Exception $e1) {
    //                 $urlAry = $driver->executeScript('return window.location',array());
    //                 $currentURL = $urlAry['href'];
    //                 if($currentURL == 'https://www.google.com/sorry/index'){
    //                     //$this->updateKey($listKey[0]['keyword_id'], -2);
    //                 } else {

    //                     foreach ($listApp as $app) {

    //                         $keyword = 'https://apktovi.com/'.$app['urltitle'].'-'.$app['appid'];
    //                         $info  = DB::table('apktovi_index')->where('url',$keyword)->first();
    //                         try {
    //                             $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->clear();
    //                             $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
    //                             sleep(1);
    //                             $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys($keyword);
    //                             sleep(1);
    //                             $driver->findElement(WebDriverBy::cssSelector('button.Tg7LZd'))->click();
    //                             sleep(5);
    //                             $html = $driver->findElement(WebDriverBy::id('main'))->getAttribute('innerHTML');
    //                             $dom = str_get_html($html);
    //                             $list = $dom->find('.r');
    //                             if($list){
    //                                 $urls = array();
    //                                 $check = true;
    //                                 $driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');
    //                                 foreach ($list as $key => $item) {
    //                                     //print_r($item->find('a',0)->href.PHP_EOL);
    //                                     $url = $item->find('a',0)->href;
    //                                     echo $url;
    //                                     if($url == $keyword && $key == 0){
    //                                         $check = false;
    //                                         break;
    //                                     }
    //                                     /*if(strpos($item->getText(), $domain) !== false){
                                            
    //                                         $check = false;
    //                                         //$this->clickLink($driver, $item, $timeSleep, $actionScroll, $domain);
    //                                         break;
    //                                     }*/
    //                                 }
    //                                 if($check == false){
    //                                     $status = 1;
    //                                 } else {
    //                                     $status = 0;
    //                                 }
                                    
    //                             } else {
    //                                 $status = 0;
    //                             }
    //                             if($info){
    //                                 //DB::table('apktovi_index')->where('url',$keyword)->update(['status' => $status, 'updated_at' => $time]);
    //                                 DB::table('appid_video')->where('appid',$app['appid'])->update(['checkIndex' => $status]);
    //                             } else {
    //                                 //DB::table('apktovi_index')->insert(['status' => $status, 'created_at' => $time, 'updated_at' => $time,'url' => $keyword]);
    //                                 DB::table('appid_video')->where('appid',$app['appid'])->update(['checkIndex' => $status]);
    //                             }
    //                         } catch (\Exception $e) {
                                
    //                         }
                            
    //                         //die;
    //                     }
    //                 }
    //                 // unset($listKey[0]);
    //             }
    //         }
        
    //     }
        
    // }
    public function clickLink($driver, $item, $time, $actionScroll, $domain){
        $item->click();
        $loop = round($time/($actionScroll*60));
        $height = $driver->executeScript("return document.body.scrollHeight")/$actionScroll;
        $driver->executeScript('window.scrollTo(0,'.($height/2).');');
        sleep($time/2);
        $driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');
        sleep($time/2);
        $driver->close();

    }
    public function checkLink($driver, $domain){
    	$list = $driver->findElements(WebDriverBy::cssSelector('cite.iUh30'));
        $urls = array();
        $check = true;
        foreach ($list as $item) {
        	if(strpos($item->getText(), $domain) !== false){
        		print_r($item->getText());
        		$check = false;
        		break;
        	}
        }
        return $check;
    }
    public function addProxy(){
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
        $profile->setPreference('network.proxy.socks', '104.248.64.188');
        $profile->setPreference('network.proxy.socks_port', 28982);
        
        $caps = DesiredCapabilities::firefox();
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);

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
        $driver->get("https://socks24h.com/login.jsp");
        //$driver->get('https://whatismyip.com');die;
        # enter text into the search field
        $list = $driver->findElements(WebDriverBy::cssSelector('input.form-control'));
        $list[0]->click();
        sleep(1);
        $list[0]->sendKeys('thanhtoanlc9');
        sleep(1);
        $list[1]->click();
        sleep(1);
        $list[1]->sendKeys('Toan1234@');
        sleep(1);
        $driver->findElement(WebDriverBy::cssSelector('button.btn'))->click();
        $driver->get("https://socks24h.com/list.jsp");
        sleep(5);
        $driver->findElement(WebDriverBy::id('your-list'))->click();
        sleep(2);
        $html = $driver->findElement(WebDriverBy::cssSelector('#tableview'));
        $dom = str_get_html($html->getAttribute('innerHTML'));
        $listproxy = $dom->find('tbody tr');
        foreach ($listproxy as $proxy) {
            $tmp = $proxy->find('td',0);
            $ip = $tmp->find('a',0)->plaintext;
            //print_r($ip.PHP_EOL);
            $ip = explode(':', $ip);
            $created_at = date('Y-m-d',time());
            $count = DB::table('proxy')->where('ip',$ip[0])->count();
            if($count == 0){
                DB::table('proxy')->insert(['ip' => $ip[0],'port' => $ip[1],'status' => 0, 'created_at' => $created_at]);
            } else {
                echo 'duplicate';
            }
        }
        $page = $driver->findElement(WebDriverBy::id('tableview_info'));
        $text = $page->getText();
        $text = str_replace('Showing page 1 of ', '', $text);
        $text = explode('-', $text);
        $totalpage = $text[0];
        for($i = 2;$i<= (int)$totalpage;$i++){
            $driver->findElement(WebDriverBy::id('tableview_next'))->click();
            sleep(5);
            $html = $driver->findElement(WebDriverBy::cssSelector('#tableview'));
            $dom = str_get_html($html->getAttribute('innerHTML'));
            $listproxy = $dom->find('tbody tr');
            foreach ($listproxy as $proxy) {
                $tmp = $proxy->find('td',0);
                $ip = $tmp->find('a',0)->plaintext;
                $ip = explode(':', $ip);
                $created_at = date('Y-m-d',time());
                $count = DB::table('proxy')->where('ip',$ip[0])->count();
                if($count == 0){
                    DB::table('proxy')->insert(['ip' => $ip[0],'port' => $ip[1],'status' => 0,'created_at' => $created_at]);
                } else {
                    echo 'duplicate';
                }
            }
            
        }
        //print_r($totalpage);
        //$urls = array();
        //$check = true;
        //$driver->executeScript('window.scrollTo(0,document.body.scrollHeight);');

    }
    public function checkProxy(){
        $list = DB::table('proxy')->get();
        foreach ($list as $proxy) {
            $host = 'http://localhost:4444/wd/hub'; // this is the default
            $USE_FIREFOX = true; // if false, will use chrome.
            $caps = DesiredCapabilities::chrome();
            $prefs = array();
            $options = new ChromeOptions();
            $prefs['profile.default_content_setting_values.notifications'] = 2;
            $options->setExperimentalOption("prefs", $prefs);
            // firefox
            // $profile = new FirefoxProfile();
            // $profile->setPreference('network.proxy.type', 1);
            // # Set proxy to Tor client on localhost
            // $profile->setPreference('network.proxy.socks', '104.248.64.188');
            // $profile->setPreference('network.proxy.socks_port', 28982);
            
            $caps = DesiredCapabilities::firefox();
            // $caps->setCapability(FirefoxDriver::PROFILE, $profile);

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
            $driver->get("https://whatismyipaddress.com/ip-lookup");
            //$driver->get('https://whatismyip.com');die;
            # enter text into the search field
            $list = $driver->findElements(WebDriverBy::cssSelector('#section_left_3rd input'));
            $list[0]->click();
            sleep(1);
            $list[0]->clear();
            sleep(1);
            $list[0]->sendKeys($proxy->ip);
            sleep(1);
            $list[1]->click();
            sleep(5);
            $html = $driver->findElements(WebDriverBy::cssSelector('#section_left_3rd form'));
            sleep(2);
            $dom = str_get_html($html[1]->getAttribute('innerHTML'));
            $table = $dom->find('table',0);
            $listTr = $table->find('tr');
            foreach ($listTr as $tr) {
                $th = $tr->find('th',0)->plaintext;
                $td = $tr->find('td',0)->plaintext;
                print_r($td.PHP_EOL);
                if($th == 'ISP:'){
                    $isp = $td;
                }
                
            }
            $html = $driver->findElements(WebDriverBy::cssSelector('#section_left_3rd table'));
            $dom = str_get_html($html[1]->getAttribute('innerHTML'));
            $listTr = $dom->find('tr');
            foreach ($listTr as $tr) {
                $th = $tr->find('th',0)->plaintext;
                $td = $tr->find('td',0)->plaintext;
                print_r($td.PHP_EOL);
                if($th == 'Country:'){
                    $country = $td;
                }
                
            }
            DB::table('proxy')->where('proxy_id',$proxy->proxy_id)->update(['isp' => $isp,'country' => $country,'status' =>1]);
            //die;
            $driver->close();
        }
        

    }
    public function requestIp(){
        $list = DB::table('proxy')->where('status',0)->get();
        foreach ($list as $proxy) {
            $url = 'http://ip-api.com/json/';
            $res = $this->getRequest($url.$proxy->ip);
            $data = json_decode($res,true);
            DB::table('proxy')->where('proxy_id',$proxy->proxy_id)->update(['isp' => $data['isp'],'country' => $data['country'],'status' => 1]);
        }
    }
    public function checkLive(){
        $list = DB::table('proxy')->get();
        foreach ($list as $proxy) {
            $check = $this->test($proxy->ip.':'.$proxy->port);
            if($check == true){
                $status = 1;
            } else {
                $status = 0;
            }
            DB::table('proxy')->where('proxy_id',$proxy->proxy_id)->update(['die' => $status,'updated_at' => date('Y-m-d H:i:s',time())]);
        }
    }
    public function test($proxy)
    {
      global $fisier;
      $splited = explode(':',$proxy); // Separate IP and port
      if($con = @fsockopen($splited[0], $splited[1], $eroare, $eroare_str, 3)) 
      {
        //fwrite($fisier, $proxy . "\n"); // Check if we can connect to that IP and port
        //print $proxy . '<br>'; // Show the proxy
        fclose($con); // Close the socket handle
        return true;
      }
      return false;
    }

    public function getRequest($url){
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_SSL_VERIFYHOST => 0,
          CURLOPT_SSL_VERIFYPEER => 0
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        echo $err;
        curl_close($curl);
        return $response;

    }
    // ahref
    public function exportAhref($domain_check){
        $host = 'http://localhost:4444/wd/hub'; // this is the default
        $profile = new FirefoxProfile();
        /*$caps = DesiredCapabilities::firefox();
        $profile->setPreference('general.useragent.override', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36');
        $profile->setPreference("dom.webdriver.enabled", false);
        $profile->setPreference('useAutomationExtension', false);
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);*/
        $caps = DesiredCapabilities::chrome();
        $driver = RemoteWebDriver::create(
            $host,
            $caps
        );
        $d = new WebDriverDimension(1920, 1080);
        $driver->manage()->window()->setSize($d);
        $driver->get("http://tool.buyseotools.io");
        $driver->manage()->deleteAllCookies();
        $tmp_ck = array('name' => 'buyseotools', 'value' => '0c53af54df958733f3fb3dc633d3db27');
        $driver->manage()->addCookie($tmp_ck);
        $tmp_ck = array('name' => '_ga', 'value' => 'GA1.2.1640273708.1573832609');
        $driver->manage()->addCookie($tmp_ck);
        $tmp_ck = array('name' => '_gid', 'value' => 'GA1.2.557383215.1585207398');
        $driver->manage()->addCookie($tmp_ck);
        $tmp_ck = array('name' => '_gat', 'value' => '1');
        $driver->manage()->addCookie($tmp_ck);

        $driver->get("http://tool.buyseotools.io/ahrefs.php");
        $link  = $driver->findElement(WebDriverBy::partialLinkText('Truy cập vào Ahrefs Tools'))->getAttribute('href');

        $link = str_replace('http://','',$link);
        $domain = str_replace('/site-explorer','',$link);

        $tmp_ck = array('name' => 'buyseotools', 'value' => '0c53af54df958733f3fb3dc633d3db27','domain' => $domain);
        $driver->manage()->addCookie($tmp_ck);
        $tmp_ck = array('name' => '_ga', 'value' => 'GA1.2.1640273708.1573832609','domain' => $domain);
        $driver->manage()->addCookie($tmp_ck);
        $tmp_ck = array('name' => '_gid', 'value' => 'GA1.2.557383215.1585207398','domain' => $domain);
        $driver->manage()->addCookie($tmp_ck);
        $tmp_ck = array('name' => '_gat', 'value' => '1','domain' => $domain);
        $driver->manage()->addCookie($tmp_ck);

        $driver->findElement(WebDriverBy::partialLinkText('Truy cập vào Ahrefs Tools'))->click();
        sleep(10);
        $driver->findElement(WebDriverBy::id('se_index_target'))->sendKeys($domain_check);
        sleep(1);
        $driver->findElement(WebDriverBy::id('se_index_target'))->sendKeys(WebDriverKeys::ENTER);
        sleep(10);
        $min_vl = 10;
        $max_vl = 100;
        $min_position = 1;
        $max_position = 100;
        $min_key = 1;
        $max_key = 7;
        $vl = '10-1000';
        $url = 'http://'.$domain.'/positions-explorer/organic-keywords/v5/subdomains/us/{position}/{vl}/0-1/all/all/{key}/all/1/traffic_desc?target='.$domain_check;
        $url_run = str_replace('{position}','all',$url);
        $url_run = str_replace('{key}','all',$url_run);
        $url_run = str_replace('{vl}','all',$url_run);
        $driver->get($url_run);
        $total = $driver->findElement(WebDriverBy::cssSelector("div#PERegionsFirstList a.active"))->getText();
        $total = str_replace(',', '', $total);
        $total = intval($total);
        for ($i=$min_vl; $i <= $max_vl ; $i=$i+10) { 
            $url_new = $url;
            $vl = $i.'-'.($i);
            $url_new = str_replace('{position}','all',$url);
            $url_new = str_replace('{key}','all',$url_new);
            $url_new = str_replace('{vl}',$i.'-'.($i),$url_new);
            $driver->get($url_new);
            $total = $driver->findElement(WebDriverBy::cssSelector("div#PERegionsFirstList a.active"))->getText();
            $total = str_replace(',', '', $total);
            $total = intval($total); 
            if($total > 100000){
                $url_new = $url;
                $url_new = str_replace('{position}','all',$url);
                $url_new = str_replace('{vl}',$i.'-'.($i),$url_new);
                for ($j=1; $j <= 7; $j++) { 
                    $key = $j.'-'.($j);
                    $url_new = $url;
                    $url_new = str_replace('{position}','all',$url);
                    $url_new = str_replace('{vl}',$i.'-'.($i),$url_new);
                    if($j == 7){
                        $url_new = str_replace('{key}',$j.'-0',$url_new);
                    } else {
                        $url_new = str_replace('{key}',$j.'-'.$j,$url_new);
                    }
                    $driver->get($url_new);
                    $total = $driver->findElement(WebDriverBy::cssSelector("div#PERegionsFirstList a.active"))->getText();
                    $total = str_replace(',', '', $total);
                    $total = intval($total); 
                    if($total == 0){
                        break;
                    }
                    if($total > 0){
                        $driver->findElement(WebDriverBy::cssSelector("a.export-data"))->click();
                        sleep(10);
                        $driver->findElement(WebDriverBy::id("start_custom_export"))->click();
                        sleep(1);
                        $driver->findElement(WebDriverBy::id("start_export_button"))->click();
                        sleep(20);
                        
                    }
                }
            } else {
                $driver->findElement(WebDriverBy::cssSelector("a.export-data"))->click();
                sleep(10);
                $driver->findElement(WebDriverBy::id("start_custom_export"))->click();
                sleep(1);
                $driver->findElement(WebDriverBy::id("start_export_button"))->click();
                sleep(20);
            }
        }
    }
}
