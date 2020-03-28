<?php

namespace App\Http\Controllers;

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
ini_set('memory_limit',-1);
class ScreenShotApi extends Controller
{
    //
    public function screenShot(){
    	$proxy = false;
    	$host = 'http://localhost:4444/wd/hub'; // this is the default

        $USE_FIREFOX = true; // if false, will use chrome.
        // $caps = DesiredCapabilities::chrome();
        // $prefs = array();
        // $options = new ChromeOptions();
        // $prefs['profile.default_content_setting_values.notifications'] = 2;
        // $options->setExperimentalOption("prefs", $prefs);
        // firefox
        $profile = new FirefoxProfile();
        
        if($proxy != false){
            
            $profile->setPreference('network.proxy.type', 1);
            # Set proxy to Tor client on localhost
            $profile->setPreference('network.proxy.socks', '104.248.64.188');
            $profile->setPreference('network.proxy.socks_port', 28982);
            

        }
        $caps = DesiredCapabilities::firefox();
        $profile->setPreference('general.useragent.override', 'Mozilla/5.0 (Linux; Android 4.0.4; Galaxy Nexus Build/IMM76B) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.133 Mobile Safari/535.19');

        
        //$profile->setPreference('general.useragent.override', $userAgentChange);
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
        $width = 500;
        $height = 1000;
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
            $d = new WebDriverDimension($width,$height);
            $driver->manage()->window()->setSize($d);
        }

