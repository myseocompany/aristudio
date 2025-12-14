<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;



// Social CRM
define('FACEBOOK_APPID','242399522626953');
define('FACEBOOK_SECRET','70bf87e26953f3caf1bfad7f4eea8db9');

// Quirky
//define('FACEBOOK_APPID','242399522626953');
//define('FACEBOOK_SECRET','70bf87e26953f3caf1bfad7f4eea8db9');



// MyChatBot
//define('FACEBOOK_APPID','782096565261916');
//define('FACEBOOK_SECRET','e8234ff213214247651e071bb05673e9');

// SocialCRM2
//define('FACEBOOK_APPID','514330838676118');
//define('FACEBOOK_SECRET','8b4a86fcd43cdd8b66bb444ffb745b14');

//8b4a86fcd43cdd8b66bb444ffb745b14



class ContestController extends Controller{

	function getComment($commentID, $token){

		
		$urlComment = 'https://graph.facebook.com/v7.0/'.$commentID.'/?fields=comments.order(reverse_chronological)&access_token='.$token;
		

		$urlComment = 'https://graph.facebook.com/v7.0/'.$commentID.'/comments?limit=1000&order=reverse_chronological&access_token='.$token;
		
		$commentJSON = $this->getJSON($urlComment);

		
		return $commentJSON;
	}

	function postComment($commentID, $token){
		$urlComment = 'https://business.facebook.com/'.$commentID."/?message=hola";
		$txt = "<a href='".$urlComment."' target='_blank'>Responder</a>";
		return "";
	}

	function getCommentNext($urlComment){
		
		$commentJSON = $this->getJSON($urlComment);
		return $commentJSON;
	}



	function getJSON($url){
				$page_ = file_get_contents($url); 
				$page = json_decode($page_, false); 
				
				return $page;		
	}

	function hasReplay($comnent){
		$tiene = property_exists($comnent, 'comments');
		
		return $tiene;
	}

	

	function fbDateFix($str){
		
		date_default_timezone_set('America/Bogota');
		$timestamp = strtotime($str);
		$local_datetime = date('Y-m-d H:i:s',$timestamp);

		return $local_datetime;
	}



	function fbDateTimeStamp($str){
		
		date_default_timezone_set('America/Bogota');
		$timestamp = strtotime($str);

		return $timestamp;
	}

	function getReplayName($comment){
		$txt ="";
		if(property_exists($comment, 'comments')){
			$data = $comment->comments->data;
			
			if(property_exists($data[0], 'from')){
			
				$from = $data[0]->from;
				if(property_exists($from, 'name')){
					
					$txt = $from->name;
				}else{
					
				}
			}else{
				
			}

		}else{
			
		}
		return $txt;

	}
	function getReplayMessage($comment){
		$txt ="<ul>";
		if(property_exists($comment, 'comments')){
			$replays = $comment->comments->data;
			foreach($replays as $rep ){
			
				if(property_exists($rep, 'message')){
					$txt .= "<li>".$rep->message. "</li>";
				}
			}
		}
		return $txt."</ul>";

	}
		
	public function getInterval($finish_date, $created_time){
		$now = strtotime($this->fbDateFix($created_time));
		$created = strtotime($this->fbDateFix($finish_date));
		$interval =  -$now + $created ;

		//dd($interval);
		return $interval;
	}	

	public function isActive($user_id){
		$active_users = array('10159273711325123','10155919625184117','1277167919086173', '10154475097327700','778467272294300', '10153939628812956','10210141695554692','1952286441486683','1269691089716251','10155503998512051');
		return in_array($user_id, $active_users);
		//return true;

	}
	public function commentsIndex(){

		return view("contests.comments");
	}

	/*******************************************************
	*
	*
	*      Esta es la funcion principal
	*
	*
	*******************************************************/
	public function getData($post_id, $token, $finish_date, $fanpage, $unique){
		$next = false;
		$i = 1;
		$array = Array();
		$users = Array();

		$commentJSON = $this->getComment($fanpage."_".$post_id, $token);
			
		do{
			$data = $commentJSON->data;

			foreach ($data as $post) {
				//dd($data);
				if(isset($post->from)){
					if($unique || !in_array($post->from->id, $users, true)){
						array_push($users, $post->from->id);
						// verifico que el post tenga todos los parametros y en ese caso se guarda		
						if(property_exists($post, 'id')&& property_exists($post, 'message')){
							
							$interval = $this->getInterval($finish_date, $post->created_time);
							if($interval>0){
								$array[$i]['created_time'] = $this->fbDateFix($post->created_time);
								$array[$i]['message'] = $post->message;
								$array[$i]['id'] = $post->id;
								$array[$i]['from_name'] = $post->from->name;
								$array[$i]['from_id'] = $post->from->id;
								
							}
							$i++;
						}
					}
				}
			}	
			/*
			if(property_exists($commentJSON, 'paging')||property_exists($commentJSON->comments, 'paging')){
				
				$paging = $commentJSON->paging;

				if(property_exists($paging, 'next')){
					$next = true;
					$commentJSON = $this->getCommentNext($paging->next);
				}else{
					$next = false;
				}
			}else{
				$next = false;
			}
			*/
		}while($next);
		return $array;
	}

	public function commentsShow(Request $request){


		$title = $request->title;

		$post_id = $request->post_id;
		$finish_date = $request->finish_date;

		$fanpage = $request->fanpage;
		//$TOKEN = FACEBOOK_APPID."|".FACEBOOK_SECRET;
		$unique = false;
		if($request->unique){
			$unique = true;
		}


		$token = $request->token;
		$controller = $this; 
		
		$access_token = $token; 
		$app_secret = FACEBOOK_SECRET;

		//$appsecret_proof= hash_hmac('sha256', $access_token, $app_secret);

		//echo $appsecret_proof;
		//exit();
		$data = $this->getData($post_id, $token, $finish_date, $fanpage, $unique);

		
        
		
		//$commentJSON = $controller->getComment($post_id, $token);

		return view("contests.commentsShow", compact('title','data', 'fanpage'));
	}

}