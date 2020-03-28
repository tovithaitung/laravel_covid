<?php

namespace App\Http\Controllers;
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
use Illuminate\Http\Request;
use App\NoCaptchaProxyless;
use App\Anticaptcha;
class AntiCapcha extends Controller
{
	private $api_key = 'abe3054d7aef8ffa75d418744bdbad6c';
    private $site_key = '6LfwuyUTAAAAAOAmoS0fdqijC2PbbdH4kjq62Y1b';
    //
    public function runSearch(){

        while(1){
            $keywords = DB::table('domain_list')->whereNull('google_index')->where('rank','>=',500)->where('rank','<=',100000)->get();
            echo count($keywords);
            $res = array();
            foreach ($keywords as $key => $keyword) {
                $res[] = array('domain' => $keyword->domain_name,'id' => $keyword->id);
                if($key %15 ==0){
                    $this->seachSelenium($res);
                    $res = array();
                }
            }
        }

    }
    public function seachSelenium($listKey = array()){
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
        // $profile->setPreference('network.proxy.socks', '67.205.180.86');
        // $profile->setPreference('network.proxy.socks_port', 28982);
        
        $caps = DesiredCapabilities::firefox();
        //$caps->setCapability(FirefoxDriver::PROFILE, $profile);
        //$caps->setCapability(ChromeOptions::CAPABILITY, $options);
        // $capabilities = [
        //     WebDriverCapabilityType::BROWSER_NAME => 'firefox',
        //     WebDriverCapabilityType::PROXY => [
        //         'proxyType' => 'manual',
        //         'socksProxy' => '104.248.64.188:28982',
        //         //'sslProxy' => '127.0.0.1:2043',
        //     ],
        // ];
        $caps->setCapability(
            'moz:firefoxOptions',
           ['args' => ['-headless']]
        );
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
        	echo $listKey[0]['domain'];
            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->click();
            sleep(1);
            $driver->findElement(WebDriverBy::cssSelector('input.gLFyf'))->sendKeys('site:'.$listKey[0]['domain']);
            sleep(1);
            $driver->findElement(WebDriverBy::cssSelector('div.FPdoLc .gNO89b'))->click();
            sleep(5);

            try {
                $id = $driver->findElement(WebDriverBy::id("resultStats"))->getAttribute('innerHTML');
                $tmp = explode('About',$id);
                $tmp = explode('results', $tmp[1]);
                $result = $tmp[0];
                $result = str_replace('.', '', $result);
                $result = str_replace(',', '', $result);
                //$this->updateKey($listKey[0]['id'], $result);
                unset($listKey[0]);
                foreach ($listKey as $key => &$value) {
                    if($key ==0){
                        continue;
                    }
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
                        $tmp = explode('About',$id);
                        $tmp = explode('results', $tmp[1]);
                        $result = $tmp[0];
                        $result = str_replace('.', '', $result);
                        $result = str_replace(',', '', $result);
                        print_r($result.PHP_EOL);
                        $this->updateKey($value['id'], $result);
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
                    $driver->executeScript('document.getElementById("g-recaptcha-response").innerHTML = "'.$recaptchaToken['token'].'"');
                    $driver->executeScript('document.getElementById("captcha-form").submit()');
                    sleep(10);
                    try {
                        $id =$driver->findElement(WebDriverBy::id("resultStats"))->getAttribute('innerHTML');
                        //print_r($id.PHP_EOL);
                        $tmp = explode('About',$id);
                        $tmp = explode('results', $tmp[1]);
                        $result = $tmp[0];
                        $result = str_replace('.', '', $result);
                        $result = str_replace(',', '', $result);
                        $this->updateKey($listKey[0]['id'], $result);
                        print_r($result.PHP_EOL);

                        unset($listKey[0]);
                        foreach ($listKey as $key => $value) {
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
                                    $this->updateKey($value['id'], $result);
                                    print_r($result.PHP_EOL);
                                    unset($listKey[$key]);
                                } else {
                                    $tmp = explode('result', $id);
                                    $result = $tmp[0];
                                    $result = str_replace('.', '', $result);
                                    $result = str_replace(',', '', $result);
                                    $this->updateKey($value['id'], $result);
                                    print_r($result.PHP_EOL);
                                    unset($listKey[$key]);
                                }
                            } catch (\Exception $e3) {
                                $urlAry = $driver->executeScript('return window.location',array());
                                $currentURL = $urlAry['href'];
                                if($currentURL == 'https://www.google.com/sorry/index'){
                                    $this->report($recaptchaToken['taskId']);
                                    $this->updateKey($listKey[$key]['id'], 0);
                                } else {
                                    $this->updateKey($listKey[$key]['id'], 0);
                                }
                                unset($listKey[$key]);
                            }
                            
                        }
                    } catch (\Exception $e1) {
                        $urlAry = $driver->executeScript('return window.location',array());
                        $currentURL = $urlAry['href'];

                        if($currentURL == 'https://www.google.com/sorry/index'){
                        	$this->report($recaptchaToken['taskId']);
                            $this->updateKey($listKey[0]['id'], 0);
                        } else {

                            $this->updateKey($listKey[0]['id'], 0);
                            foreach ($listKey as $key => $value) {
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
                                        $this->updateKey($value['id'], $result);
                                        print_r($result.PHP_EOL);
                                        unset($listKey[$key]);
                                    } else {
                                        $tmp = explode('result', $id);
                                        $result = $tmp[0];
                                        $result = str_replace('.', '', $result);
                                        $result = str_replace(',', '', $result);
                                        $this->updateKey($value['id'], $result);
                                        print_r($result.PHP_EOL);
                                        unset($listKey[$key]);
                                    }
                                } catch (\Exception $e3) {
                                    $urlAry = $driver->executeScript('return window.location',array());
                                    $currentURL = $urlAry['href'];
                                    if($currentURL == 'https://www.google.com/sorry/index'){
                                    	$this->report($recaptchaToken['taskId']);
                                        $this->updateKey($listKey[$key]['id'], 0);
                                    } else {
                                        $this->updateKey($listKey[$key]['id'], 0);
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
            echo $captcha->getMessage();
        }
    
        $driver->quit();
    }
    public function updateKey($id, $search){
        $search = str_replace(',', '', $search);
        DB::table('domain_list')->where('id',$id)->update(['google_index' => $search]);
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
            $recaptchaToken = $api->getTaskSolution();
            //echo "\ntoken result: $recaptchaToken\n\n";
            return  array('token' => $recaptchaToken,'taskId' => $taskId);
        }
    }
    public function report($id){
    	$api = new Anticaptcha();
        $api->setVerboseMode(true);
                
        //your anti-captcha.com account key
        $api->setKey($this->api_key);
        $api->jsonPostRequest('reportIncorrectRecaptcha', array('clientKey' => $this->api_key, 'taskId' => $id));
         
    }
}
