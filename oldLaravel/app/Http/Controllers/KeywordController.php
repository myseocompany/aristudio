<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Keyword;

class KeywordController extends Controller{
	
	public $APIkey="AIzaSyDfUk7MJxqbDVAt7Gzij5pXfuUMn9qDkFs";
	
	public function getNumber($page){
		$pos = strrpos($page, "resultStats");
		$pos2 = strrpos($page, "topstuff",$pos);
		$firstString=substr($page,$pos,$pos2-$pos);
		$numRes=filter_var($firstString, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		return	$firstString;
		}
	
	public function queryUpdate(Request $request){
	    
		$model= Keyword::where('searches',0)->first();
		$keyword = str_replace(" ", "+", $model->keyword);
		$kId = $model->id;
		$APIkey=$this->APIkey;
		return view("keywords.queryUpdate",compact('keyword','kId','APIkey'));
		}
		
	public function updateValue(Request $request){
		$model= Keyword::find($request->keywordId);
		$model->searches=$request->total;
		$model->save();
		return $request->keywordId;	
		}
		
	public function keyFinder(Request $request){
		$keyAnalized=[];
		$model= Keyword::where('searches',0)->get();		
		$opts = [
		    "http" => [
		        "method" => "GET",
		        "header" => "Accept-language: es\r\n" .
		            "Cookie: foo=bar\r\n".
		            "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
		    ]
		];
		
		//$model10=$model->slice(0, 10);
		//dd($model_10);	
		$context = stream_context_create($opts);
		foreach ($model as $keywordDb){
			$keyword = str_replace(" ", "+", $keywordDb->keyword);
			$urlSearch =  'https://www.googleapis.com/customsearch/v1?q='.$keyword.'&cx=006667151083533909385%3Auug8opexqgc&key='.$this->APIkey;
			//$urlSearch =  utf8_decode ('https://www.google.com.co/search?q='.$keyword);
			//$urlSearch =  utf8_decode ( 'https://www.google.com.co/search?client=ubuntu&channel=fs&q='.$keyword.'&ie=utf-8&oe=utf-8');
			$page_ = file_get_contents($urlSearch, false, $context);
			$jsonPage=json_decode($page_);
			$keyCount=$jsonPage->searchInformation->totalResults;
			$keyAnalized[]=[$keywordDb->keyword,$keyCount]; 
			$keywordDb->searches_url = $urlSearch;
			$keywordDb->searches= $keyCount;
			$keywordDb->save();
			}
		dd($keyAnalized);
		}

	public function mapTest(){
		return view("instagram.mapTest");
		}

}
