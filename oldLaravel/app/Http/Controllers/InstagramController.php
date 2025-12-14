<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstagramController extends Controller{


	public $insta_client_id = '3417ab4abbb64ece98142a5e5a6188a3';
	public $insta_client_secret = '84d9728d96ac499ca4db978d7d858d0f';
	public $insta_redirect_uri = 'https://myseo.com.co/contests/instagram/redirect';
 


	public function commentsIndex(){
		$authentication_url = $this->getAuthURL();

		return view("instagram.comments", compact('authentication_url'));
	}

	public function commentsShow(Request $request){


		$title = $request->title;

		$finish_date = $request->finish_date;

		$media_id = $request->media_id;
		
		$unique = false;
		if($request->unique){
			$unique = true;
		}


		$token = $request->token;
		$controller = $this; 
		
		$data = $this->getData($media_id, $token, $finish_date,  $unique);


		//var_dump($data);

		
		$data = $data->data;
		/*
		foreach($data as $post){
			$post->user_photo_url = $this->getUserPhotoUrl($post->from->username, $token);
		}
		*/
		
		return view("instagram.commentsShow", compact('title','data'));
	}


	public function getUserPhotoUrl($uid, $token){
		
		$url = "https://api.instagram.com/v1/users/".$uid."/?access_token=".$token;
		$content = file_get_contents($url);
		//var_dump($content);
		$data = json_decode($content);
		return $data;
	}

	public function getData($media_id, $token, $finish_date,  $unique){
		$urlComment = "https://api.instagram.com/v1/media/".$media_id."/comments?access_token=".$token;
		$urlComment = 'https://api.instagram.com/v1/media/'. $media_id . '/comments?access_token='. $token;
		//var_dump($urlComment);
		$content = file_get_contents($urlComment);
		//var_dump($content);
		$comments = json_decode($content);
		

		//$commentJSON = $this->getJSON($urlComment);



		return $comments;
	}

	public function getUserMediaComments($id, $limit = 0) {
		return $this->_makeCall('media/'.$id.'/comments', true, array('count' => $limit));
	}

	public function getJSON($url){
		$page_ = file_get_contents($url); 

		

		$page = json_decode($page_, false); 
		
		return $page;		
	}

	public function getAuthURL(){
		return "https://api.instagram.com/oauth/authorize?client_id=".$this->insta_client_id."&redirect_uri=".$this->insta_redirect_uri."&response_type=code&hl=en";

	}
	public function redirect(Request $request){
		
		$error = "";
		$access_token = "";


		if(isset($request['code']))
		{
			$code = $request['code'];
			$authentication_url = $this->getAuthURL();

			$apiData = array(
			  'client_id'       => $this->insta_client_id,
			  'client_secret'   => $this->insta_client_secret,
			  'grant_type'      => 'authorization_code',
			  'redirect_uri'    => $this->insta_redirect_uri,
			  'code'            => $code
			);

			$apiHost = 'https://api.instagram.com/oauth/access_token';

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiHost);
			curl_setopt($ch, CURLOPT_POST, count($apiData));
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiData));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$jsonData = curl_exec($ch);
			curl_close($ch);

			
			$user = @json_decode($jsonData); 
			
			$access_token = $user->access_token; //this is your access token

			
		}else{
			$error = 'error_reason = '.$request['error_reason'];
			$error .= '<br>error = '.$request['error'];
			$error .= '<br>error_description = '. $request['error_description'];
		}
		$authentication_url = "https://api.instagram.com/oauth/authorize?client_id=".$this->insta_client_id."&redirect_uri=".$this->insta_redirect_uri."&response_type=code";
		return view("instagram.comments", compact('request', 'authentication_url', 'error', 'access_token'));
	}
	

}

