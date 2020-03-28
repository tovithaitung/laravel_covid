<?php

namespace App\Http\Controllers;
require_once 'simple_html_dom.php';
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
use Facebook\WebDriver\Exception;
use App\NoCaptchaProxyless;
use App\Anticaptcha;


class CourseController extends Controller
{
    public function SaveHtmlCapcha () {
		$host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.

        $profile = new FirefoxProfile();
        $profile->setPreference('general.useragent.override', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36');

       
        $caps = DesiredCapabilities::firefox();
        $profile->setPreference("dom.webdriver.enabled", false);
        $profile->setPreference('useAutomationExtension', false);
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);
        
        $driver = RemoteWebDriver::create($host, $caps);
        $height = 1080;
        $width = 1920;
        $d = new WebDriverDimension($width,$height);
        $driver->manage()->window()->setSize($d);
		
		try {
        	$list_page = DB::table('course_list')
							->select('id','url')
							->where('parent', '1')
							->where('type', '1')
							->where('status', '0')
							->get();
				foreach ($list_page as $url) {
					$id_url = $url->id;
					$url = $url->url;
					$driver->get($url);
					sleep(3);
					$elements = $driver->findElements(WebDriverBy::cssSelector('#px-captcha'));
					if (empty($elements)) {
                        continue;
					}
					if (empty($elements)) {
						$list = $driver->manage()->getCookies();
						$html = $driver->findElement(WebDriverBy::tagName('html'))->getAttribute('innerHTML');
						$public_path = public_path();
						file_put_contents($public_path."/udemy/".$id_url.".html", $html);
						DB::table('course_list')->where('url',$url)->update(['status' => '1']);
					} else {
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
    
        $driver->quit();
	}
	public function InsertUrl() {
		$public_path = public_path();
		for ($i = 1; $i <= 65; $i++) {
			// $id_url = $i;
			if ($i == 1) {
				$url = $public_path."/coursera/social-sciences.html";
			} else {
				$url = $public_path."/coursera/social-sciences_".$i.".html";
			}
			$resp = file_get_contents($url);
			$html = str_get_html($resp);
			// var_dump($url);die;
			$data_link = $html->find('a');
			$main_url = 'https://www.coursera.org/';
			foreach ($data_link as $value) {
				$data = array();
				$link = $value->href;
				if (!empty($link)) {
					if (strpos($link, '/learn/') !== false) {
						$link = trim($link, '/');
						$link = $main_url . $link;
						$data['url'] = $link;
						$data['parent'] = '1';
						$data['type'] = '1';
						$data['status'] = '0';
						DB::table('course_list')->insertOrIgnore($data);
					}
				}
			}
		}
		// echo'<pre>';print_r($list_link);die;
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
	public function getAPI($list_cate, $subcategory='instructional_level') {
		// $course_id = '351892';
		foreach ($list_cate as $category_id) {
			$url = 'https://www.udemy.com/api-2.0/discovery-units/all_courses/?p=1&page_size=16&subcategory=&'.$subcategory.'=&lang=&price=&duration=&closed_captions=&category_id='.$category_id.'&source_page=category_page&locale=en_US&currency=usd&navigation_locale=en_US&skip_price=true&sos=pc&fl=cat';
			$curl = curl_init();
					curl_setopt_array($curl, array(
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_URL => $url,
					CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/83.0.144 Chrome/77.0.3865.144 Safari/537.36',
					CURLOPT_SSL_VERIFYPEER => false,
				));
			$resp = curl_exec($curl);
			curl_close($curl);
			$html = json_decode($resp);
			$limit = (ceil(($html->unit->remaining_item_count)/16)) + 1;
			for ($num_page=1;$num_page<=$limit;$num_page++) {
				$data = array();
				$url1 = 'https://www.udemy.com/api-2.0/discovery-units/all_courses/?p='.$num_page.'&page_size=16&subcategory=&'.$subcategory.'=&lang=&price=&duration=&closed_captions=&category_id='.$category_id.'&source_page=category_page&locale=en_US&currency=usd&navigation_locale=en_US&skip_price=true&sos=pc&fl=cat';
				// $url = 'https://www.udemy.com/api-2.0/discovery-units/?context=landing-page&course_id='.$course_id.'&item_count=18&source_page=course_landing_page';
				$curl1 = curl_init();
					curl_setopt_array($curl1, array(
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_URL => $url1,
					CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/83.0.144 Chrome/77.0.3865.144 Safari/537.36',
					CURLOPT_SSL_VERIFYPEER => false,
				));
				$resp1 = curl_exec($curl1);
				curl_close($curl1);
				$html1 = json_decode($resp1);
				// echo'<pre>';print_r($html1->unit->items);die;
				$items = $html1->unit->items;
				foreach ($items as $value) {
					$data['id_course'] = $value->id;
					$data['status'] = '0';
					DB::table('id_course')->insertOrIgnore($data);
				// echo'<pre>';print_r($html1);die;
				}
			}
		}
	}
	public function SaveHtml($start, $end) {
		$host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.

        $profile = new FirefoxProfile();
        $profile->setPreference('general.useragent.override', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0');

       
        $caps = DesiredCapabilities::firefox();
        $profile->setPreference("dom.webdriver.enabled", false);
        $profile->setPreference('useAutomationExtension', false);
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);
        
        $driver = RemoteWebDriver::create($host, $caps);
        $height = 1080;
        $width = 1920;
        $d = new WebDriverDimension($width,$height);
        $driver->manage()->window()->setSize($d);
		$list_page = DB::table('course_list')
					->select('id','url')
					->where('parent', '1')
					->where('type', '2')
					->where('status', '0')
					->whereBetween('id', [$start,$end])
					->get();
		// echo'<pre>';print_r($list_page);die;
		foreach ($list_page as $url) {
			$id_url = $url->id;
			$url = $url->url;
		// business,computer-science,data-science,information-technology,health,math-and-logic,personal-development,physical-science-and-engineering,social-sciences,language-learning
		// 136,105,67,30,59,18,41,51,65,19
		// for ($i = 2; $i<=65; $i++) {
			// $url = 'https://www.coursera.org/browse/social-sciences?page='.$i;
			// $url = 'https://www.coursera.org/learn/stem';
			$driver->get($url);
			sleep(5);
			$list = $driver->manage()->getCookies();
			$html_down = $driver->findElement(WebDriverBy::tagName('html'))->getAttribute('innerHTML');
			$html = $driver->findElements(WebDriverBy::cssSelector('div.BreadcrumbItem_1pp1zxi > a'));
			foreach ($html as $value) {
				$check = $value->getAttribute('data-reactid');
				if ($check == '237') {
					// $href_cate = $value->getAttribute('href');
					// $name_cate = parse_url($href_cate);
					// $name_cate = str_replace('/browse/', '', $name_cate['path']);
					$name_cate = strtolower($value->getText());
					$name_cate = str_replace(' ', '-', $name_cate);
				}
			}
			print_r($name_cate);
			$public_path = public_path();
			file_put_contents($public_path."/coursera/".$name_cate."_detail/".$id_url.".html", $html_down);
			DB::table('course_list')->where('url',$url)->update(['status' => '1']);
		}
		$driver->quit();
		// }
	}
	public function AutoFill() {
		$list_page = DB::table('course_list')
			->select('id','url')
			// ->where('type', '2')
			->get();
		foreach ($list_page as $url) {
			$url = $url->url;
			$data = array();
			$data['url'] = $url;
			$data['parent'] = '1';
			$data['type'] = '2';
			$data['status'] = '0';
			DB::table('course_list_tam')->insert($data);
		}
	}
	public function InsertCourse($min, $limit) {
		$data = array();
		$list_page = DB::select('select id_course from id_course where status = "0" LIMIT '.$min.','.$limit	);
		foreach ($list_page as $value) {
			// $id_course = $value->id_course;
			$id_course = '656974';
			$url = 'https://www.udemy.com/api-2.0/courses/'.$id_course.'?fields%5Bcourse%5D=';
			$url .= 'context_info,primary_category,primary_subcategory,avg_rating_recent,visible_instructors,locale,';
			$url .= 'estimated_content_length,num_subscribers,description,content_info,headline,caption_locales,avg_rating,num_published_lectures,url,num_reviews,title';
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			  CURLOPT_HTTPHEADER => array(
				": authority: www.udemy.com",
				": method: GET",
				": path: /api-2.0/courses/567828?fields[course]=context_info,primary_category,primary_subcategory,avg_rating_recent,visible_instructors,locale,estimated_content_length,num_subscribers",
				": scheme: https",
				"accept:  application/json, text/plain, /",
				"accept-encoding:  gzip, deflate, br",
				"accept-language:  vi,und;q=0.9",
				"cookie:  cfduid=dea2f4d475eab3d8a7e8a6ec5ac6650c61585011853; ud_cache_language=en; ud_cache_price_country=VN; ud_cache_marketplace_country=VN; ud_firstvisit=2020-03-24T01:04:13.711677+00:00:1jGXzK:762pDYxHKvhYJ82EhtVX9dyTzyQ; ud_cache_modern_browser=1; udmy_2_v57r=9c52b4a3ea194975abe92afb6ca2436d; ud_cache_brand=VNen_US; ud_cache_logged_in=0; ud_cache_user=\"\"; ud_cache_version=1; pxhd=40a6d88f032989a09ec2a2dd5741f7730cca9a859b6e9e2154a325627fc47d33:6072dc91-6d6b-11ea-961b-ff1093b11e1e; _cfruid=122748f818eea0db5e20e70923dc428653e8942a-1585011854; EUCookieMessageShown=true; EUCookieMessageState=initial;"
			  ),
			));
			$resp = curl_exec($curl);
			$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$html = json_decode($resp);
			$instructors = array();
			$course_content = array();
			$incentives = array();
			$subs = array();
			// echo'<pre>';print_r($http_code);die;
			if ($http_code == 200) {
				$donotpermission = '0';
				if (isset($html->detail)) {
					$donotpermission = '1';
				}
				if ($donotpermission == '0') {
					$visible_instructors = $html->visible_instructors;
					foreach ($html->visible_instructors as $key => $instructor) {
						if (empty($instructor->title)) {
							$instructors[$key]['title'] = $instructor->name;
						} else {
							$instructors[$key]['title'] = $instructor->title;
						}
						$instructors[$key]['image']['image_50x50'] = $instructor->image_50x50;
						$instructors[$key]['image']['image_100x100'] = $instructor->image_100x100;
					}
					$instructors = json_encode($instructors);
					$enrollment = $html->num_subscribers;
					$category = $html->primary_category->title;
					$tag = $html->primary_subcategory->title;
					$locale = $html->locale->title;
					$description = $html->description;
					//////////////
					$url1 = 'https://www.udemy.com/api-2.0/course-landing-components/'.$id_course.'/me/?components=practice_test_bundle,curriculum,incentives,buy_button,introduction_asset';
					$curl1 = curl_init();
						curl_setopt_array($curl1, array(
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_URL => $url1,
						CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/83.0.144 Chrome/77.0.3865.144 Safari/537.36',
						CURLOPT_SSL_VERIFYPEER => false,
					));
					$resp1 = curl_exec($curl1);
					curl_close($curl1);
					$html1 = json_decode($resp1);
					////////////////////////
					$short_description = $html->headline;
					$price = $html1->buy_button->button->payment_data->purchasePrice->amount;
					$domain = 'https://www.udemy.com';
					$origin_url = $domain.$html->url;
					$list_sub = $html->caption_locales;
					foreach ($list_sub as $sub) {
						$subs[] = $sub->english_title;
					}
					$subs = json_encode($subs);
					$total_rate = $html->num_reviews;
					$score = $html->avg_rating;
					$img_url = $html1->introduction_asset->images;
					$img_url = json_encode($img_url);
					$array_course_content = $html1->curriculum->data->sections;
					$content_info = $html->content_info;// total hours
					$total_lectures = $html->num_published_lectures;// total hours
					foreach ($array_course_content as $key => $content) {
						$course_content[$key]['title'] = $content->title;
						$course_content[$key]['content_length_text'] = $content->content_length_text;
						$items = $content->items;
						foreach ($items as $key2 => $item) {
							$course_content[$key]['items'][$key2]['title'] = $item->title;
							$course_content[$key]['items'][$key2]['content_summary'] = $item->content_summary;
						}
						$course_content[$key]['lecture_count'] = $content->lecture_count;
					}
					$course_content['content_info'] = $content_info;
					$course_content['total_lectures'] = $total_lectures;
					$course_content = json_encode($course_content);
					$incentives['video_content_length'] = $html1->incentives->video_content_length;
					$incentives['num_additional_resources'] = $html1->incentives->num_additional_resources;
					$incentives['num_practice_tests'] = $html1->incentives->num_practice_tests;
					$incentives['devices_access'] = $html1->incentives->devices_access;
					$incentives['has_lifetime_access'] = $html1->incentives->has_lifetime_access;
					$incentives = json_encode($incentives);
					$title = $html->title;
					$data['category'] = $category;
					$data['title'] = $title;
					$data['short_description'] = $short_description;
					$data['score'] = $score;
					$data['total_rate'] = $total_rate;
					$data['enrollment'] = $enrollment;
					$data['instructor'] = $instructors;
					// $data['last_updated'] = $html2->units[0]->items[0]->buyables[0]->last_updated_date;
					$data['last_updated'] = null;
					$data['locale'] = $locale;
					$data['sub'] = $subs;
					$data['what_you_get'] = '';
					$data['course_content'] = $course_content;
					$data['requirements'] = '';
					$data['description'] = $description;
					$data['price'] = $price;
					$data['incentives'] = $incentives;
					$data['img_url'] = $img_url;
					$data['origin_url'] = $origin_url;
					$data['tag'] = $tag;
					$data['created_at'] = date('Y-m-d H:i:s');
					// echo'<pre>';print_r($data);die;
					DB::table('coures_detail')->insertOrIgnore($data);
					DB::table('id_course')->where('id_course',$id_course)->update(['status' => '1']);
					DB::table('id_course')->where('id_course',$id_course)->update(['status_code' => '0']);
				} else {
					DB::table('id_course')->where('id_course',$id_course)->update(['status' => '2']);
				}
			} elseif($http_code == '403') {
				DB::table('id_course')->where('id_course',$id_course)->update(['status' => '2']);
			} else {
				DB::table('id_course')->where('id_course',$id_course)->update(['status_code' => '1']);
			}
		}
	}
	public function RunAuto($limit = 0){
		$first = DB::table('course_list')
			->where('parent', '6215')
			->where('type', '2')
			->where('status', '0')
			->orderBy('id','ASC')
			->first();
		$last = DB::table('course_list')
			->where('parent', '6215')
			->where('type', '2')
			->where('status', '0')
			->orderBy('id','DESC')
			->first();
		$min = $first->id;
		$max = $last->id;
		while(1){
			if($min >= $max){
				break;
			}
			$cmd = 'nohup php artisan course:insert 13 '.$min.' '.($min+$limit).' >/dev/null 2>&1&';
			shell_exec($cmd);
			$min = $min + $limit;
		}
		// for ($i = 0; $i <= 10; $i++) {
			// $cmd = 'nohup php artisan course:insert 5 '.$i.'0000 10000 >/dev/null 2>&1&';
			// shell_exec($cmd);
		// }
	}
	public function InsertCourseSera() {
		$public_path = public_path();
		$array_cate = array('arts-and-humanities_detail','business_detail','computer-science_detail','data-science_detail','health_detail','information-technology_detail','language-learning_detail','math-and-logic_detail','personal-development_detail','physical-science-and-engineering_detail','social-sciences_detail');
		// $name_cate = 'arts-and-humanities_detail';
		foreach ($array_cate as $name_cate) {
			$url_folder = $public_path.'/coursera/'.$name_cate.'/';
			$array_file = scandir($url_folder);
			$array_file= array_diff($array_file, array('.', '..'));
			sort($array_file);
			foreach ($array_file as $file_name) {
				if (substr($file_name, -1) != '1') {
					$url = $public_path.'/coursera/'.$name_cate.'/'.$file_name;
					// print_r($url);die;
					$resp = file_get_contents($url);
					$html = str_get_html($resp);
					$find_title = $html->find('h1', 0);
					$find_enrollment = $html->find('.enrolledLargeFont_zrvvmr > span > strong > span');
					$find_description = $html->find('div.content-inner > p');
					$find_what_u_get = $html->find('.Col_i9j08c-o_O-xsCol12_1m1ceo5-o_O-mdCol6_1rbv01c p');
					$find_sub_title_course_content = $html->find('.Col_i9j08c-o_O-xsCol12_1m1ceo5-o_O-mdCol10_1eb21lj-o_O-lgCol10_ra5osh .SyllabusModule');
					$find_subtitle = $html->find('.atGlanceMarginTop_1v77vnx-o_O-hideOnSmall_143z63n div.ProductGlance .Box_120drhm-o_O-displayflex_poyjc');
					$find_avg_rating = $html->find('.rc-ReviewsOverview__totals__rating', 0);
					$find_total_view = $html->find('a.rc-ReviewsOverview__totals__total-reviews', 0);
					$find_instructor = $html->find('div.Row_nvwp6p > div.Col_i9j08c-o_O-xsCol12_1m1ceo5-o_O-lgCol6_8hn8ps');
					$find_category = $html->find('div.BreadcrumbItem_1pp1zxi > a');
					$find_origin_url = $html->find('meta');
					$check_title = $find_title->getAttribute('data-reactid');
					$course_content = array();
					$instructor = array();
					foreach ($find_enrollment as $enroll) {
						$enrollment = $enroll->plaintext;
					}
					if (empty($enrollment)) {
						$enrollment = '';
					}
					foreach ($find_description as $descriptions) {
						$check_description = $descriptions->getAttribute('data-reactid');
						if ($check_description == '392') {
							$short_description = $descriptions->plaintext;
						} else {
							$short_description = '';
						}
						if ($check_description == '393') {
							$description = $descriptions->plaintext;
						} else {
							$description = '';
						}
					}
					if (!empty($find_what_u_get->plaintext)) {
						$what_you_get = $find_what_u_get->plaintext;
					} else {
						$what_you_get = '';
					}
					foreach ($find_sub_title_course_content as $key => $sub_title_course_content) {
						$find_title_course_content = $sub_title_course_content->find('h2',0);
						$course_content[$key]['title'] = $find_title_course_content->plaintext;
						$find_sub_title_course_contentt = $sub_title_course_content->find('.ItemGroupView .P_gjs17i-o_O-weightNormal_s9jwp5-o_O-fontBody_56f0wi');
						foreach ($find_sub_title_course_contentt as $key2 => $item) {
							if (!empty($item->plaintext)) {
								$course_content[$key]['items'][$key2]['title'] = $item->plaintext;
							} else {
								$find_name = $item->find('a',0);
								$course_content[$key]['items'][$key2]['title'] = $find_name->plaintext;
							}
							$time_lession = $item->find('span span', 0);
							if (!empty($$time_lession->plaintext)) {
								$course_content[$key]['items'][$key2]['content_summary'] = $time_lession->plaintext;
								$course_content[$key]['items'][$key2]['title'] = str_replace($time_lession->plaintext, '', $course_content[$key]['items'][$key2]['title']);
							} else {
								$course_content[$key]['items'][$key2]['content_summary'] = '';
								$course_content[$key]['items'][$key2]['title'] = '';
							}
						}
					}
					$course_content = json_encode($course_content);
					foreach ($find_subtitle as $key => $filter) {
						$find_sub = $filter->find('div.Box_120drhm-o_O-displayflex_poyjc-o_O-columnDirection_ia4371 div span');
						$find_locale = $filter->find('div.Box_120drhm-o_O-displayflex_poyjc-o_O-columnDirection_ia4371>h4', 0);
						foreach ($find_sub as $subs) {
							if (strpos($subs->plaintext, 'Subtitles') !== false) {
								$sub = $subs->plaintext;
								$sub = str_replace('Subtitles: ', '', $sub);
								$sub = trim($sub);
							}
						}
						if (!empty($find_locale->plaintext)) {
							$locale = $find_locale->plaintext;
						} else {
							$locale = '';
						}
					}
					if (!empty($find_avg_rating->plaintext)) {
						$score = $find_avg_rating->plaintext;
					} else {
						$score = '';
					}
					if (!empty($$find_total_view->plaintext)) {
						$total_rate = $find_total_view->plaintext;
						$total_rate = str_replace(' reviews','', $total_rate);
					} else {
						$total_rate = '';
					}
					foreach ($find_instructor as $instructors) {
						$find_img_instructor = $instructors->find('a > div > div > img', 0);
						$find_name_instructor = $instructors->find('a > div > div > h3.instructor-name', 0);
						if (!empty($find_name_instructor->plaintext)) {
							$instructor[$key]['title'] = str_replace('&nbsp;', '',$find_name_instructor->plaintext);
							$instructor[$key]['image'] = $find_img_instructor->getAttribute('srcset');
						}
					}
					sort($instructor);
					$instructor = json_encode($instructor);
					foreach ($find_category as $cate) {
						$check_cate = $cate->getAttribute('data-reactid');
						if ($check_cate == '237') {
							$category = $cate->plaintext;
						}
						if ($check_cate == '242') {
							$tag = $cate->plaintext;
						}
					}
					foreach ($find_origin_url as $origin) {
						$check_url = $origin->getAttribute('property');
						if ($check_url == 'og:url') {
							$origin_url = $origin->getAttribute('content');
						}
						if ($check_url == 'og:title') {
							$title = $origin->getAttribute('content');
						}
					}
					$data['category'] = $category;
					$data['title'] = $title;
					$data['short_description'] = $short_description;
					$data['score'] = $score;
					$data['total_rate'] = $total_rate;
					$data['enrollment'] = $enrollment;
					$data['instructor'] = $instructor;
					$data['last_updated'] = null;
					$data['locale'] = $locale;
					$data['sub'] = $sub;
					$data['what_you_get'] = $what_you_get;
					$data['course_content'] = $course_content;
					$data['requirements'] = '';
					$data['description'] = $description;
					$data['price'] = '';
					$data['incentives'] = '';
					$data['img_url'] = '';
					$data['origin_url'] = $origin_url;
					$data['tag'] = $tag;
					$data['created_at'] = date('Y-m-d H:i:s');
					$data['updated_at'] = null;
					DB::table('coures_detail')->insertOrIgnore($data);
							// print_r($data);die;
					rename($url,$url.'_1');
				}
			}
		}
	}
	public function GetPageListEDX(){
		$domain = 'https://www.edx.org';
		$array_sbj = array('Architecture','Art-Culture','Biology-Life-Sciences','Business-Management','Chemistry','Communication','Computer Science','data-science','Design','Economics-Finance','Education-Teacher Training','Electronics','Energy-Earth-Sciences','Engineering','Environmental-Studies','Ethics','Food-Nutrition','Health-Safety','History','Humanities','Language','Law','Literature','Math','Medicine','Music','Philanthropy','Philosophy-Ethics','Physics','Science','Social Sciences');
		foreach ($array_sbj as $subject) {
			$data = array();
			$subject = strtolower($subject);
			$subject = str_replace(' ','-', $subject);
			$url = 'https://www.edx.org/page-data/course/subject/'.$subject.'/page-data.json';
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/83.0.144 Chrome/77.0.3865.144 Safari/537.36',
				CURLOPT_SSL_VERIFYPEER => false
			));
			$resp = curl_exec($curl);
			$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$html = json_decode($resp);
			print_r($subject);
			echo PHP_EOL;
			if (!empty($html->result->pageContext->productList)) {
				$productlist = $html->result->pageContext->productList;
				foreach ($productlist as $value) {
					if (!empty($value->prospectusPath)) {
						$link_course = $value->prospectusPath;
						$link = $domain.$link_course;
						$data['url'] = $link;
						$data['parent'] = '4036';
						$data['type'] = '2';
						$data['status'] = '0';
						DB::table('course_list')->insertOrIgnore($data);
					}
				}
			} else {
				$productlist = $html->result->pageContext->allCourse;
				foreach ($productlist as $value) {
					if (!empty($value->node->prospectusPath)) {
						$link_course = $value->node->prospectusPath;
						$link = $domain.$link_course;
						$data['url'] = $link;
						$data['parent'] = '4036';
						$data['type'] = '2';
						$data['status'] = '0';
						DB::table('course_list')->insertOrIgnore($data);
					}
				}
			}
			// print_r($course);die;
		}
	}
	public function GetProgramEDX(){
		$domain = 'https://www.edx.org';
		// $array_sbj = array('microbachelors','micromasters','professional-certificate','masters','gfa','xseries');
		// foreach ($array_sbj as $subject) {
			// $data = array();
			// $subject = strtolower($subject);
			// $subject = str_replace(' ','-', $subject);
			// $url = 'https://www.edx.org/api/v1/catalog/search/?type='.$subject.'&page_size=100';
			// $curl = curl_init();
			// curl_setopt_array($curl, array(
				// CURLOPT_URL => $url,
				// CURLOPT_RETURNTRANSFER => true,
				// CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/83.0.144 Chrome/77.0.3865.144 Safari/537.36',
				// CURLOPT_SSL_VERIFYPEER => false
			// ));
			// $resp = curl_exec($curl);
			// $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			// curl_close($curl);
			// $html = json_decode($resp);
			// print_r($subject);
			// echo PHP_EOL;
			// // $marketing_url = $html->objects->results[0]->marketing_url;
			// print_r($http_code);die;
			// foreach ($productlist as $value) {
				// if (!empty($value->prospectusPath)) {
					// $link_course = $value->prospectusPath;
					// $link = $domain.$link_course;
					// $data['url'] = $link;
					// $data['parent'] = '4036';
					// $data['type'] = '2';
					// $data['status'] = '0';
					// DB::table('course_list')->insertOrIgnore($data);
				// }
			// }
		// }
		$host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.

        $profile = new FirefoxProfile();
        $profile->setPreference('general.useragent.override', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0');

       
        $caps = DesiredCapabilities::firefox();
        $profile->setPreference("dom.webdriver.enabled", false);
        $profile->setPreference('useAutomationExtension', false);
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);
        
        $driver = RemoteWebDriver::create($host, $caps);
        $height = 1080;
        $width = 1920;
        $d = new WebDriverDimension($width,$height);
        $driver->manage()->window()->setSize($d);
		$public_path = public_path();
		$url = $public_path.'/udx/xseries.html';
		$aaaa = file_get_contents($url);
		$html1 = str_get_html($aaaa);
		$find_a = $html1->find('a');
		foreach ($find_a as $value) {
			if (!empty($value->href)) {
				if (strpos($value->href, '/xseries/') !== false) {
					if (strpos($value->href, '/edx.org/') !== false) {
						$driver->get($domain.$value->href);
					} else {
						$driver->get($value->href);
					}
					sleep(3);
					$list = $driver->manage()->getCookies();
					$html_down = $driver->findElements(WebDriverBy::tagName('a'));
					foreach ($html_down as $valuee) {
						$linkk = $valuee->getAttribute('href');
						// var_dump(strpos($linkk, '/course/subject/'));die;
						if (strpos($linkk, '/course/') !== false and strpos($linkk, '/course/subject/') == false) {
						// print_r(strpos($linkk, '/course/subject/'));die;
							// $link_course = $linkk;
							// $link = $domain.$link_course;
							$data['url'] = $linkk;
							$data['parent'] = '4036';
							$data['type'] = '2';
							$data['status'] = '0';
							DB::table('course_list')->insertOrIgnore($data);
						}
					}
					// print_r($value->href);
					// echo PHP_EOL;
				}
			}
		}
		$driver->quit();
	}
	public function InsertCourseEDX() {
		$domain = 'https://www.edx.org';
		$list_page = DB::select('select url from course_list where parent="4036" and type="2" and status = "0"');
		foreach ($list_page as $value) {
			$data = array();
			$incentives = array();
			$course_content = array();
			$instructor = array();
			$category = array();
			$url_detail = $value->url;
			if (strpos($url_detail, '/master/') == false and strpos($url_detail, '/course/subject/') == false) {
				$aaaa = parse_url($url_detail);
				$url_detail = str_replace('/course/', '',$aaaa['path']);
				$name_course = str_replace('-asux-mat170x', '',$url_detail);
				$url = 'https://www.edx.org/page-data/course/'.$name_course.'/page-data.json';
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/83.0.144 Chrome/77.0.3865.144 Safari/537.36',
					CURLOPT_SSL_VERIFYPEER => false
				));
				$resp = curl_exec($curl);
				$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				curl_close($curl);
				$html = json_decode($resp);
				// print_r($html->result);die;
				if (!empty($html->result->pageContext)) {
					$title = $html->result->pageContext->course->title;
					$short_description = json_encode($html->result->pageContext->course->shortDescription);
					$description = json_encode($html->result->pageContext->course->fullDescription);
					$enrollment = $html->result->pageContext->course->enrollmentCount;
					$what_you_get = $html->result->pageContext->course->outcome;
					$what_you_get = str_replace('<ul>', '', $what_you_get);
					$what_you_get = str_replace('<li>', '', $what_you_get);
					$what_you_get = str_replace('</li>', ',', $what_you_get);
					$what_you_get = str_replace('</ul>', '', $what_you_get);
					// $what_you_get = explode(",",$what_you_get);
					$what_you_get = json_encode($what_you_get);
					$course_json = $html->result->pageContext->course->syllabusRaw;
					$course_content_array = array();
					preg_match_all('/<strong>(.+?)<\/strong>/', $course_json, $course_content_array);
					$course_content_array[1] = array_unique($course_content_array[1]);
					foreach ($course_content_array[1] as $key => $value1) {
						$course_content[$key]['title'] = $value1;
						$course_content[$key]['content_length_text'] = 'empty';
						$course_content[$key]['items'] = 'empty';
					}
					$course_content = json_encode($course_content);
					$img_url = $html->result->pageContext->course->image->src;
					$img_url = json_encode($img_url);
					$locale = $html->result->pageContext->course->activeCourseRun->contentLanguage;
					$sub = $html->result->pageContext->course->activeCourseRun->transcriptLanguages;
					$sub = json_encode($sub);
					if (!empty($html->result->pageContext->course->activeCourseRun->weeksToComplete)) {
						$incentives['weeksToComplete'] = $html->result->pageContext->course->activeCourseRun->weeksToComplete;
					}
					$incentives['levelType'] = $html->result->pageContext->course->activeCourseRun->levelType;
					$incentives = json_encode($incentives);
					if (!empty($price = $html->result->pageContext->course->entitlements)) {
						$price = $html->result->pageContext->course->entitlements[0]->price;
					} else {
						$price = '';
					}
					$origin_url = $domain.$html->path;
					$list_instructor = $html->result->pageContext->course->activeCourseRun->staff;
					foreach ($list_instructor as $key => $instructors) {
						$instructor[$key]['title'] = $instructors->givenName . $instructors->familyName;
						$instructor[$key]['image'] = $instructors->profileImageUrl;
					}
					$instructor = json_encode($instructor);
					$list_cate = $html->result->pageContext->course->subjects;
					foreach ($list_cate as $key => $cate) {
						$category[] = $cate->name;
					}
					$tag = $list_cate[0]->name;
					$category = json_encode($category);
					$data['category'] = $category;
					$data['title'] = $title;
					$data['short_description'] = $short_description;
					$data['score'] = '';
					$data['total_rate'] = '';
					$data['enrollment'] = $enrollment;
					$data['instructor'] = $instructor;
					$data['last_updated'] = null;
					$data['locale'] = $locale;
					$data['sub'] = $sub;
					$data['what_you_get'] = $what_you_get;
					$data['course_content'] = $course_content;
					$data['requirements'] = '';
					$data['description'] = $description;
					$data['price'] = $price;
					$data['incentives'] = $incentives;
					$data['img_url'] = $img_url;
					$data['origin_url'] = $origin_url;
					$data['tag'] = $tag;
					$data['created_at'] = date('Y-m-d H:i:s');
					$data['updated_at'] = null;
					// var_dump($data);die;
					DB::table('coures_detail')->insertOrIgnore($data);
					DB::table('course_list')->where('url',$url_detail)->update(['status' => '1']);
				}
			}
		}
	}
	public function InsertCourseMaster() {
		$domain = 'https://www.edx.org';
		$list_page = DB::select('select url from course_list where `url` LIKE "%https://www.edx.org/masters/%" and parent="4036" and type="2" and status = "0"');
		foreach ($list_page as $value) {
			$url = $value->url;
			// print_r($url);die;
			$incentive = array();
			$requirments = array();
			$course_content = array();
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) coc_coc_browser/83.0.144 Chrome/77.0.3865.144 Safari/537.36',
				CURLOPT_SSL_VERIFYPEER => false
			));
			$resp = curl_exec($curl);
			$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$html = str_get_html($resp);
			$find_body = $html->find('body', 0);
			$data_master = $find_body->getAttribute('data-masters-program');
			$data_master = html_entity_decode($data_master);
			$data_master = json_decode($data_master);
			$title = '';
			$title .= $data_master->title;
			$title .= $data_master->subtitle;
			$origin_url = $data_master->marketing_url;
			$img_url = $data_master->card_image_url;
			$img_url = json_encode($img_url);
			$find_requirments = $data_master->degree->application_requirements;
			$find_requirments = str_replace('<p>', '', $find_requirments);
			$find_requirments = str_replace('<li>', '', $find_requirments);
			$find_requirments = str_replace('</p>', '', $find_requirments);
			$find_requirments = str_replace('</ul>', '', $find_requirments);
			$requirements = explode('<ul>', $find_requirments);
			// $requirements[1] = explode('</li>', $requirements[1]);
			$requirements = json_encode($requirements);
			$price = $data_master->degree->costs[0]->amount;
			$short_description = $data_master->degree->overall_ranking;
			$description = $data_master->overview;
			$description = str_replace('<p>', '', $description);
			$description = str_replace('<li>', '', $description);
			$description = str_replace('</li>', '', $description);
			$description = str_replace('</p>', '', $description);
			$description = str_replace('<ul>', '', $description);
			$description = str_replace('</ul>', '', $description);
			$course_content = $data_master->curricula[0]->marketing_text;
			$course_content = str_replace('<p>', '', $course_content);
			$course_content = str_replace('<i>', '', $course_content);
			$course_content = str_replace('</i>', '', $course_content);
			$course_content = str_replace('<ul>', '', $course_content);
			$course_content = str_replace('</ul>', '', $course_content);
			$course_content = str_replace('</li>', '', $course_content);
			$course_content = str_replace('<li>', '', $course_content);
			$course_content = explode('</p>', $course_content);
			$count_content = count($course_content);
			if (empty($course_content[$count_content - 1])) {
				array_pop($course_content);
			}
			$course_content = json_encode($course_content);
			// print_r($data_master);die;
			$data['category'] = 'master';
			$data['title'] = $title;
			$data['short_description'] = $short_description;
			$data['score'] = '';
			$data['total_rate'] = '';
			$data['enrollment'] = '';
			$data['instructor'] = '';
			$data['last_updated'] = null;
			$data['locale'] = '';
			$data['sub'] = '';
			$data['what_you_get'] = '';
			$data['course_content'] = $course_content;
			$data['requirements'] = $requirements;
			$data['description'] = $description;
			$data['price'] = $price;
			$data['incentives'] = '';
			$data['img_url'] = $img_url;
			$data['origin_url'] = $origin_url;
			$data['tag'] = 'master';
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = null;
			DB::table('coures_detail')->insertOrIgnore($data);
			DB::table('course_list')->where('url',$url)->update(['status' => '1']);
		}
	}
	public function InsertPageList() {
		$list_page = DB::table('courses_details')
			->select('url')
			->where('status', '0')
			->get();
		foreach ($list_page as $url) {
			$url = $url->url;
			$data = array();
			$data['url'] = $url;
			$data['parent'] = '6215';
			$data['type'] = '2';
			$data['status'] = '0';
			DB::table('course_list')->insert($data);
		}
	}
	public function InsertCourseLynda($start, $end){
		$list_page = DB::table('course_list')
					->select('url')
					->whereBetween('id', [$start,$end])
					->where('parent', '6215')
					->where('type', '2')
					->where('status', '0')
					->get();
		foreach ($list_page as $url) {
			$url = $url->url;
			$instructor = array();
			$course_content = array();
			$incentives = array();
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $url,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET"
			));
			$resp = curl_exec($curl);
			$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			$html = str_get_html($resp);
			$find_instructor = $html->find('.author-thumb > a > cite');
			$find_instructor_img = $html->find('img');
			foreach ($find_instructor as $value) {
				if ($value->getAttribute('data-ga-label') == 'author-name') {
					$instructor['title'] = $value->plaintext;
				}
			}
			foreach ($find_instructor_img as $value) {
				if ($value->getAttribute('data-ga-label') == 'author') {
					$instructor['image'] = $value->getAttribute('src');
				}
			}
			$instructor = json_encode($instructor);
			$find_description = $html->find('div');
			$description = '';
			foreach ($find_description as $desc) {
				if ($desc->getAttribute('itemprop') == 'description') {
					$description .= trim($desc->plaintext);
				}
			}
			$find_course_content = $html->find('ul.course-toc > li');
			foreach ($find_course_content as $key => $content) {
				$course_content[$key]['title'] = trim($content->find('h4', 0)->plaintext);
				$list_item = $content->find('ul>li');
				foreach ($list_item as $key2 => $item) {
					$course_content['items'][$key]['title'] = trim($item->find('a', 0)->plaintext);
					$course_content['items'][$key]['content_summary'] = $item->find('span', 0)->plaintext;
				}
			}
			$course_content = json_encode($course_content);
			$find_tag = $html->find('div.software-tags>a>em');
			$tag = '';
			foreach ($find_tag as $tag_name) {
				$tag .= $tag_name->plaintext.',';
			}
			$tag = trim($tag, ',');
			$find_incentive = $html->find('span.course-info-stat');
			foreach ($find_incentive as $incentive) {
				if ($incentive->getAttribute('itemprop') == 'timeRequired') {
					$incentives['video_content_length'] = $incentive->plaintext;
				}
			}
			$incentives = json_encode($incentives);
			$find_category = $html->find('ol.page-breadcrumb > li');
			$category = '';
			foreach ($find_category as $cate) {
				$category .= $cate->find('div>a>span', 0)->plaintext;
			}
			$find_meta = $html->find('meta');
			foreach ($find_meta as $meta) {
				if ($meta->getAttribute('property') == 'og:url') {
					$origin_url = $meta->getAttribute('content');
				}
				if ($meta->getAttribute('property') == 'og:locale') {
					$locale = $meta->getAttribute('content');
				}
				if ($meta->getAttribute('property') == 'og:image') {
					$img_url = $meta->getAttribute('content');
				}
				if ($meta->getAttribute('property') == 'og:title') {
					$title = $meta->getAttribute('content');
				}
			}
			$data['category'] = $category;
			$data['title'] = $title;
			$data['short_description'] = '';
			$data['score'] = '';
			$data['total_rate'] = '';
			$data['enrollment'] = '';
			$data['instructor'] = $instructor;
			$data['last_updated'] = null;
			$data['locale'] = $locale;
			$data['sub'] = '';
			$data['what_you_get'] = '';
			$data['course_content'] = $course_content;
			$data['requirements'] = '';
			$data['description'] = $description;
			$data['price'] = '';
			$data['incentives'] = $incentives;
			$data['img_url'] = json_encode($img_url);
			$data['origin_url'] = $origin_url;
			$data['tag'] = $tag;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = null;
			// print_r($data);die;
			DB::table('coures_detail')->insertOrIgnore($data);
			DB::table('course_list')->where('url',$url)->update(['status' => '1']);
		}
	}
	public function GetPageListEdin(){
		$host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.

        $profile = new FirefoxProfile();
        $profile->setPreference('general.useragent.override', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:70.0) Gecko/20100101 Firefox/70.0');

       
        $caps = DesiredCapabilities::firefox();
        $profile->setPreference("dom.webdriver.enabled", false);
        $profile->setPreference('useAutomationExtension', false);
        $caps->setCapability(FirefoxDriver::PROFILE, $profile);
        
        $driver = RemoteWebDriver::create($host, $caps);
        $height = 1080;
        $width = 1920;
        $d = new WebDriverDimension($width,$height);
        $driver->manage()->window()->setSize($d);
		$url = 'https://www.linkedin.com/learning-login/?redirect=https%3A%2F%2Fwww.linkedin.com%2Flearning%2F%3Ftrk%3Ddefault_guest_learning&trk=sign_in';
		$driver->get($url);
		sleep(3);
		$driver->findElement(WebDriverBy::cssSelector("input#auth-id-input"))->sendKeys("cutaoto101096@gmail.com");
		$mail_login = $driver->findElement(WebDriverBy::cssSelector("button#auth-id-button"));
		if ($mail_login->isDisplayed()) {
			$mail_login->click();
		}
		sleep(5);
		$driver->findElement(WebDriverBy::cssSelector("input#password"))->sendKeys("a7563674");
		$login = $driver->findElement(WebDriverBy::cssSelector("button.from__button--floating"));
		if ($login->isDisplayed()) {
			$login->click();
		}
		$list = $driver->manage()->getCookies();
		$public_path = public_path();
		foreach ($list as $cookie) {
            //$domain = $cookie->getDomain();
            print_r($cookie);
            $tmp = $cookie['name'].':'.$cookie['value'];
            file_put_contents(public_path('cookie_linkedin.txt'), $tmp.PHP_EOL, FILE_APPEND | LOCK_EX);
        }
		$driver->quit();
		// $html_down = $driver->findElement(WebDriverBy::tagName('html'))->getAttribute('innerHTML');
		// $html = $driver->findElements(WebDriverBy::cssSelector('div.BreadcrumbItem_1pp1zxi > a'));
	}
}
