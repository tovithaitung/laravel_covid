<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Domain;
use App\Setting;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\Remote;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use App\Imports\ListCheckImport;
use Maatwebsite\Excel\Facades\Excel;
use DB;
require_once '/var/www/html/keywords/app/helpers.php';
require_once 'simple_html_dom.php';
ini_set('memory_limit','-1');
class DomainController extends Controller
{
    //
    protected $dr = -1;
    public function checkSubDomain(){
    	$list = array('.co.uk');
    	$listDomain = Domain::where('domain_name','LIKE','%.%.%')->where('is_del',0)->pluck('domain_name');
    	foreach ($listDomain as $p) {
    		foreach ($list as $value) {
    			if(strpos($p, $value) == false){
    				Domain::where('domain_name',$p)->update(['is_del' => 1]);
    				print_r($p.PHP_EOL);
    			}
    		}
    		
    	}
    }
    public function autoDomcop(){
        // update rdomain;
        /*$list = DB::table('domain_domcop_checked')->where('statusRdomain',1)->where('is_del', 0)->where('maj_tf', '>=', 10)->where('index', '>', 0)->get();
        echo count($list);die;
        foreach ($list as $p) {
            if($p->RDomain_detail != ''){
                echo $p->domain.PHP_EOL;
                $domains = json_decode($p->RDomain_detail,true);
                DB::table('domain_domcop_select')->where('domain',$p->domain)->update(['RDomain' => count($domains)]);
            }
        }*/
        //$limit = $page * 5;

        //$domain_request = DB::select(DB::raw("SELECT domain, code FROM domain_requests WHERE code IS NOT NULL limit $page, 5"));
        /*$domain_request = DB::table('domain_requests')->whereNotNull('code')->get();
        foreach ($domain_request as $rq_domain) {

            $rdomain = $rq_domain->domain;
            $code = $rq_domain->code;
            var_dump($rdomain);

            $result = DB::select(DB::raw("SELECT domain FROM domain_domcop_checked WHERE RDomain_detail LIKE '%".$rdomain."%' and type is null"));

            foreach ($result as $item) {
                $domain = $item->domain;
                DB::table('domain_domcop_checked')->where('domain',$domain)->update(['type'=>$code]);
                DB::table('domain_domcop_select')->where('domain',$domain)->update(['type'=>$code]);
                var_dump('table updated !');
            }


        }*/

        $total = DB::table('domain_domcop_select')->where('statusRdomain',1)->where('statusPre',0)->count();
        //$total = DB::table('domain_domcop_select')->where('type',2)->count();
        echo $total;
        try {
            //$min = $first->domain_out_id;
            //$max = $last->domain_out_id;
            $min = 0;
            $max = $total;
            while(1){
                $cmd = 'nohup php artisan domain premium --min='.$min.' >/dev/null 2>&1&';
                exec($cmd);
                echo $cmd;
                //$this->runcheckStatusCode($min, $min +5000);
                $min = $min + 10000;
                if($min >= $max){
                    break;
                }
            }
        } catch (\Exception $e) {
            echo 'het domain';
        }
    }
    public function premiumDomain($page){
        $listDomain = DB::select(DB::raw('SELECT domain from domain_domcop_select where statusRdomain = 1 and statusPre = 0 limit '.$page[0].',10000'));
        echo count($listDomain);
        foreach ($listDomain as $p) {
            $info = DB::table('domain_domcop_checked')->where('domain',$p->domain)->first();
            //dd($info);die;
            if(!empty($info)){
                $refer = json_decode($info->RDomain_detail, true);
                $count = 0;
                foreach ($refer as $tmp) {
                    if($tmp['domain_rating'] >= 80 && $tmp['do_follow'] != 0){
                        $count++;
                    }
                }
                DB::table('domain_domcop_select')->where('domain',$p->domain)->update(['RDomain_count' => $count,'statusPre' => 1]);
            }
        }
    }
    public function checkDomCop($page){
    	$list = array('.co','.com','.net','.org','.info','.me','.website','.io','.us');
    	// $listDomain = DB::table('domain_domcop_checked')->where('is_del',0)->pluck('domain');
        $domcop = new DomCopController();
    	//$listDomain	= DB::table('domain_domcop_select')->where('is_del',0)->get();
        $listDomain = DB::select(DB::raw('SELECT domain from domain_domcop_select where is_del = 0 limit '.$page[0].',200000'));
    	foreach ($listDomain as $p) {
    		//$check = false;
    		// foreach ($list as $value) { 
    			
    		// 	if(strpos($p->domain, $value) != false){
      //               if($p->RDomain_detail != ''){

      //                   $domains = json_decode($p->RDomain_detail,true);
      //                   DB::table('domain_domcop_select')->where('domain',$p->domain)->update(['RDomain' => count($domains)]);
      //               }
    		// 		//DB::table('domain_domcop_checked')->where('domain',$p)->update(['is_del' => 0]);
    		// 		$check = true;
    				
    		// 		break;
    				
    		// 	}
    			
    		// }
            // if($domcop->checkTLD($p->domain) == true){
            //     print_r($p->domain.PHP_EOL);
            //     DB::table('domain_domcop_select')->where('domain',$p->domain)->update(['is_del' => 1]);
            // }
   //  		if($check == false){
			// 	DB::table('domain_domcop_select')->where('domain',$p->domain)->update(['is_del' => 1]);
			// 	print_r($p->domain.PHP_EOL);
			// } 
    		
    	}
    	// $a = array('.co.uk');
    	// $listDomain = DB::table('domain_domcop_select')->where('domain','LIKE','%.%.%')->where('is_del',0)->pluck('domain');
    	// foreach ($listDomain as $p) {
    	// 	foreach ($a as $value) {
    	// 		if(strpos($p, $value) == false){
    	// 			DB::table('domain_domcop_select')->where('domain',$p)->update(['is_del' => 1]);
					// print_r($p.PHP_EOL);
    	// 		}
    	// 	}
    		
    	// }
    }
    public function checkStatusCode(){
    	$first = Domain::where('dr','>=',$this->dr)->where('code',0)->where('status',0)->orderBy('domain_out_id','asc')->first();
    	$last = Domain::where('dr','>=',$this->dr)->where('code',0)->where('status',0)->orderBy('domain_out_id','desc')->first();
    	try {
    		$min = $first->domain_out_id;
	    	$max = $last->domain_out_id;
	    	while(1){
	    		$cmd = 'nohup php artisan domain code --min='.$min.' --max='.($min + 50000).' >/dev/null 2>&1&';

	    		exec($cmd);
	    		//$this->runcheckStatusCode($min, $min +5000);
	    		$min = $min + 50000;
	    		if($min >= $max){
	    			break;
	    		}
	    	}

    	} catch (\Exception $e) {
    		echo 'het domain';
    	}
    	    	/*
    	$list = Domain::where('dr','>=',2)->where('code',0)->where('status',0)->orderBy('dr','asc')->pluck('domain_name');
    	foreach ($list as $value) {
    		print_r($value.PHP_EOL);
    		$res = httprequest('http://'.$value, 'GET', 30, array(), array(), array('statuscode' => true));
    		print_r($res);
    		if($res['status'] == false){
    			if($res['err'] == 'Could not resolve host: '.$value.'; Unknown error'){
    				Domain::where('domain_name',$value)->update(['code' => 404,'status' => 1]);
    			} else {
    				Domain::where('domain_name',$value)->update(['status' => 1]);
    			}
    			
    		} else {
    			Domain::where('domain_name',$value)->update(['status' => 1]);
    		}
    	}*/
    }
    public function runcheckStatusCode($min, $max){
    	$this->checkSubDomain();
    	$list = Domain::where('dr','>=',$this->dr)->where('domain_out_id','>=',$min)->where('domain_out_id','<',$max)->where('code',0)->where('status',0)->where('is_del',0)->orderBy('dr','desc')->pluck('domain_name');
    	foreach ($list as $value) {
    		print_r($value.PHP_EOL);
    		$res = httprequest('http://'.$value, 'GET', 30, array(), array(), array('statuscode' => true));
    		print_r($res);
    		if($res['status'] == false){
    			if($res['err'] == 'Could not resolve host: '.$value.'; Unknown error'){
    				Domain::where('domain_name',$value)->update(['code' => 404,'status' => 1]);
    			} else {
    				Domain::where('domain_name',$value)->update(['status' => 1]);
    			}
    			
    		} else {
    			Domain::where('domain_name',$value)->update(['status' => 1]);
    		}
    	}
    }
    public function runcheckExpired(){
    	//$this->checkSubDomain();
    	//$setting = Setting::where('name','minDR')->first();

    	//$first = Domain::where('code',404)->where('statusExpire',0)->where('is_del',0)->where('dr','>=',$setting->content)->orderBy('domain_out_id','asc')->first();
    	//$first = Domain::where('statusWayback',1)->whereNotNull('total_index')->orderBy('domain_out_id','asc')->first();
    	//$last = Domain::where('code',404)->where('statusExpire',0)->where('is_del',0)->where('dr','>=',$setting->content)->orderBy('domain_out_id','desc')->first();
    	//$last = Domain::where('statusWayback',1)->whereNotNull('total_index')->orderBy('domain_out_id','desc')->first();
    	$total = DB::table('domain_domcop_checked')->where('is_del',0)->where('statusExpire',0)->where('maj_tf','>=',10)->count();
        //$total = DB::table('domain_domcop_select')->where('type',2)->count();
    	try {
    		//$min = $first->domain_out_id;
	    	//$max = $last->domain_out_id;
	    	$min = 0;
	    	$max = $total;
	    	while(1){
	    		$cmd = 'nohup php artisan domain expire --min='.$min.' --max='.($min + 5000).' >/dev/null 2>&1&';
	    		exec($cmd);
	    		echo $cmd;
	    		//$this->runcheckStatusCode($min, $min +5000);
	    		$min = $min + 5000;
	    		if($min >= $max){
	    			break;
	    		}
	    	}
    	} catch (\Exception $e) {
    		echo 'het domain';
    	}
    	
    }
    public function checkExpired($min,$max){
    	//$setting = Setting::where('name','minDR')->first();
    	//$domains = Domain::where('code',404)->where('statusExpire',0)->where('dr','>=',$setting->content)->where('domain_out_id','>=',$min)->where('domain_out_id','<',$max)->orderBy('dr','desc')->pluck('domain_name');
    	//$domains = Domain::where('statusWayback',1)->whereNotNull('total_index')->where('isBuy',1)->orderBy('total_index','desc')->pluck('domain_name');
       // $domains = DB::table('domain_domcop_checked')->where('is_del',0)->where('statusExpire',0)->whereNotNull('RDomain_detail')->pluck('domain');
    	//print_r($min);die;
       	$domains = DB::select(DB::raw('SELECT domain FROM domain_domcop_checked where is_del = 0 and statusExpire = 0 and maj_tf >= 10 limit '.$min[0].',5000'));
       	//$domains = DB::select(DB::raw('SELECT domain FROM domain_domcop_select where type = 2 limit '.$min[0].',100'));
    	$i = 0;
    	foreach ($domains as $tmp) {
    		$domain = $tmp->domain;
    		echo $domain;
    		try {
    			$update = true;
    		if($i == 0){

	    		$host = 'http://localhost:4444/wd/hub'; // this is the default
		        $USE_FIREFOX = true; // if false, will use chrome.
		        $caps = DesiredCapabilities::chrome();
		        $prefs = array();
		        $options = new ChromeOptions();
		        $prefs['profile.default_content_setting_values.notifications'] = 2;
		        $options->setExperimentalOption("prefs", $prefs);
		        $caps->setCapability(ChromeOptions::CAPABILITY, $options);

                $capabilities = DesiredCapabilities::firefox();
                $capabilities->setCapability(
                    'moz:firefoxOptions',
                   ['args' => ['-headless']]
                );
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
	        
		        $driver->get("https://www.godaddy.com/domainsearch/find");
		        sleep(5);
		        $driver->findElement(WebDriverBy::className('search-input'))->click();
		        sleep(1);
		        $driver->findElement(WebDriverBy::className('search-input'))->sendKeys($domain);
		        sleep(1);
		        $driver->findElement(WebDriverBy::className('search-action'))->click();
		        sleep(10);
		        try {
		        	$html = $driver->findElement(WebDriverBy::className('exact-match-wrap'))->getAttribute('innerHTML');
			        $dom = str_get_html($html);
			        if($dom){
			        	$textDomain = $dom->find('.dpp-price',0);

						if($textDomain){
							//print_r($textDomain->innertext);
							echo 'thanh cong';
							//$text = $textDomain->plaintext;
							//f($text == $domain.' is available'){
							$price = $dom->find('.dpp-price',0)->plaintext;
							$price = str_replace('€', '', $price);
    						$price = str_replace(',', '', $price);
							//Domain::where('domain_name',$domain)->update(['statusExpire' => 1,'isBuy' => 1,'price' => $price]);
                            DB::table('domain_domcop_checked')->where('domain',$domain)->update(['price' => $price,'statusExpire' => 1]);
                            DB::table('domain_domcop_select')->where('domain',$domain)->update(['price' => $price]);
							$update = false;
							//}
						} else {
							$textDomain = $dom->find('.price',0);
							if($textDomain){
								$price = $dom->find('.price',0)->plaintext;
								$price = str_replace('€', '', $price);
    							$price = str_replace(',', '', $price);
								//Domain::where('domain_name',$domain)->update(['statusExpire' => 1,'isBuy' => 1,'price' => $price]);
                                DB::table('domain_domcop_checked')->where('domain',$domain)->update(['price' => $price,'statusExpire' => 1]);
                                DB::table('domain_domcop_select')->where('domain',$domain)->update(['price' => $price]);
								$update = false;

							} 
						}
			        } else {
			        	$update = true;
			        }
		        } catch (\Exception $e) {
		        	$update = false;
		        }
		        
		        if($update == true){
		    		//Domain::where('domain_name',$domain)->update(['statusExpire' => 1,'isBuy' => 0,'price' => '0']);
                    DB::table('domain_domcop_checked')->where('domain',$domain)->update(['statusExpire' => 1,'price' => 0]);
                    DB::table('domain_domcop_select')->where('domain',$domain)->update(['price' => 0]);

		    	}
	    	} else {
	    		$driver->findElement(WebDriverBy::className('search-input'))->click();
	        	sleep(1);
	    		$driver->findElement(WebDriverBy::className('search-input'))->clear();
	        	sleep(1);
	        	$driver->findElement(WebDriverBy::className('search-input'))->click();
		        sleep(1);
		        $driver->findElement(WebDriverBy::className('search-input'))->sendKeys($domain);
	        	sleep(1);
	        	$driver->findElement(WebDriverBy::className('search-action'))->click();
		        sleep(10);
		        try {
		        	$html = $driver->findElement(WebDriverBy::className('exact-match-wrap'))->getAttribute('innerHTML');
			        //print_r($html);
			        $dom = str_get_html($html);
			        if($dom){
			        	$textDomain = $dom->find('.dpp-price',0);

						if($textDomain){
							//print_r($textDomain->innertext);
							echo 'thanh cong';
							//$text = $textDomain->plaintext;
							//f($text == $domain.' is available'){
							$price = $dom->find('.dpp-price',0)->plaintext;
							$price = str_replace('€', '', $price);
    						$price = str_replace(',', '', $price);
							//Domain::where('domain_name',$domain)->update(['statusExpire' => 1,'isBuy' => 1,'price' => $price]);
                            DB::table('domain_domcop_checked')->where('domain',$domain)->update(['price' => $price,'statusExpire' => 1]);
                            DB::table('domain_domcop_select')->where('domain',$domain)->update(['price' => $price]);
							$update = false;
							//}
						} else {
							$textDomain = $dom->find('.price',0);
							
							if($textDomain){
								$price = $dom->find('.price',0)->plaintext;
								$price = str_replace('€', '', $price);
    							$price = str_replace(',', '', $price);
								//Domain::where('domain_name',$domain)->update(['statusExpire' => 1,'isBuy' => 1,'price' => $price]);
                                DB::table('domain_domcop_checked')->where('domain',$domain)->update(['price' => $price,'statusExpire' => 1]);
                                DB::table('domain_domcop_select')->where('domain',$domain)->update(['price' => $price]);
								$update = false;

							}
						}
			        } else {
			        	$update = true;
			        }
		        } catch (\Exception $e1) {
		        	$update = false;
		        }
		        
		        if($update == true){
		    		//Domain::where('domain_name',$domain)->update(['statusExpire' => 1,'isBuy' => 0,'price' => '0']);
                    DB::table('domain_domcop_checked')->where('domain',$domain)->update(['statusExpire' => 1,'price' => 0]);
                    DB::table('domain_domcop_select')->where('domain',$domain)->update(['price' => 0]);
		    	}
	    	}
	    	
    		} catch (\Exception $errrr) {
    			//Domain::where('domain_name',$domain)->update(['statusExpire' => 1,'isBuy' => 0,'price' => '0']);
                DB::table('domain_domcop_checked')->where('domain',$domain)->update(['statusExpire' => 1,'price' => 0]);
                DB::table('domain_domcop_select')->where('domain',$domain)->update(['price' => 0]);
    		}
    		$i++;
    		//$url = 'https://www.godaddy.com/domainsearch/find?isc=GPPTCUST&checkAvail=1&tmskey=&domainToCheck='.$domain;
    		//print_r($url);
    		
	    	
    	}
    }
    public function checkWayback(){

    	//https://archive.org/wayback/available?url=office.com
    	$domains = Domain::where('isBuy',1)->where('statusWayback',0)->pluck('domain_name');
    	foreach ($domains as $domain) {
    		$url = 'https://web.archive.org/__wb/sparkline?url='.$domain.'&collection=web&output=json';
    		$res = httprequest($url, 'GET', 30);
    		$check = true;
    		if($res['status'] == true){
    			$tmp = json_decode($res['msg'],true);
    			if(isset($tmp['first_ts']) && isset($tmp['last_ts'])){
    				Domain::where('domain_name',$domain)->update(['create_on' =>$tmp['first_ts'],'expire_on' => $tmp['last_ts']]);
    				$check = false;
    			}
    		}
    		if($check = true){
    			Domain::where('domain_name',$domain)->update(['statusWayback' => 1]);
    		}
    	}
    }
    public function historyWayBack(){
    	//https://web.archive.org/__wb/sparkline?url=inflikr.co&collection=web&output=json
    	//https://web.archive.org/__wb/calendarcaptures?url=omidiyeh.net&selected_year=2013
    }
    public function importFileDomain($domain, $image){

		$list = Excel::toArray(new ListCheckImport, public_path('storage/'.$image[0]),null, \Maatwebsite\Excel\Excel::CSV);
        $info = DB::table('domain_requests')->updateOrInsert(['domain' => $domain[0]],['created_at' =>date('Y-m-d h:i:s',time())]);

        $id = DB::getPdo()->lastInsertId();
        foreach ($list[0] as $key => $tmp) {
            if($key > 0){
                //$a = explode(",", $tmp[0]);
                //print_r($tmp);die;
                echo $tmp[9];
                if($tmp[3] == '-'){
                    $tmp[3] = 0;
                }
                $info = Domain::updateOrCreate(['domain_name' => $tmp[1]],['domain_name' => $tmp[1],'link_out' => $domain[0],'dr' => $tmp[2],'RDomain' => $tmp[9],'ahrefs_rank' => $tmp[3],'created_at' => date('Y-m-d H:i:s')]);
                $id_out = $info->domain_out_id;
                DB::table('domain_link')->updateOrInsert(['domain_request_id' => $id, 'domain_out_id' => $id_out],['domain_request_id' => $id, 'domain_out_id' => $id_out]);
            }
        }
    }
    public function importFileLink(){
        $list = Excel::toArray(new ListCheckImport, public_path('toan_kw.xlsx'),null, \Maatwebsite\Excel\Excel::XLSX);
        foreach ($list[0] as $key => $tmp) {
            if($key > 0){
                print_r($tmp);die;
            }
        }
    }
    public function checkDomainAuction(){
    	$host = 'http://localhost:4444/wd/hub'; // this is the default
        $USE_FIREFOX = true; // if false, will use chrome.
        $caps = DesiredCapabilities::chrome();
        $prefs = array();
        $options = new ChromeOptions();
        $prefs['profile.default_content_setting_values.notifications'] = 2;
        $options->setExperimentalOption("prefs", $prefs);
        $caps->setCapability(ChromeOptions::CAPABILITY, $options);
        $capabilities = DesiredCapabilities::firefox();

	    // $capabilities->setCapability(
	    //     'moz:firefoxOptions',
	    //    ['args' => ['-headless']]
	    // );
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
    
        $driver->get("https://auctions.godaddy.com/");
		sleep(10);
        $html = $driver->findElement(WebDriverBy::id('tblSearchResults'))->getAttribute('innerHTML');
        $dom = str_get_html($html);
        //print_r($html);
        $domains = array();
        $this->parseDomain($dom);
        $tmp_select = 2;
        while (1) {
        	try {
        		$driver->findElement(WebDriverBy::xpath("/html[@id='auctionsCSS']/body[@class='js-focus-visible']/table[@id='tblMasterPageContents']/tbody/tr[@id='auctionsTD']/td/form[@id='aspnetForm']/table[3]/tbody/tr/td/div[@id='tblSearchResults']/table/tbody/tr[2]/td/table/tbody/tr/td[2]/table/tbody/tr/td/table/tbody/tr/td[4]/a"))->click();
        		sleep(10);
        		$html = $driver->findElement(WebDriverBy::id('tblSearchResults'))->getAttribute('innerHTML');
        		$dom = str_get_html($html);
        		//print_r($html);
        		$this->parseDomain($dom);
        	} catch (\Exception $e) {
        		//echo 'err';
        		$selectElement = $driver->findElement(WebDriverBy::id('ddlPredefinedSelect'));

				// Now pass it to WebDriverSelect constructor
				$select = new WebDriverSelect($selectElement);
				$select->selectByIndex($tmp_select);
				$tmp_select++;
				sleep(5);
        	}	
            
        }
        
	}
	public function parseDomain($dom){
		$domains = array();
		if($dom){
		$table = $dom->find('table#search-table',0);
		if($table){
			$list = $dom->find('tr.srRow1');
			if($list){
				foreach ($list as $key => $item) {
					$name = $item->find('td',3)->plaintext;
					$traffic = $item->find('td',5)->plaintext;
					$es_value = $item->find('td',6)->plaintext;
					$price = $item->find('td',7)->plaintext;
					$data = array(
						'domain_name' => $name,
						'traffic' => $traffic,
						'es_value' => $es_value,
						'price' => $price,
					);
					echo $name.PHP_EOL;
					if(DB::table('domain_auction')->where('domain_name',$name)->count() == 0){
						DB::table('domain_auction')->insert($data);
					}
					
				}
			}
			$list = $dom->find('tr.srRow2');
			if($list){
				foreach ($list as $key => $item) {
					$name = $item->find('td',3)->plaintext;
					$traffic = $item->find('td',5)->plaintext;
					$es_value = $item->find('td',6)->plaintext;
					$price = $item->find('td',7)->plaintext;
					$data = array(
						'domain_name' => $name,
						'traffic' => $traffic,
						'es_value' => $es_value,
						'price' => $price,
					);
					echo $name.PHP_EOL;
					if(DB::table('domain_auction')->where('domain_name',$name)->count() == 0){
						DB::table('domain_auction')->insert($data);
					}
					
				}
			}
		}
		} else {
			echo 'error dom';
		}
	}
	public function processDataAuction(){
		$list = DB::table('domain_auction')->get();
		foreach($list as $item){
			$name = $item->domain_name;
			$name = trim($name);
			var_dump($name);
			$price = str_replace(' *', '', $item->price);
			$price = str_replace('$', '', $price);
			$price = str_replace(',', '', $price);
			if(DB::table('domain_out')->where('domain_name',$name)->count() == 0){
			 DB::table('domain_out')->insert(['domain_name' => $name,'link_out' => -1,'dr' => -1,'RDomain' => -1,'ahrefs_rank' => -1,'created_at' => date('Y-m-d H:i:s'),'check_status' => 7,'isBuy' => 1,'price' => (float)$price]);
			} else {
				DB::table('domain_out')->where('domain_name',$name)->update(['price' => $price,'check_status' => 7]);
			}
		}

		
	}
    
}
