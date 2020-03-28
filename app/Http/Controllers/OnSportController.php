<?php

namespace App\Http\Controllers;
include '/var/www/html/keywords/app/helpers.php';
include 'simple_html_dom.php';
use Illuminate\Http\Request;
use DB;
date_default_timezone_set('Asia/Ho_Chi_Minh'); 
class OnSportController extends Controller
{
    //
    public function categories(){
    	$url = 'http://onsports.vn/api/v1/categories';
    	$res = httprequest($url, 'GET');
    	if($res['status'] == 1){
    		$data = json_decode($res['msg'], true);
    		if($data['code'] == 0){
    			$categories = $data['data'];
    			foreach ($categories as $category) {
    				$check = DB::table('categories')->where('category_id',$category['id'])->count();
    				if($check == 0){
    					if($category['type'] == 'article'){
    						$type = 1;
    					} else {
    						$type = 2;
    					}
    					if($category['status'] == 'PUBLISHED'){
    						$status = 1;
    					} else {
    						$status = 2;
    					}
    					$data = array('name' => $category['name'], 'slug' => $category['slug'], 'description' => $category['description'], 'seo_title' => $category['seo_title'], 'seo_description' => $category['seo_description'], 'type' => $type, 'status' => $status,'created_at' => date('Y-m-d H:i:s',time()),'category_id' => $category['id']);
    					DB::table('categories')->insert($data);
    				}
    				// 	echo 'exits';
    				// }
    			}
    		}
    	}
    }
    public function runPostCategory(){
    	$categories = DB::table('categories')->where('status_check',0)->get();
    	foreach ($categories as $category) {
    		$slug = $category->slug;
    		$page = $category->page;
    		$url = 'http://onsports.vn/api/v1/categories/'.$slug.'?page='.$page.'&pageSize=100';
    		$res = $this->getPostCategory($url);
    		if($res != false){
    			
    			for ($i = $page; $i <= $res['pageCount'] ; $i++) {
    				
    				$url_tmp = 'http://onsports.vn/api/v1/categories/'.$slug.'?page='.$i.'&pageSize=100';
    				$check = $this->getPostCategory($url_tmp);
    				if($check != false){
    					DB::table('categories')->where('category_id', $category->category_id)->update(['page' => $i]);
    
    				} else {
    					echo $i.PHP_EOL;
    					die;
    				}
    			}
    			DB::table('categories')->where('category_id', $category->category_id)->update(['rowCount' => $res['rowCount'],'status_check' => 1]);
    		}
    	}
    }
    public function getPostCategory($url){
    	$res = httprequest($url, 'GET');
    	if($res['status'] == 1){
    		$data = json_decode($res['msg'], true);
    		if($data['code'] == 0){
    			if(isset($data['data']['items'])){
	    			$items = $data['data']['items'];

	    			foreach ($items as $item) {
	    				if($item['status'] == 'PUBLISHED'){
							$status = 1;
						} else {
							$status = 2;
						}
						$duration = 0;
						$thumbnail = '';
						if($item['item_type'] == 'video'){
							$thumbnail = $item['metadata']['thumbnail'];
							$duration = $item['metadata']['duration'];
						}
						$title = '';
						$breaking_news = 0;
						$featured = 0;
						if(isset($item['title'])){
							$title = $item['title'];
						}
						if(isset($item['breaking_news'])){
							$breaking_news = $item['breaking_news'];
						}
						if(isset($item['featured'])){
							$featured = $item['featured'];
						}
						$short_description = '';
						if(isset($item['short_description'])){
							$short_description = $item['short_description'];
						}
						$author_id = null;
						if(isset($item['author'])){
							$author_id = $item['author']['id'];
						}
                        $post_type = '';
                        if(isset($item['post_type'])){
                            $post_type = $item['post_type'];
                        }
	    				$tmp = array(
	    					'type' => $item['type'],
	    					'thumbnail_video' => $thumbnail,
	    					'duration' => $duration,
	    					'item_id' => $item['id'],
	    					'slug' => $item['slug'],
	    					'title' => $title,
	    					'status' => $status,
	    					'breaking_news' => $breaking_news,
	    					'featured' => $featured,
	    					'thumbnail' => $item['thumbnail'],
	    					'short_description' => $short_description,
	    					'author_id' =>$author_id,
	    					'category_id' => $item['category']['id'],
	    					'item_type' => $item['item_type'],
	    					'name' => $item['name'],
                            'post_type' => $post_type
	    				);
	    				$count = DB::table('items')->where('item_id',$item['id'])->where('category_id',$item['category']['id'])->where('type',$item['type'])->count();
	    				if($count == 0){
	    					DB::table('items')->insert($tmp);
	    				}
	    				if(!empty($item['author'])){
	    				$author = DB::table('authors')->where('author_id',$item['author']['id'])->count();
	    				if($author == 0){
	    					$tmp_author = array('author_id' => $item['author']['id'], 'name' => $item['author']['name'], 'slug' => $item['author']['slug']);
	    					DB::table('authors')->insert($tmp_author);
	    				}
	    				}
	    			}
	    			if(isset($data['pagination'])){
	    				return array('page' => $data['pagination']['page'], 'pageCount' => $data['pagination']['pageCount'], 'rowCount' => $data['pagination']['rowCount']);
	    			} else {
	    				return false;
	    			}
    			} else {
    				return false;
    			}
    		} else {
    			return false;
    		}
    	} else {
    		return false;
    	}

    }
    public function detailPost(){
        $list = DB::table('items')->where('status_check',0)->get();
        foreach ($list as $item) {
            $url = 'http://onsports.vn/'.$item->item_type.'/'.$item->slug;
            $res = httprequest($url, 'GET');
            //print_r($res);
            if($res['status'] == 1){
                $html = $res['msg'];
                $tmp = explode('window.__NUXT__=', $html);
                if(isset($tmp[1])){
                    $json = explode(';</script><script src="/_nuxt/manifest', $tmp[1]);
                    $json = $json[0];
                    $data = json_decode($json,true);
                    $update_data = array();
                    $tags = array();
                    $check = true;
                    if($item->item_type == 'post'){
                        if(isset($data['data'][0]['post'])){
                            $info = $data['data'][0]['post'];
                            $update_data['content'] = htmlentities($info['content']);
                            $tags = $info['tags'];
                        } else {
                            $check = false;
                        }
                    } else {
                        if(isset($data['data'][0]['video'])){
                            $info = $data['data'][0]['video'];
                            if(isset($info['fullUrl'])){
                                $update_data['video_url'] = $info['fullUrl'];
                            } else {
                                $update_data['video_url'] = '';
                            }
                            $update_data['description'] = $info['description'];
                            $tags = $info['tags'];
                        } else {
                            $check = false;
                        }
                    }
                    if($check == true){
                        DB::table('items')->where('id',$item->id)->update($update_data);
                        if(count($tags) > 0){
                            foreach ($tags as $tag) {
                                $info = DB::table('tags')->where('tag_id',$tag['id'])->first();
                                if(!$info){
                                    
                                    $slug = $tag['slug'];
                                    if($slug == ''){
                                        $slug = str_slug($tag['name']);
                                    }
                                    $data_insert = array('tag_id' => $tag['id'], 'name' => $tag['name'], 'slug' => $slug);
                                    DB::table('tags')->insert($data_insert); 
                                }
                                $count = DB::table('tag_item')->where('tag_id',$tag['id'])->where('item_id',$item->id)->count();
                                if($count == 0){
                                    DB::table('tag_item')->insert(['tag_id' => $tag['id'], 'item_id' => $item->id]);
                                }
                            }
                        }
                        DB::table('items')->where('id',$item->id)->update(['status_check' => 1]);
                    } else {
                        DB::table('items')->where('id',$item->id)->update(['status_check' => -1]);
                    }
                }
            }
            //die;
        }
    }
}
