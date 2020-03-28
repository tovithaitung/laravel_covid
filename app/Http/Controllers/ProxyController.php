<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
include 'simple_html_dom.php';
class ProxyController extends Controller
{
    //
    public function runCrawlProxy(){
    	$this->getproxylistcom();
    	$this->pubproxycom();
    	$this->gimmeproxydom();
    	$this->usproxyorg('https://www.us-proxy.org/');
    	$this->usproxyorg('https://free-proxy-list.net/uk-proxy.html');
    	//$this->sockproxynets();
    	//$this->proxyfishcom();
    	//$this->freeproxycz();
    	$this->proxydailycom();
    	$this->openproxyspace();
    	//$this->spysonecom();
    }
    public function runCheckLive(){
    	$total = DB::table('proxy')->where('countDie','<=',5)->where('type','!=','socks4')->count();
    	$page = ceil($total/400);
    	for ($i = 0; $i < $page; $i++) {
    		$cmd = 'nohup php artisan proxy live --min='.($i*400).' --max=400 >/dev/null 2>&1&';
    		exec($cmd);
    	}
    }
    public function liveProxy($limit, $offset){
    	$list = DB::table('proxy')->where('countDie','<=',5)->where('type','!=','socks4')->limit($offset)->offset($limit)->get();
    	//print_r($list);
    	foreach ($list as $item) {

    		$method = 'GET';
	    	$url = 'https://api.tovicorp.com/ip';
	    	$res = httprequest($url,$method, 30, array(), array('host' => $item->ip,'port' => $item->port,'type' => $item->type));
	    	$die = 1;
	    	if($res['status'] == true){
	    		$data = json_decode($res['msg'],true);
	    		print_r($res['msg']);
	    		if(isset($data['ip'])){
	    			$die = 0;
	    		}
	    	}
	    	$time = date('Y-m-d H:i:s',time());
	    	echo $item->ip.'-'.$die.PHP_EOL;
	    	if($die == 1){
	    		$count = $item->countDie + 1;
	    	} else {
	    		$count = $item->countDie;
	    	}
	    	DB::table('proxy')->where('ip',$item->ip)->update(['die' => $die, 'updated_at' => $time,'countDie' => $count]);
    	}
    	
    }
    public function listProxy(Request $request){ 
    	//'SFatnWg8WqTkXe6z'
    	$header = $request->header('x-api-key');
    	$info = DB::table('proxy_key')->where('access_key',$header)->first();
    	if($info){
	    	$access_key = $header;
	    	$time_check = time() - 3600*24;
	    	$time_check = date('Y-m-d H:i:s',$time_check);
    		$sql = "SELECT * FROM proxy where proxy.ip not IN (SELECT proxy_1.ip from proxy as proxy_1, proxy_get where proxy_1.ip = proxy_get.ip and  proxy_get.access_key = '$access_key') and proxy.die = 0 and type != 'socks4' and updated_at >= '$time_check' ORDER BY updated_at LIMIT 1 ";
    		//echo $sql;
    		$list  = DB::select(DB::raw($sql));
    		if(count($list) == 0){
    			$result = array('status' => 'failure', 'message' => 'nodata', 'data' => array());
    		} else {
    			$data = array('host' => $list[0]->ip, 'port' => $list[0]->port, 'type' => $list[0]->type);
    			DB::table('proxy_get')->insertOrIgnore(['ip' => $list[0]->ip,'access_key' => $header]);
    			$result = array('status' => 'success', 'message' => 'data', 'data' => $data);
    		}
    	} else {
    		$result = array('status' => 'failure', 'message' => 'error key', 'data' => array());
            
    	}
    	return response()->json($result);
    }
    public function addKey(Request $request){
    	try {
	    	$header = $request->header('x-api-key');
	    	if($header == 'SFatnWg8WqTkXe6z'){
	    		DB::table('proxy_key')->insertOrIgnore(['access_key' => $request->key]);	    		
	    		$result = array('status' => 'success', 'message' => 'success');
			} else {
	    		$result = array('status' => 'failure', 'message' => 'error key');
	            
	    	}
    		return response()->json($result);
    	} catch (\Exception $e) {
			echo $e->getMessage();die;
		}
    }
    public function getproxylistcom(){
    	$url = 'https://api.getproxylist.com/proxy';
    	$method = 'GET';
    	
    	while(1){
    		$response = httprequest($url, $method);
    		//print_r($response);
	    	if($response['status'] == true){
	    		$msg = json_decode($response['msg'], true);
	    		if(isset($msg['ip'])){
	    			$ip = $msg['ip'];
	    			$port = $msg['port'];
	    			$country = strtolower($msg['country']);
	    			$type = strtolower($msg['protocol']);
	    			$time = date('Y-m-d H:i:s',time());
	    			$data = array('ip' => $ip, 'port' => $port, 'country' => $country, 'type' => $type,'created_at' => $time, 'updated_at' => $time);
	    			DB::table('proxy')->insertOrIgnore($data);
	    		} else {
	    			break;
	    		}
	    	} else {
	    		break;
	    	}
    	}
    }
    public function pubproxycom(){
    	$url = 'http://pubproxy.com/api/proxy';
    	$method = 'GET';
    	
    	while(1){
    		$response = httprequest($url, $method);
    		sleep(2);
	    	if($response['status'] == true){
	    		$msg = json_decode($response['msg'], true);
	    		if(isset($msg['data'][0]['ip'])){
	    			$ip = $msg['data'][0]['ip'];
	    			$port = $msg['data'][0]['port'];
	    			$country = strtolower($msg['data'][0]['country']);
	    			$type = strtolower($msg['data'][0]['type']);
	    			$time = date('Y-m-d H:i:s',time());
	    			$data = array('ip' => $ip, 'port' => $port, 'country' => $country, 'type' => $type,'created_at' => $time, 'updated_at' => $time);
	    			DB::table('proxy')->insertOrIgnore($data);
	    		} else {
	    			break;
	    		}
	    	} else {
	    		break;
	    	}
	    	
    	}
    }
    public function gimmeproxydom(){
    	$url = 'https://gimmeproxy.com/api/getProxy';
    	$method = 'GET';
    	
    	while(1){
    		$response = httprequest($url, $method);
    		print_r($response);
	    	if($response['status'] == true){
	    		$msg = json_decode($response['msg'], true);
	    		if(isset($msg['ip'])){
	    			$ip = $msg['ip'];
	    			$port = $msg['port'];
	    			$country = strtolower($msg['country']);
	    			$type = strtolower($msg['protocol']);
	    			$time = date('Y-m-d H:i:s',time());
	    			$data = array('ip' => $ip, 'port' => $port, 'country' => $country, 'type' => $type,'created_at' => $time, 'updated_at' => $time);
	    			DB::table('proxy')->insertOrIgnore($data);
	    		} else {
	    			break;
	    		}
	    	} else {
	    		break;
	    	}
    	}
    }
    public function freeproxylistcom(){
    	$url = 'https://free-proxy-list.net/';
    	$method = 'GET';
    	$response = httprequest($url, $method);
		if($response['status'] == true){
			$html = $response['msg'];
			$dom = str_get_html($html);
			if($dom){
				$table = $dom->find('#proxylisttable',0);
				$list = $table->find('tbody tr');
				foreach ($list as $item) {
					$detail = $item->find('td');
					$ip = $detail[0]->plaintext;
					$port = $detail[1]->plaintext;
					$country = strtolower($detail[2]->plaintext);
					$type = 'http';
					$time = date('Y-m-d H:i:s',time());
	    			$data = array('ip' => $ip, 'port' => $port, 'country' => $country, 'type' => $type,'created_at' => $time, 'updated_at' => $time);
	    			DB::table('proxy')->insertOrIgnore($data);
				}
			}
		}
    }
    public function usproxyorg($url){
    	//$url = 'https://www.us-proxy.org/';
    	$method = 'GET';
    	$response = httprequest($url, $method);
		if($response['status'] == true){
			$html = $response['msg'];
			$dom = str_get_html($html);
			if($dom){
				$table = $dom->find('#proxylisttable',0);
				$list = $table->find('tbody tr');
				foreach ($list as $item) {
					$detail = $item->find('td');
					$ip = $detail[0]->plaintext;
					$port = $detail[1]->plaintext;
					$country = strtolower($detail[2]->plaintext);
					$type = 'http';
					$time = date('Y-m-d H:i:s',time());
	    			$data = array('ip' => $ip, 'port' => $port, 'country' => $country, 'type' => $type,'created_at' => $time, 'updated_at' => $time);
	    			DB::table('proxy')->insertOrIgnore($data);
				}
			}
		}
    }
    public function sockproxynets(){
    	$url = 'https://www.socks-proxy.net/';
    	$method = 'GET';
    	//echo $url;
    	$response = httprequest($url, $method);
    	//print_r($response);
		if($response['status'] == true){
			$html = $response['msg'];
			$dom = str_get_html($html);
			if($dom){
				$table = $dom->find('#proxylisttable',0);
				$list = $table->find('tbody tr');
				foreach ($list as $item) {
					$detail = $item->find('td');
					$ip = $detail[0]->plaintext;
					$port = $detail[1]->plaintext;
					$country = strtolower($detail[2]->plaintext);
					$type = strtolower($detail[4]->plaintext);
					$time = date('Y-m-d H:i:s',time());
					//echo $ip;
	    			$data = array('ip' => $ip, 'port' => $port, 'country' => $country, 'type' => $type,'created_at' => $time, 'updated_at' => $time);
	    			DB::table('proxy')->insertOrIgnore($data);
				}
			}
		}
    }
    public function proxyfishcom(){
    	
    	$url = 'https://www.proxyfish.com/proxylist';
    	$method = 'GET';
    	//echo $url;
    	$response = httprequest($url, $method, 30, array('user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36', array('host' => 'http://localhost', 'port' =>'9050'), array(), 1));
    	print_r($response);
		if($response['status'] == true){
			$html = $response['msg'];
			$dom = str_get_html($html);
			if($dom){
				$table = $dom->find('#datatable',0);
				$list = $table->find('tbody tr');
				foreach ($list as $item) {
					$detail = $item->find('td');
					$ip = $detail[1]->plaintext;
					$port = $detail[2]->plaintext;
					//$country = strtolower($detail[2]->plaintext);
					$type = strtolower($detail[6]->plaintext);
					$time = date('Y-m-d H:i:s',time());
					//echo $ip;
	    			$data = array('ip' => $ip, 'port' => $port, 'type' => $type,'created_at' => $time, 'updated_at' => $time);
	    			DB::table('proxy')->insertOrIgnore($data);
				}
			}
		}
    }
    public function freeproxycz(){
    	$url = 'http://free-proxy.cz/';
    	$method = 'GET';
    	//echo $url;
    	$response = httprequest($url, $method, 300, array('user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36'));
    	print_r($response);
		if($response['status'] == true){
			$html = $response['msg'];
			$dom = str_get_html($html);
			if($dom){
				$table = $dom->find('#proxy_list',0);
				$list = $table->find('tbody tr');
				foreach ($list as $item) {
					$detail = $item->find('td');
					$ip = $detail[0]->plaintext;
					$port = $detail[1]->plaintext;
					//$country = strtolower($detail[2]->plaintext);
					$type = strtolower($detail[2]->plaintext);
					$time = date('Y-m-d H:i:s',time());
					//echo $ip;
	    			$data = array('ip' => $ip, 'port' => $port, 'type' => $type,'created_at' => $time, 'updated_at' => $time);
	    			DB::table('proxy')->insertOrIgnore($data);
				}
			}
		}
    }
    public function proxydailycom(){
    	
    	$url = 'https://proxy-daily.com/';
    	$method = 'GET';
    	//echo $url;
    	$response = httprequest($url, $method, 300, array('user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36'));
    	if($response['status'] == true){
			$html = $response['msg'];
			$dom = str_get_html($html);
			if($dom){

				$list = $dom->find('.freeProxyStyle');
				foreach ($list as $key => $item) {
					$tmp = $item->plaintext;
					$tmp = explode(" ", $tmp);
					foreach ($tmp as $detail) {
						if(!empty($detail)){
							$tmp_detail = explode(':', $detail);
							$ip = $tmp_detail[0];
							$port = $tmp_detail[1];
							if($key == 0){
								$type = 'http';
							} elseif($key == 1){
								$type = 'socks4';
							} elseif($key == 2){
								$type = 'socks5';
							}
							$time = date('Y-m-d H:i:s',time());
							$data = array('ip' => $ip, 'port' => $port, 'type' => $type,'created_at' => $time, 'updated_at' => $time);
	    					DB::table('proxy')->insertOrIgnore($data);
						}
					}
	    			
				}
			}
		}
    }
    public function openproxyspace(){
    	$nanotime = system('date +%s%N');
    	//print_r($nanotime);
    	$url = 'https://api.openproxy.space/list?skip=0&ts='.$nanotime;
    	$method = 'GET';
    	//echo $url;
    	$response = httprequest($url, $method, 300, array('user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36'));
    	if($response['status'] == true){
    		$data = $response['msg'];
    		$data = json_decode($data, true);
    		foreach ($data as $item) {
    			if($item['title'] == 'FRESH SOCKS5'){
    				$url_list = 'https://openproxy.space/list/'.$item['code'];
    				$res = httprequest($url_list, $method, 300, array('user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36'));
    				if($res['status'] == true){
    					$str_regex = '/items:(.*?),active/';
    					preg_match_all($str_regex, $res['msg'], $matchs);
    					if(count($matchs) >= 2){
    						$tmp = $matchs[1];
    						foreach ($tmp as $proxy) {
    							$type = 'socks5';
    							$list_proxy = json_decode($proxy, true);
    							foreach ($list_proxy as $item_proxy) {
    								$tmp_proxy = explode(':', $item_proxy);
    								$time = date('Y-m-d H:i:s',time());
    								$ip = $tmp_proxy[0];
    								$port = $tmp_proxy[1];
									$data = array('ip' => $ip, 'port' => $port, 'type' => $type,'created_at' => $time, 'updated_at' => $time);
			    					DB::table('proxy')->insertOrIgnore($data);
    							}
    							
    						}
    					}
    				}
    			} elseif($item['title'] == 'FRESH HTTP/S'){
    				$url_list = 'https://openproxy.space/list/'.$item['code'];
    				$res = httprequest($url_list, $method, 300, array('user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36'));
    				if($res['status'] == true){
    					$str_regex = '/items:(.*?),active/';
    					preg_match_all($str_regex, $res['msg'], $matchs);
    					if(count($matchs) >= 2){
    						$tmp = $matchs[1];
    						foreach ($tmp as $proxy) {
    							$type = 'http';
    							$list_proxy = json_decode($proxy, true);
    							foreach ($list_proxy as $item_proxy) {
    								$tmp_proxy = explode(':', $item_proxy);
    								$time = date('Y-m-d H:i:s',time());
    								$ip = $tmp_proxy[0];
    								$port = $tmp_proxy[1];
									$data = array('ip' => $ip, 'port' => $port, 'type' => $type,'created_at' => $time, 'updated_at' => $time);
			    					DB::table('proxy')->insertOrIgnore($data);
    							}
    							
    						}
    					}
    				}
    			}
    		}
    	}
    }
    public function spysonecom($url = 'http://spys.one/en/socks-proxy-list/'){
    	//$url = 'http://spys.one/en/socks-proxy-list/';
    	$file = public_path('proxy.txt');
    	$method = 'GET';
    	$res = httprequest($url, $method, 300, array('user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36'), array(), array('statuscode' => false));
    	print_r($res);
    	if($res['status'] == true){
			$html = $res['msg'];
			$dom_tmp = str_get_html($html);
			if($dom_tmp){
				$list = $dom_tmp->find('input');
				foreach ($list as $input_item) {
					if($input_item->value != ''){
						$code = $input_item->value;
						$data = 'xx0='.$code.'&xpp=5&xf1=0&xf2=2&xf4=0&xf5=2';
						$response = httprequest($url,'POST', 300, array('user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36','Content-Length: 66','Content-Type: application/x-www-form-urlencoded','Referer: http://spys.one/en/socks-proxy-list/'), array(), array('body' => $data, 'statuscode' => false));
						print_r($data);
						$this->processHtml($response['msg']);

					}
				}
			}

		}
    	//echo $url;
    	/*$response = httprequest($url, $method, 300, array('user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36'));
    	if($response['status'] == true){
			$html = $response['msg'];
			$dom = str_get_html($html);
			if($dom){
				$tr = $dom->find('tr.spy1x');
				foreach ($tr as $item) {
					$td = $item->find('td',1);
					if($td){
						$link = $td->find('a',0)->href;
						$tmp_link = 'http://spys.one'.$link;
						$res = httprequest($tmp_link, $method, 300, array('user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Safari/537.36'));
						if($res['status'] == true){
							$html = $res['msg'];
							$dom_tmp = str_get_html($html);
							if($dom_tmp){
								$list = $dom_tmp->find('input');
								foreach ($list as $input_item) {
									if($input_item->value != ''){
										$code = $input_item->value;

									}
								}
							}

						}
						die;
					}
				}
			}
		}*/
    }
    public function processHtml($html){
    	$dom_tmp = str_get_html($html);
    	if($dom_tmp){
    		$tmp_var = array();
    		$script = $dom_tmp->find('body',0)->find('script',2)->outertext;
    		$tmp = str_replace('<script type="text/javascript">', '', $script);
    		$tmp = str_replace('</script>', '', $tmp);
    		$tmp = explode(';', $tmp);
    		foreach ($tmp as $item) {
    			$tmp_item = explode('^', $item);
    			if(count($tmp_item) >= 2){
    				//echo $tmp_item[0].PHP_EOL;
    				$var = explode('=', $tmp_item[0]);
    				$tmp_var[$var[0]] = $var[1];
    			}
    		}
			$list = $dom_tmp->find('.spy14');
			foreach ($list as $input_item) {
				$str = $input_item->outertext;
				// /echo $str;
				$str_regex = '/\+(.*?)\)<\/script>/s';
				preg_match($str_regex , $str, $matchs);
				if(count($matchs) >= 2){
					$tmp_ss = explode('+', $matchs[1]);
					$port = '';
					//print_r($tmp_ss);die;
					foreach ($tmp_ss as $value) {
						//echo $value;
						foreach ($tmp_var as $key => $p) {
							//echo $key;
							if(strpos($value, $key) !== false){
								$port .= $p;
							}
						}
					}
					$ip = $input_item->plaintext;
					echo $ip.':'.$port.PHP_EOL;
				}
			}

		}
    }
   
}
