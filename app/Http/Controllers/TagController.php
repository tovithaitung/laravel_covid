<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class TagController extends Controller
{
    public function nr_gasiri($key,$string) {
        $q=0;
        $nr=0;
        $key=strtolower($key);
        $string=strtolower($string);
        while($q==0)
        {
			$pos = strpos($string,$key);
			if ($pos===false) $q=1;
			else 
			{
				$string = substr ($string,$pos+strlen($key));
				$nr++;
			}
        }
        return $nr;
	}
    public function Counts_2($text) {
        while(strpos($text,'  ')!==false) $text = preg_replace("/  /", " ", $text);
        $string = $text;
       // echo $string;
        $text=explode(" ",trim($text));
        $new_text=array();
        $i=0;
        foreach($text as $k=>$t) {
			if(strlen(trim($t))>0) $new_text[$i]=trim($t);
			$i++;
        }
        $text=$new_text;
        //var_dump($text);die();
        $keywords=array();
        
        
        for($i = 0;$i<count($text)-1;$i++){
			if(!isset($text[$i])) continue;
			if(!isset($text[$i+1])) continue;
			$tmp = $text[$i];
			$tmp = $tmp." ".$text[$i+1];
			//echo $tmp.PHP_EOL;
			$nr_finds=$this->nr_gasiri($tmp,$string);
			//var_dump($nr_finds);
			if($nr_finds>=2 && strlen($tmp)>=2*3) $keywords[$tmp]=$nr_finds;
        }
        arsort($keywords);
        //var_dump($keywords);
        return $keywords;
	}
    public function makeCate($title,$short_description,$description,$course_content){
		$description = strip_tags($description);
		$string = $title.' '.$title.' '.$title.' '.$short_description.' '.$short_description.' '.$description.$course_content;
		$string = strtolower($string);
		$i = 0;
		$key_2 = array();
		$words2 = $this->Counts_2($string);
		
		//$stop_new = explode(PHP_EOL,$stop_new);
		foreach($words2 as $key => $c) {
			$i++;
			$key = trim($key);
			
			$stop_new = preg_split("/[\r\n]+/", file_get_contents(public_path("stopwords_1.txt")));
			$stopwords_2= preg_split("/[\r\n]+/", file_get_contents(public_path("stopwords_2.txt")));
			foreach ($stopwords_2 as &$verb) {
				$verb = trim($verb);
				$verb = '/\b' . preg_quote($verb, '/') . '\b/';
			}
			//echo $verb .PHP_EOL;
			$key = preg_replace($stopwords_2, '', $key);
			//echo $key .PHP_EOL;				        
			
			if($key!=''){
				$firs_word = '';
				$word_arr = '';
				$word_arr = explode(' ', $key);
				// var_dump($stop_new);
				$firs_word = $word_arr[0];
				$last_word = $word_arr[1];
				if(!in_array($firs_word, $stop_new) && !in_array($last_word, $stop_new) && $firs_word != $last_word){	
						$key_2[] = $key;
				}
			}
			if(count($key_2) == 2)	break;
		}
		return $key_2;
	}
    public function run(){
        $data= DB::select("SELECT id,title,short_description,course_content,description,category FROM coures_detail WHERE status = 0");
        foreach ($data as $item){
			if (json_decode($item->course_content) != null) {
				$course = json_decode($item->course_content);
				if (!empty($course->{0})) {
					$title_content = $course->{0}->title;
					$item_title = '';
					if (!empty($course->{0}->items) and $course->{0}->items != 'empty') {
						$item_content = $course->{0}->items;
						foreach ($item_content as $value) {
							$item_title .= $value->title .',';
						}
					}
					$item_title = trim($item_title, ',');
					$course_content = $title_content.','.$item_title;
				} else {
					$course_content = '';
				}
			} else {
				$course_content = $item->course_content;
			}
			if (json_decode($item->category) != null) {
				$cate = json_decode($item->category);
				$category = '';
				foreach ($cate as $value) {
					$category .= $value.',';
				}
			} else {
				$category = $item->category . ',';
			}
			$title = $item->title;
			// $short_description = $item->short_description;
			$short_description = '';
			$description = $item->description;
            $res = $this->makeCate($title,$short_description,$description,$course_content);
            //echo $res[0]." ";
			// print_r($item->id);die;
            //if(isset($res[0])) echo strlen($res[0]).PHP_EOL;
			$string_key = '';
			foreach ($res as $keyword) {
				$string_key .= $keyword.',';
			}
			$string_key = trim($string_key, ',');
			$string_key = $category . $string_key;
            if(!isset($string_key)) continue;
            if(strlen($string_key) > 200) continue;
                
                
            DB::table('coures_detail')->where('id',$item->id)->update([
                "tag2" => $string_key,
                "status" => 1
            ]);
        }
        //var_dump($data);
    }
	public function InsertKeyword(){
        $result= DB::select("SELECT id,category,tag,tag2 FROM coures_detail WHERE status = 1");
		foreach ($result as $item) {
			$id_source = $item->id;
			if (json_decode($item->category) != null) {
				$cate = json_decode($item->category);
				foreach ($cate as $value) {
					$data = array();
					$data['tag_keyword'] = trim($value);
					$data['search_volume'] = '0';
					$data['status'] = '0';
					$data['id_source'] = $id_source;
					$data['tag2'] = '0';
					DB::table('tag_keyword')->insertOrIgnore($data);
				}
			} else {
				$category = $item->category;
				$data = array();
				$data['tag_keyword'] = trim($category);
				$data['search_volume'] = '0';
				$data['status'] = '0';
				$data['id_source'] = $id_source;
				$data['tag2'] = '0';
				DB::table('tag_keyword')->insertOrIgnore($data);
			}
			$tag = $item->tag;
			$tag = explode(",", $tag);
			foreach ($tag as $value) {
				$data_tag = array();
				$data_tag['tag_keyword'] = trim($value);
				$data_tag['search_volume'] = '0';
				$data_tag['status'] = '0';
				$data_tag['id_source'] = $id_source;
				$data_tag['tag2'] = '0';
				DB::table('tag_keyword')->insertOrIgnore($data_tag);
			}
			///////////////////
			$tag2 = $item->tag2;
			$tag2 = explode(",", $tag2);
			foreach ($tag2 as $value) {
				$data_tag2 = array();
				$data_tag2['tag_keyword'] = trim($value);
				$data_tag2['search_volume'] = '0';
				$data_tag2['status'] = '0';
				$data_tag2['id_source'] = $id_source;
				$data_tag2['tag2'] = '1';
				DB::table('tag_keyword')->insertOrIgnore($data_tag2);
			}
		}
	}
	public function AutoFill() {
		$list_page = DB::table('tag_keyword')
			->select('*')
			->get();
		foreach ($list_page as $value) {
			$data = array();
			$data['tag_keyword'] = $value->tag_keyword;
			$data['search_volume'] = '0';
			$data['status'] = '0';
			$data['id_source'] = $value->id_source;
			$data['tag2'] = $value->tag2;
			DB::table('tag_keyword_tam')->insert($data);
		}
	}
}
