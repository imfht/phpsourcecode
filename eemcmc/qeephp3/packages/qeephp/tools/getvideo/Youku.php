<?php namespace qeephp\tools\getvideo;

use qeephp\http\AHttpRequest;

class Youku extends Video
{
	
 	protected function compiler($url)
 	{
 		# get id
 		if (preg_match("/\.html/", $url))
 		{
 			if (preg_match("/id_([^.]+)/", $url,$result))
 			{
 				$id = $result[1];
 			}
 		}
 		else if (preg_match("/\.swf/", $url))
 		{
 			if (preg_match("/sid\/([^\/]+)\/v.swf/", $url,$result))
 			{
 				$id = $result[1];
 			}	
 		}

 		if (empty($id)) return false;

 		$this->id = $id;
 		$this->html_url = "http://v.youku.com/v_show/id_{$id}.html";
 		$this->flash = "http://player.youku.com/player.php/sid/{$id}/v.swf";


 		$api_url = "http://v.youku.com/player/getPlayList/VideoIDS/{$id}";

 		$response = AHttpRequest::get($api_url, array("Accept" => "application/json"));
		
		$response_body = $response->raw_body;			
		if (200 == $response->code && !empty($response_body))
		{
			$arr = json_decode($response_body,TRUE);
			
			if (is_array($arr) && !empty($arr['data']) && !empty($arr['data'][0]))
			{
				$data = $arr['data'][0];

				$this->cover = val($data,'logo');
				$this->title = val($data,'title');
				$this->videoid = val($data,'videoid');
				$this->m3u8 = "http://v.youku.com/player/getM3U8/vid/{$this->videoid}/type/flv/ts/v.m3u8";
			}
		}
		
		return ($this->json(true));
 	}

}