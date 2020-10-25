<?php
        	
namespace Addons\Third\Model;
use Home\Model\WeixinModel;

        	
/**
 * Third的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {

		
		$myword=$dataArr ['Content'];//用户输入的内容
		$keyword=$keywordArr ['keyword'];//设置的关键词
		$map ['keyword'] =$keywordArr ['keyword'];
		$map ['status'] =0;//状态为启用
		$infos = M ( 'third' )->where ($map )->order('priority desc')->select ();
		
		foreach ($infos as $info)
		{
		
			//(is_array($info) && count($info)>0)
			if($info['isfilter']==0)$myword=str_replace($info['keyword'],'',$myword);//过滤匹配词
			$count=$info['count'];
			if($count==0)$count=rand(1,10);//如果是随机条数则随机1到10
			//echo $info['parameters'];
		if($info['type']==2)//如果是第三方平台则无需转换参数
			
		{
			$postdata = $GLOBALS ['HTTP_RAW_POST_DATA'];
			$postdata = str_replace ( '<Content><![CDATA[' . $dataArr ['Content'] . ']]></Content>', '<Content><![CDATA[' . $myword . ']]></Content>', $postdata );
		
		}
		else
		{
			
			$postdata = array(
					'keyword' =>$myword,// $info['keyword'],
					'count' => $count,
			);
			//附加参数
			$arr = explode("\n",$info['parameters']);
			foreach ($arr as $value)
			{
				$arr2=explode(":",$value);
				$postdata[$arr2[0]]=$arr2[1];
			}
		
			//var_dump($postdata);
		}
		
			$postdata = http_build_query($postdata);
			$info['apisite']=str_replace("[site]", $_SERVER ['HTTP_HOST'],$info['apisite']);
			
			if($info['requesttype']==1)
			{
				$result= $this->do_post_request($info['apisite'], $postdata);//执行POST请求
			}
			else{
				$result= $this->do_get_request($info['apisite'], $postdata);//执行GET请求
			}
		
			if($info['output_format']==2)//合并数据处理
			{
				$articles=$this->format_result($result,get_picture_url($info['defaultpic']),$info['count'],$articles);
				////array_push( 	$rst,$this->format_result($result,get_picture_url($info['defaultpic']),$info['count']))
				//$articles=$this->format_result($result,get_picture_url($info['defaultpic']),$info['count'],$articles);;
			}
		
		
		}
		//结果输出
		
		switch ($info['output_format'])
		{
			case 0:
		$arrresult=simplexml_load_string( $result,'SimpleXMLElement', LIBXML_NOCDATA );
		$MsgType=$arrresult->MsgType;
		if($MsgType=='text')
		{
		
			$res = $this->replyText(''.$arrresult->Content);
			exit();
		}
		elseif ($MsgType=='image')
		{
			$media_id=$arrresult->MediaId;
			$res = $this->replyImage($media_id);
			exit();
		}
		elseif ($MsgType=='voice')
		{
			$media_id=$arrresult->MediaId;
			$res = $this->replyVoice($media_id);
			exit();
		}
		elseif ($MsgType=='video')
		{
			$media_id=$arrresult->MediaId;
			$title=$arrresult->Title;
			$description=$arrresult->Description;
			$res = $this->replyVideo($media_id, $title , $description );
			exit();
		}
		elseif ($MsgType=='news')
		{
			$itmes=   $arrresult->item;
			foreach ($itmes as $item){
				$item=(array)$item;
				if(!empty($item['Title']))//标题不能为空
				{
						
					$articles [] = array (
							'Title' => $item['Title'],
							'Description' =>$item['Description'],
							'PicUrl' =>empty( $item['PicUrl'])?$defalutPic:$item['PicUrl'],
							'Url' => $item['Url']
					);
		
				}
			}
			$res = $this->replyNews($articles);
			exit();
		}
		
				//echo $result;//输出微信XML的数据
			//	$this->format_wechatresult($result);
			$res = $this->replyText('无法获取数据'.$myword.$info['apisite']);
				break;
			case 1://原样数据输出
				echo $result;
				break;
			case 2://整合平台数据
			$res = $this->replyNews ( $articles );
				//var_dump($articles);
				//$result= 	var_dump($articles)//$this->format_result($result,get_picture_url($info['defaultpic']),$info['count']);
				break;
			default:
				//异常处理
		}
		

		
	} 

	// 关注公众号事件
	public function subscribe() {
		return true;
	}
	
	// 取消关注公众号事件
	public function unsubscribe() {
		return true;
	}
	
	// 扫描带参数二维码事件
	public function scan() {
		return true;
	}
	
	// 上报地理位置事件
	public function location() {
		return true;
	}
	
	// 自定义菜单事件
	public function click() {
		return true;
	}	
	
	

	//发送GET请求
	public function do_get_request($url, $data, $optional_headers = null)
	{
		$ch = curl_init();
	
		$str =$url."?".$data;
		curl_setopt($ch, CURLOPT_URL, $str);
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$output = curl_exec($ch);
		return  $output;
	}
	
	
	//发送POST请求
	public function do_post_request($url, $data, $optional_headers = null)
	{
		$params = array('http' => array(
				'method' => 'POST',
				'content' => $data
		));
		if ($optional_headers !== null) {
			$params['http']['header'] = $optional_headers;
		}
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		if (!$fp) {
			throw new Exception("Problem with $url, $php_errormsg");
		}
		$response = @stream_get_contents($fp);
		if ($response === false) {
			throw new Exception("Problem reading data from $url, $php_errormsg");
		}
		return $response;
	}
	
	
	//结果格式化整合平台数据
	public function format_result($result,$defalutPic='',$count=10,$articles)
	{
		$arrresult=simplexml_load_string( $result,'SimpleXMLElement', LIBXML_NOCDATA );
		$itmes=   $arrresult->itmes;
	
	
		$count_item=0;//当前第三方的回复条数统计
		foreach ($itmes as $item){
			$item=(array)$item;
			if(!empty($item['Title']))//标题不能为空
			{
					
					
				//需要加上数据验证
				$articles [] = array (
						'Title' => $item['Title'],
						'Description' =>$item['Description'],
						'PicUrl' =>empty( $item['PicUrl'])?$defalutPic:$item['PicUrl'],
						'Url' => $item['Url']
				);
					
				$count_item++;
				if($count_item>$count)return ;//当前第三方回复条数不超过设置
				if(count($articles)>10)return ;//所有第三方回复总条数超过10条结束，最好设置在replynew方法里面
	
			}
		}
		return $articles;
		// 				var_dump($articles);;
		// 		return ($arrresult['itmes'][0]->Title);
	
	}
	
	
}
        	