        $driver->get("https://apktovi.com?video=1");
        $list = $this->getData(1, 1900);
        $list = $list['data'];
        //echo count($list);die;
        foreach ($list as $data) {
            //print_r($data);die;
            if(isset($data['status_full']) && $data['status_full'] == 100){
                $path = public_path('variants/'.$data['appid']. "_2.png");
                $file_name = public_path('variants/'.$data['appid'].'.txt');
                if(is_file(public_path('variants/'.$data['appid']. "_2.png"))){
                    $file_name = public_path('variants/'.$data['appid'].'.txt');
                	$driver->get("https://apktovi.com/".$data['urltitle'].'-'.$data['appid'].'?video=1');
                    $output = $driver->executeScript('return $(".detail-app").height()');
                                        //print_r($output);
                    file_put_contents($file_name, $output.PHP_EOL , FILE_APPEND | LOCK_EX);
                }
            }
        	//die;


        }
        
    }
    public function TakeScreenshot($element=null, $appid, $driver) {
        // Change the Path to your own settings

        $screenshot = public_path('variants/'.$appid. ".png");



        if( ! (bool) $element) {
            return $screenshot;
        }


        $element_screenshot = public_path('variants/'.$appid. ".png"); // Change the path here as well

        
        $element_width = $element->getSize()->getWidth();
        $element_height = $element->getSize()->getHeight();
        
        $element_src_x = $element->getLocationOnScreenOnceScrolledIntoView()->getX();
        $element_src_y = $element->getLocationOnScreenOnceScrolledIntoView()->getY();

        // Change the driver instance
        $driver->takeScreenshot($screenshot);
        if(! file_exists($screenshot)) {
            throw new Exception('Could not save screenshot');
        }
        
        // Create image instances
        $src = imagecreatefrompng($screenshot);
        $dest = imagecreatetruecolor(482, 854);

        // Copy
        imagecopy($dest, $src, 0, 0, (int) ceil($element_src_x), (int) ceil($element_src_y), (int) ceil(482), (int) ceil(854));
        
        imagepng($dest, $element_screenshot);
        
        // unlink($screenshot); // unlink function might be restricted in mac os x.
        
        if( ! file_exists($element_screenshot)) {
            throw new Exception('Could not save element screenshot');
        }
        
        return $element_screenshot;
    }
    public function getData($page, $limit){
    	$curl = curl_init();

		curl_setopt_array($curl, array(

		  //CURLOPT_URL => "http://159.69.156.39/api-video/public/api/video?page=".$page."&limit=".$limit,
          CURLOPT_URL => "http://api.tovicorp.com/listAppVideo?page=".$page."&size=".$limit.'&type=0&variant=0',
		  CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);
        //print_r($response);
		return json_decode($response,true);
    }
    public function getAppApkTovi($page, $limit){
        $curl = curl_init();

        curl_setopt_array($curl, array(

          //CURLOPT_URL => "http://159.69.156.39/api-video/public/api/video?page=".$page."&limit=".$limit,
          CURLOPT_URL => "http://api.tovicorp.com/listAppImage?page=".$page."&size=".$limit,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        //print_r($response);
        return json_decode($response,true);
    }
    public function takeScreenshotImage($element=null, $appid, $driver, $path, $mobile = true){
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
    public function screenShotMobile($list){
        $proxy = false;
        $host = 'http://localhost:4444/wd/hub'; // this is the default

        $USE_FIREFOX = true; // if false, will use chrome.
        // $caps = DesiredCapabilities::chrome();
        // $prefs = array();
        // $options = new ChromeOptions();
        // $prefs['profile.default_content_setting_values.notifications'] = 2;
        // $options->setExperimentalOption("prefs", $prefs);
        // firefox
        $profile = new FirefoxProfile();
        
        if($proxy != false){
            
            $profile->setPreference('network.proxy.type', 1);
            # Set proxy to Tor client on localhost
            $profile->setPreference('network.proxy.socks', '104.248.64.188');
            $profile->setPreference('network.proxy.socks_port', 28982);
            

        }
        $caps = DesiredCapabilities::firefox();
        $profile->setPreference('general.useragent.override', 'Mozilla/5.0 (Linux; Android 4.0.4; Galaxy Nexus Build/IMM76B) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.133 Mobile Safari/535.19');

        
        //$profile->setPreference('general.useragent.override', $userAgentChange);
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
        $width = 500;
        $height = 1000;
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
            $d = new WebDriverDimension($width,$height);
            $driver->manage()->window()->setSize($d);
        }

        $driver->get("https://apktovi.com");
        foreach ($list as $item) {
            try {
                $data = $item['info']['data'];
                if($data['appid'] == 'com.bandainamcoent.dblegends_ww'){
                    continue;
                }
                $path = public_path('combo/mobile/'.$data['appid']. ".png");
                echo public_path('combo/mobile/'.$data['appid']. ".png").PHP_EOL;
                if(!file_exists($path)){
                    $driver->get("https://apktovi.com/".$data['urltitle'].'-'.$data['appid'].'?video=1');
                    sleep(5);
                    $file_name = public_path('combo/mobile/'.$data['appid'].'.txt');
                    // 92 - 4 dong
                    //69 -3 dong
                    // 46 - 2 dong
                    $output = $driver->executeScript('return $(".title-content").height()');
                    file_put_contents($file_name, $output.PHP_EOL , FILE_APPEND | LOCK_EX);
                    $this->takeScreenshotImage($driver->findElement(WebDriverBy::cssSelector('body')), $data['appid'], $driver, $path);
                }
            } catch (\Exception $e) {
                
            }
            
            //die;


        }
    }
    public function screenShotPC($list){

        $proxy = false;
        $host = 'http://localhost:4444/wd/hub'; // this is the default

        $USE_FIREFOX = true; // if false, will use chrome.

        $profile = new FirefoxProfile();
        
        if($proxy != false){
            
            $profile->setPreference('network.proxy.type', 1);
            # Set proxy to Tor client on localhost
            $profile->setPreference('network.proxy.socks', '104.248.64.188');
            $profile->setPreference('network.proxy.socks_port', 28982);
            

        }
        $caps = DesiredCapabilities::firefox();

        $caps->setCapability(FirefoxDriver::PROFILE, $profile);

        $width = 1920;
        $height = 1080;
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
            $d = new WebDriverDimension($width,$height);
            $driver->manage()->window()->setSize($d);
        }

        $driver->get("https://apktovi.com");

        foreach ($list as $item) {
            try {
                $data = $item['info']['data'];
                $path = public_path('combo/pc/'.$data['appid']. ".png");
                $path_1 = public_path('combo/pc/'.$data['appid']. "_1.png");
                if(!file_exists($path) || !file_exists($path_1)){
                    echo public_path('combo/pc/'.$data['appid']. "_1.png").PHP_EOL;
                    $driver->get("https://apktovi.com/search?q=".urlencode($data['title']));
                    sleep(5);
                    $this->takeScreenshotImage($driver->findElement(WebDriverBy::cssSelector('body')), $data['appid'], $driver, $path, false);
                    
                    //$path_1 = public_path('combo/pc/'.$data['appid']. "_1.png");
                    $driver->get("https://apktovi.com/download-".$data['urltitle'].'-'.$data['appid']);
                    sleep(5);
                    $file_name = public_path('combo/pc/'.$data['appid'].'.txt');
                    $output = $driver->executeScript('return $(".apk-detail__title").height()');
                                            //print_r($output);
                    file_put_contents($file_name, $output.PHP_EOL , FILE_APPEND | LOCK_EX);
                    $this->takeScreenshotImage($driver->findElement(WebDriverBy::cssSelector('body')), $data['appid'], $driver, $path_1, false);
                }

            } catch (\Exception $e) {
                
            }
            
            //die;


        }
    }
 
    public function detailScreen($appid){
        $curl = curl_init();

        curl_setopt_array($curl, array(

          //CURLOPT_URL => "http://159.69.156.39/api-video/public/api/video?page=".$page."&limit=".$limit,
          CURLOPT_URL => "http://api.tovicorp.com/detailScreen?appid=".$appid,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        //print_r($response);
        return json_decode($response,true);
    }
    public function screenShotVariants($list){
        $proxy = false;
        $host = 'http://localhost:4444/wd/hub'; // this is the default

        $USE_FIREFOX = true; // if false, will use chrome.
        $profile = new FirefoxProfile();
        
        if($proxy != false){
            
            $profile->setPreference('network.proxy.type', 1);
            # Set proxy to Tor client on localhost
            $profile->setPreference('network.proxy.socks', '104.248.64.188');
            $profile->setPreference('network.proxy.socks_port', 28982);
            

        }
        $caps = DesiredCapabilities::firefox();
        $profile->setPreference('general.useragent.override', 'Mozilla/5.0 (Linux; Android 4.0.4; Galaxy Nexus Build/IMM76B) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.133 Mobile Safari/535.19');

        
        //$profile->setPreference('general.useragent.override', $userAgentChange);
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);

        $width = 500;
        $height = 1000;
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
            $d = new WebDriverDimension($width,$height);
            $driver->manage()->window()->setSize($d);
        }

        $driver->get("https://apktovi.com?video=1");
        //echo count($list);die;
        foreach ($list as $item) {
            //print_r($data);die;
            try {
                $data = $item['info']['data'];
                $files = $item['files'][0]['detail'];
                $path = public_path('variants/'.$data['appid']. ".png");
                $file_name = public_path('variants/'.$data['appid'].'.txt');
                echo $data['appid'];
                if(!is_file(public_path('variants/'.$data['appid']. ".png"))){
                    $file_name = public_path('variants/'.$data['appid'].'.txt');
                    $driver->get("https://apktovi.com/".$data['urltitle'].'-'.$data['appid'].'?video=1');
                    $output = $driver->executeScript('return $(".detail-app").height()');
                    $this->takeScreenshotImage($driver->findElement(WebDriverBy::cssSelector('body')), $data['appid'], $driver, $path, true);
                    file_put_contents($file_name, $output.PHP_EOL , FILE_APPEND | LOCK_EX);
                    $driver->findElement(WebDriverBy::className('btn-download-apk'))->click();
                    sleep(1);
                    $path = public_path('variants/'.$data['appid']. "_1.png");
                    $this->takeScreenshotImage($driver->findElement(WebDriverBy::cssSelector('body')), $data['appid'], $driver, $path, true);
                    foreach($files as $file){
                        $arc = json_decode($file['architecture'], true);
                        if($arc[0] != 'x86'){
                            $driver->get("https://apktovi.com/".$data['urltitle'].'-'.$data['appid'].'/'.$file['version'].'/'.$file['versioncode'].'?video=1');
                            $path = public_path('variants/'.$data['appid']. "_2.png");
                            $this->takeScreenshotImage($driver->findElement(WebDriverBy::cssSelector('body')), $data['appid'], $driver, $path, true);
                            break;

                        }
                    }
                }        
            } catch (\Exception $e) {
                
            }
                //die;
            //die;

        }
    }
    public function screenShotApk($list){
        $this->screenShotPC($list);
        $this->screenShotMobile($list);
    }
    public function repairApp(){

        /*for ($i = 1; $i <= 40; $i++) {
            $list = $this->getAppApkTovi($i,1000);
            $list = $list['data'];
            $listVariants = array();
            $listApk = array();
            foreach ($list as $item) {
                $app = $this->detailScreen($item['appid']);
                $appid = $item['appid'];
                if($app['abuse'] == false){
                    $info = $app['app'];
                    $files = $app['files'];
                    $path = '/var/www/html/testapktot/keywords/public/combo/gplay';

                    if(file_exists($path.'/'.$item['appid'].'.png') == true){
                        $listApk[] = array('info' => $info, 'files' => $files);
                    } 
                }
            }
            //echo count($listVariants);
            //$this->screenShotVariants($listVariants);
            $this->screenShotApk($listApk);
        }*/
         $list_file = scandir('/var/www/html/testapktot/keywords/public/combo/gplay');
    
        unset($list_file[0]);
        unset($list_file[1]);
        foreach ($list_file as &$tmp) {
            $tmp = str_replace('.png', '', $tmp);
        }
        $listApk = array();
        foreach ($list_file as $item) {
            $app = $this->detailScreen($item);
            $appid = $item;
            if($app['abuse'] == false){
                $info = $app['app'];
                $files = $app['files'];
                $path = '/var/www/html/testapktot/keywords/public/combo/gplay';

                if(file_exists($path.'/'.$item.'.png') == true){
                    $listApk[] = array('info' => $info, 'files' => $files);
                } 
            }
        }
        //echo count($listVariants);
        //$this->screenShotVariants($listVariants);
        $this->screenShotApk($listApk);
    }

    public function screenApp(){
        for ($i = 1; $i <= 40; $i++) {
            $list = $this->getAppApkTovi($i,1000);
            $list = $list['data'];
            $listVariants = array();
            $listApk = array();
            foreach ($list as $item) {
                $app = $this->detailScreen($item['appid']);
                if($app['abuse'] == false){
                    $info = $app['app'];
                    $files = $app['files'];
                    if($app['checkVariants'] == true){
                        $listVariants[] =  array('info' => $info, 'files' => $files);
                    } else {
                        $listApk[] = array('info' => $info, 'files' => $files);
                    }
                }
            }
            //echo count($listVariants);
            $this->screenShotVariants($listVariants);
            $this->screenShotApk($listApk);
        }
        

    }
    public function youtube(){
        $host = 'http://localhost:4444/wd/hub';
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();

        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $options->setExperimentalOption("prefs", $prefs);
        $caps->setCapability(ChromeOptions::CAPABILITY, $options);
        $caps->setCapability('userAgent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0');
        $driver = RemoteWebDriver::create($host, DesiredCapabilities::firefox());
        $driver->get("https://google.com/");
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
        $driver->get('https://google.com/');
        sleep(5);
        $driver->get('https://youtube.com/');
        sleep(5);
        sleep(5);

    }
    public function loginGG(){
        $mail = 'thanhtoanlc106@gmail.com';
        $pass = 'toan1234';
        $num_bot = 1;
        

        $host = 'http://localhost:4444/wd/hub';
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();

        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $options->setExperimentalOption("prefs", $prefs);
        $caps->setCapability(ChromeOptions::CAPABILITY, $options);
        $caps->setCapability('userAgent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0');
        $driver = RemoteWebDriver::create($host, DesiredCapabilities::firefox());

        $driver->get("https://accounts.google.com/");
        sleep(5);
        // $driver->findElement(WebDriverBy::cssSelector('#buttons a.yt-simple-endpoint.ytd-button-renderer'))->click();
        // sleep(2);
        $driver->findElement(WebDriverBy::cssSelector("input"))->sendKeys($mail);
        sleep(1);
        $driver->findElement((WebDriverBy::id('identifierNext')))->click();
        sleep(1);
        $driver->findElement(WebDriverBy::id('password'))->findElement(WebDriverBy::cssSelector('input'))->sendKeys($pass);
        sleep(1);
        $driver->findElement((WebDriverBy::id('passwordNext')))->click();
        sleep(20);
        $list = $driver->manage()->getCookies();
        foreach ($list as $cookie) {
            //$domain = $cookie->getDomain();
            print_r($cookie);
            $tmp = $cookie['name'].':'.$cookie['value'];
            file_put_contents(public_path('gg.txt'), $tmp.PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }
}
