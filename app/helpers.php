<?php 
if (! function_exists('downloadFile')) {
    function downloadFile($url, $path) {
        if(file_exists($path)){
        	return false;
        } else {
        	set_time_limit(0);
			//This is the file where we save the    information
			$fp = fopen ($path, 'w+');
			//Here is the file we are downloading, replace spaces with %20
			$ch = curl_init(str_replace(" ","%20",$url));
			curl_setopt($ch, CURLOPT_TIMEOUT, 50);
			// write curl response to file
			curl_setopt($ch, CURLOPT_FILE, $fp); 
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			// get curl response
			curl_exec($ch); 
			curl_close($ch);
			fclose($fp);
			return true;
        }
    }
}
if(!function_exists('httprequest')){
	/* header = array('file') - url file cookie
	
	*/
	function httprequest($url, $method, $timeout = 30, $header = array(), $proxy = array(), $filter = array()){
		$curl = curl_init();
		//print_r($filter);
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => $timeout,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			//CURLOPT_POSTFIELDS => $body,
			CURLOPT_FOLLOWLOCATION => true,

			// CURLOPT_PROXYTYPE => 7,
			// CURLOPT_PROXY => "195.201.192.254",
			// CURLOPT_PROXYPORT => "28982"
		));
		if(!empty($filter)){
			if(isset($filter['file'])){
				$file = $filter['file'];
				curl_setopt($curl, CURLOPT_COOKIEJAR, $file);
				curl_setopt($curl, CURLOPT_COOKIEFILE, $file);
			}
			if($filter['statuscode'] == true){
				curl_setopt($curl, CURLOPT_HEADER  , true);  // we want headers
				curl_setopt($curl, CURLOPT_NOBODY  , true);
			}
		}
		if($method == 'POST'){
			curl_setopt($curl, CURLOPT_POSTFIELDS, $filter['body']);
		}
		if(!empty($proxy)){
			if(!isset($proxy['type'])){
				curl_setopt($curl, CURLOPT_PROXYTYPE, 7);
			} else {
				if($type = 'http'){
					curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    				//$tmp_type = CURLPROXY_HTTP;
	    		} elseif($type == 'socks5'){
	    			curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
	    			//$tmp_type = CURLPROXY_SOCKS5;
	    		} elseif($type == 'socks4'){
	    			curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);
	    			//$tmp_type = CURLPROXY_SOCKS4;
	    		}
				//curl_setopt($curl, CURLOPT_PROXYTYPE, $proxy['type']);
			}
			curl_setopt($curl, CURLOPT_PROXY, $proxy['host']);
			curl_setopt($curl, CURLOPT_PROXYPORT, $proxy['port']);
		}
		if(!empty($header)){
			$params = array();
			foreach ($header as $key => $value) {
				$params[] = $key.":".$value;
			}
			curl_setopt($curl, CURLOPT_HTTPHEADER, $params);
		}
		// if($xmlRequest !== false){
		// 	curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest","content-length: ".strlen($body),
		// "content-type: application/x-www-form-urlencoded; charset=UTF-8","referer: ".$referer,'x-csrf-token:'.$token));
		// }
		
		$response = curl_exec($curl);
		$err = curl_error($curl);
		if($err){
			$res = array('status' => false, 'err' => $err);
			return $res;
		}
		if(!empty($filter)){
			if(isset($filter['statuscode']) && $filter['statuscode'] == true){
				$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
				curl_close($ch);
				$res = array('status' => true,'msg' => $httpcode);
			}
		}
		curl_close($curl);
		return $res = array('status' => true,'msg' => $response);
	}
}
if(!function_exists('getmicrotime')){
function getmicrotime(){ //how to use: $runTime = round($time2 - $time1, 3);
    list($usec, $sec) = explode(" ", microtime()); 
    return ((float)$usec + (float)$sec); 
  }
}
 ?>
