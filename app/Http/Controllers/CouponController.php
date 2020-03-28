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
class CouponController extends Controller
{
    public function SaveHtml () {
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
		$list_page = DB::table('couponxoo_list')
					->select('id','url')
					->where('parent', '471')
					->where('type', '2')
					->where('status', '0')
					->get();
		foreach ($list_page as $url) {
			$id_url = $url->id;
			$url = $url->url;
			$driver->get($url);
			sleep(5);
			$list = $driver->manage()->getCookies();
			$element = $driver->findElements(WebDriverBy::cssSelector('.discount'));
			$html = $driver->findElement(WebDriverBy::tagName('html'))->getAttribute('innerHTML');
			$public_path = public_path();
			file_put_contents($public_path."/couponbirds/".$id_url.".html", $html);
			DB::table('couponxoo_list')->where('url',$url)->update(['status' => '1']);
		}
	}
	public function InsertUrl() {
		for ($i = 4210; $i <= 4472; $i++) {
			$public_path = public_path();
			$id_url = $i;
			$url = $public_path."/couponbirds/".$i.".html";
			$resp = file_get_contents($url);
			$html = str_get_html($resp);
			// var_dump($url);die;
			$data_link = $html->find('a');
			$main_url = 'https://www.couponbirds.com';
			foreach ($data_link as $value) {
				$data = array();
				$link = $value->href;
				if (!empty($link)) {
					if (strpos($link, '/codes/') !== false or strpos($link, '/coupon/') !== false or strpos($link, '/special/') !== false or strpos($link, '/codes/') !== false) {
						$link = str_replace('?source=nav', '', $link);
						$link = $main_url . $link;
						$data['url'] = $link;
						$data['parent'] = '471';
						$data['type'] = '2';
						$data['status'] = '0';
						// $list_link[] = $link;
						DB::table('couponxoo_list')->insertOrIgnore($data);
					}
				}
			}
		}
		// echo'<pre>';print_r($list_link);die;
	}
	public function AddCouponHtml () {
		$public_path = public_path();
		// var_dump($start);var_dump($end);die;
		// $list_page = DB::table('coupon_list')
					// ->select('id','url')
					// ->whereBetween('id', [$start,$end])
					// ->where('parent', $id_parent)
					// ->where('type', '2')
					// ->get();
		// $main_url = DB::table('coupon_list')
			// ->select('url')
			// ->where('type', '1')
			// ->first();
		// $main_url = $main_url->url;
		// echo '<pre>'; print_r(public_path());die;
		// foreach ($list_page as $url) {
			// if (empty($list_link)) {
				$id_url = '472';
				$url = $public_path."/couponbirds/472.html";
			// }
			// DB::table('coupon_list')->where('url',$url)->update(['status' => '1']);
			
			$resp = file_get_contents($url);
			$html = str_get_html($resp);
			$public_path = public_path();
			// file_put_contents($public_path."/couponupto/".$id_url.".html", $html);
			$find_disc = array();
			$find = array();
			$find_url = array();
			$find_title_c = array();
			$find_desc_c = array();
			$find_expire = array();
			$find_img_shop = array();
			$find_shop_domain = array();
			$find_other_info = array();
			$data = array();
			$advertiser = array();
			$coupon_description = array();
			$coupon_title = array();
			$domain = array();
			$coupon_url = array();
			$coupon_id = array();
			$expire_datetime = array();
			$array_deal = array();
			$discount = array();
			$find = $html->find('div.deal-desc>h2.title>a');
			$find_getdeal = $html->find('div.get-deal a');
			$find_getcode = $html->find('div.get-code a');
			$find_url = $html->find('div');
			$find_desc_c = $html->find('div.deal-desc>h2.title>p.subtitle');
			$find_disc = $html->find('div.deal-info > div.type');
			$find_discount = $html->find('div.deal-info > div.benefit > div.discount');
			$find_img_shop = $html->find('p.d-none');
			$find_shop_domain = $html->find('a.go-store');
			$find_other_info = $html->find('div.rich-content-p a');
			if (!empty($find_img_shop)) {
				foreach ($find_img_shop as $value1) {
					if ($value1->getAttribute('itemprop') == 'image') {
						$img_shop = $value1->plaintext;
					} elseif ($value1->getAttribute('itemprop') == 'name') {
						$shop_name = $value1->plaintext;
					}
				}
			} else {
				$img_shop = '';
				$shop_name = '';
			}
			if (!empty($find_shop_domain)) {
				$link_domain = $find_shop_domain->href;
				$domain_shop = str_replace('/out/', '', $link_domain);
			}
			$other_info = '';
			$shop = array();
			$shop['shop_name'] = $shop_name;
			$shop['shop_domain'] = $domain_shop;
			$shop['image'] = $img_shop;
			$shop['other_info'] = $other_info;
			DB::table('shop_list')->insertOrIgnore($shop);
				// echo '<pre>'; var_dump($img_shop);
			$id_shop = DB::table('shop_list')
						->select('id')
						->where('shop_domain', $domain_shop)
						->first();
			if (!empty($id_shop)) {
				$id_shop = $id_shop->id;
			}
			foreach ($find as $key => $value1) {
				if ($find_img_shop->getAttribute('data-func') == 'showDeal') {
					$array_deal['title'] = $value1->plaintext;
					$deal_url[] = str_replace('/out/', '', $value1->href);
				} elseif ($find_img_shop->getAttribute('data-func') == 'showCode') {
					$url_getcoupon = $find_img_shop->getAttribute('data-url');
					$curl_coupon = curl_init();
						curl_setopt_array($curl_coupon, array(
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_URL => $url_getcoupon,
						CURLOPT_USERAGENT => '',
						CURLOPT_SSL_VERIFYPEER => false
					));
					$response = curl_exec($curl_coupon);
					curl_close($curl_coupon);
					$html_coupon = str_get_html($response);
					$find_coupon = $html_coupon->find('input#copy-code');
					$coupon_id[] = $find_coupon->value;
					$coupon_title[] = $value1->plaintext;
					$code_url[] = str_replace('/out/', '', $value1->href);
					if (empty(trim($find_desc_c[$key]->plaintext, ' '))) {
						$coupon_description[] = '';
					} else {
						$coupon_description[] = trim($find_desc_c[$key]->plaintext, ' ');
					}
					$domain[] = $id_shop;
					$advertiser[] = $value1->getAttribute('data-website');
				}
			}
			foreach ($find_disc as $key => $value1) {
				if ($value1->plaintext == 'Deal') {
					$array_deal['discount'] = trim($find_discount[$key]->plaintext,' ');
					$find_expire = $html->find('div.deal-desc > .expire',);
				} else {
					$discount = trim($find_discount[$key]->plaintext,' ');
					$count_word = strlen($discount);
					$unit = substr($discount, $count_word-1, $count_word);
					if ($unit == '%') {
						$discount['discount_percent'][] = (int)$discount;
						$discount['discount'][] = null;
						$discount['currency'][] = null;
					} elseif($discount == 'SALE') {
						$discount['discount_percent'][] = null;
						$discount['currency'][] = null;
						$discount['discount'][] = null;
					} elseif (filter_var($unit, FILTER_VALIDATE_INT) === false) {
						$discount['currency'][] = $unit;
						$discount['discount'][] = (int)$discount;
						$discount['discount_percent'][] = null;
					} else {
						$discount['discount_percent'][] = null;
						$discount['currency'][] = substr($discount, 0, 1);
						$discount['discount'][] = (int)substr($discount, 1, $count_word);
					}
				}
			}
			foreach ($deal_url as $value1) {
				$find_deal_expire = $html->find('#https://www.couponbirds.com/codes/houzz.com?key='.$value1.'>p.expire');
				if (!empty($find_deal_expire)) {
					$deal_expire = trim($find_deal_expire->plaintext, ' ');
					$array_deal['expire_date'] = str_replace('Expire Date: ', '',$deal_expire);
				} else {
					$array_deal['expire_date'] = '';
				}
			}
			foreach ($code_url as $value1) {
				$find_coupon_expire = $html->find('#https://www.couponbirds.com/codes/houzz.com?key='.$value1.'>p.expire');
				if (!empty($find_coupon_expire)) {
					$coupon_expire = trim($find_coupon_expire->plaintext, ' ');
					if (strpos($coupon_expire, 'Expire') !== false) {
						$coupon_expire_date = str_replace('Expire Date: ', '',$deal_expire);
						$coupon_expire_date = date_create($coupon_expire_date);
						$expire_datetime[] = date_format($coupon_expire_date, 'Y-m-d H:i:s');
					} else {
						$timenow = time();
						$time_2m_ago = $timenow - 5184000;
						$expire_datetime[] = date("Y-m-d H:i:s",$time_2m_ago);
					}
				} else {
					$expire_datetime[] = null;
				}
			}
			// $count[] = count($find_disc);
			foreach ($coupon_id as $key => $value1) {
				$request = array();
				$request['code'] = $value1;
				$request['discount'] = $discount['discount'][$key];
				$request['discount_percent'] = $discount['discount_percent'][$key];
				$request['currency'] = $discount['currency'][$key];
				$request['title'] = $coupon_title[$key];
				$request['description'] = $coupon_description[$key];
				$request['advertiser'] = $advertiser[$key];
				$request['coupon_url'] = $id_url;
				$request['domain'] = $domain[$key];
				$request['begin_date'] = null;
				$request['expired_date'] = $expire_datetime[$key];
				$request['status'] = '1';
				$request['created_at'] = date('Y-m-d H:i:s');
				$request['updated_at'] = null;
				// $data[] = $request;
				// if ($coupon_id[$key] == 'deal') {
					// $request['code'] = '';
					// DB::table('deal_store')->insertOrIgnore($request);
				// } else {
					// DB::table('coupon_store')->insertOrIgnore($request);
				// }
			}
				echo '<pre>'; print_r($request);die;
		// echo '<pre>'; print_r($count);
		}
	// }
}
