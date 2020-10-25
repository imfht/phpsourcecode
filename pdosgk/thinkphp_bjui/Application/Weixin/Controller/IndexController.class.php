<?php
namespace Weixin\Controller;
use Think\Controller;
class IndexController extends Controller {
	public function index(){
		//接口验证
		if(I('get.echostr')){
			$this->valid();
			exit;
		}
		if(C('BOOK_DEBUG')){
			//接受post,或者get
			$this->save();
		}
		
		//接受XML信息
		$this->responseMsg();
		//$file_in = file_get_contents("php://input"); //接收post数据
		
		//$xml = simplexml_load_string($file_in);//转换post数据为simplexml对象
		
		//消息接受
		
		//回复消息
	}
	
	//微信接口验证
	public function valid()
	{
		$echoStr = I('get.echostr');
	
		//valid signature , option
		if($this->checkSignature()){
			echo $echoStr;
			exit;
		}
	}
	
	//验证
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!C('TOKEN')) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = C('TOKEN');
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
	
	//获取access_token
	public function get_access_token(){
		//$header[] = "Content-type: text/xml";        //定义content-type为xml,注意是数组
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.C('APPID').'&secret='.C('APPSECRET');
		$ch = curl_init ();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, 0);
		
		$response = curl_exec($ch);
		if(curl_errno($ch)){
			print curl_error($ch);
		}
		curl_close($ch);
		return $response;
	}
	
	public function responseMsg()
	{	
		//php7移除了HTTP_RAW_POST_DATA
		$postStr = file_get_contents("php://input");
		if (!empty($postStr)){
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$fromUsername = (string)$postObj->FromUserName;
			$toUsername = (string)$postObj->ToUserName;
			$keyword = trim($postObj->Content);
			$createTime = (int)$postObj->CreateTime;
			$msgType = (string)$postObj->MsgType;
			$time = NOW_TIME;
			$textTpl = "<xml>
            <ToUserName><![CDATA[%s]]></ToUserName>
            <FromUserName><![CDATA[%s]]></FromUserName>
            <CreateTime>%s</CreateTime>
            <MsgType><![CDATA[%s]]></MsgType>
            <Content><![CDATA[%s]]></Content>
            <FuncFlag>0<FuncFlag>
            </xml>";
			$imageTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
<Image>
<MediaId><![CDATA[%s]]></MediaId>
</Image>
</xml>";
			//接收文本消息
			if($msgType == 'text'){
				if(!empty( $keyword ))
				{
					//$this->save($toUsername);
					$msgType = "text";
					//保存信息
					$data['openID'] = $fromUsername;
					$data['toUserName'] = $toUsername;
					$data['content'] = $keyword;
					$data['createTime'] = $createTime;
					$data['msgType'] = $msgType;
					
					//保存信息
					M('weixin')->add($data);
				
					//如果问题包含Book关键字，则搜索小说， 形如  Book@裁决
					if(stripos($keyword, 'Book') !== false){
						$arr_keyword = explode('@', $keyword);
						$keyword_book = $arr_keyword[1];
							
						$contentStr = $this->getBookList($fromUsername, $keyword_book);
						$contentStr = $contentStr ? $contentStr : '查询失败';
					}
				
					//如果问题@@@1, 则返回上一次搜索的第一本书
					if(stripos($keyword, '@@@') === 0){
						$arr_keyword = explode('@@@', $keyword);
						$list_order = (int)$arr_keyword[1];
							
						$contentStr = $this->getBookId($fromUsername, $list_order);
						$contentStr = $contentStr ? $contentStr : '收录失败';
					}
					
					//图片回复测试
					if($keyword == 5){
						$MediaId = 'YbuKVmTupUsQAhqURZQRusiTvv2MTR2arQlSTliMjzSCKunnlY2pZp_TLpC3AFsH';
						$resultStr = sprintf($imageTpl, $fromUsername, $toUsername, $time, $MediaId);
						M('post_log')->add(array('data' => $resultStr));
						echo $resultStr;exit;
					}

					if($keyword == 4){
						
						$contentStr = '这事测试';
						$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
						echo $resultStr;exit;
					}
						
				
					//如果关键词不设置特殊功能，则启用自动回复机器人
					$contentStr = $contentStr ? $contentStr : $this->getChat($keyword);
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
					echo $resultStr;
				}
			}
			
			//接收推送消息
			if($msgType == 'event'){
				$Event = (string)$postObj->Event;
				if($Event == 'unsubscribe'){		//取消关注
					$contentStr = '谢谢关注';
				}elseif($Event == 'subscribe'){		//关注
					$contentStr = "谢谢关注\n\n如果您是xiaoshuo.mnsmz.net读者, 需要添加想看的书籍, 请按以下操作\n1. 搜索书名: 输入 Book@书名\n2.添加相应书籍: 输入@@@序号";
				}
				
				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
				echo $resultStr;
			}
			
		}else {
			echo '咋不说哈呢';
			exit;
		}
	}
	
	
	//自动回复
	public function getChat($keyword){
		//默认使用图灵机器 
		return $this->getTuling($keyword);
	}
	
	//图灵
	public function getTuling($keyword){
		$url = 'http://www.tuling123.com/openapi/api?key='.C('TULING_KEY').'&info='.urlencode($keyword);
		$content = $this->_curl($url);
		if($content['code'] == 100000){
			return $content['text'];
		}
		
		return '我现在还小, 等我长大后再回答你的问题';
		
	}
	
	//取出含关键字的小说/书本
	public function getBookList($fromUsername, $keyword){
		//$keyword = I('get.keyword');
		$url = 'http://dushu.baidu.com/ajax/searchresult?word='.urlencode($keyword);
		$content = $this->_curl($url);
		if($content['list']){
			
			$contentStr = '';
			$data['toUserName'] = $fromUsername;
			$num = 1;
			foreach ($content['list'] as $v){
				if($v['book_type'] != 1)
					continue;
				
				if(!$v['book_id'] || $num >= 10)
					continue;
				
				
				$str = $num . '、'.strip_tags ($v['book_name'])."\n--作者：".$v['author']."\n--简介:".substr($v['summary'], 0, 40);
				$contentStr .= $contentStr ? "\n\n".$str : $str;
				$data_weixin_book[$num] = $v['book_id']; 	//保存书号
				$num ++;
			}

			//保存到当前用户的查询记录， 方便下次直接输入数字， 取出Bookid
			//删除之前的记录
			M('weixin_book')->where(array('toUserName' => $fromUsername))->delete();
			$data['data'] = serialize($data_weixin_book);
			M('weixin_book')->add($data);
			return $contentStr;
		}else{
			return false;
		}
		
		//http://dushu.baidu.com/ajax/searchresult?word=%E5%B0%8F%E5%85%B5
	}
	
	//保存书本
	public function getBookId($fromUsername, $list_order){
		//用户是否查询过
		$weixin_book_data = M('weixin_book')->where(array('toUserName' => $fromUsername))->getField('data');
		if(!$weixin_book_data){
			return '输入错误哦，请重新输入或查询';
		}
		
		$weixin_book_data = unserialize($weixin_book_data);
		if(!$weixin_book_data[$list_order]){
			return '输入错误哦，请重新输入或查询';
		}
		$gid = $weixin_book_data[$list_order];
		//判断是否采集过, 没有的话, 保存文章信息
		$map_book['gid'] = $gid;
		$exist_book = D('Book')->where($map_book)->find();
		
		if(!$exist_book){
			$url = 'http://m.baidu.com/tc?srd=1&appui=alaxs&ajax=1&gid='.$gid;
			$content = $this->_curl($url);
			if(!$content || $content['status'] != 1)
				return false;
			
			$data_book['title'] = $content['data']['title'];
			$data_book['summary'] = $content['data']['summary'];
			$data_book['thumb'] = $content['data']['originalCoverImage'];
			$data_book['author'] = $content['data']['author'];
			$data_book['gid'] = $content['data']['gid'];
			$data_book['category'] = $content['data']['category'];
			$data_book['url'] = $content['data']['url'];
			$data_book['create_time'] = NOW_TIME;
		
			$bookid = D('Book')->add($data_book);
		}else{
			return $exist_book['title'].'已经存在， 不必重复添加';
		}
		
		if(!$bookid)
			return false;
		//判断最新章节
		//取出所有章节
		$group = $content['data']['group'];
		$map['bookid'] = $data['bookid'] = $bookid;
		
		//待优化: 查看已采集的章节数量,
		foreach ($group as $chapter){
			//判断是否已经存在
			//$map['chapterid'] = $chapter['index'];
			//$exist_chapter = D('BookChapter')->where($map)->field('id, status')->find();
			//if(!$exist_chapter){
				//不判断，直接保存
				$data['chapterid'] = $chapter['index'];
				$data['cid'] = $chapter['cid'];
				$data['index'] = $chapter['index'];
				$data['rank'] = $chapter['rank'];
				$data['title'] = $chapter['text'];
				$data['url'] = $chapter['href'];
					
				D('BookChapter')->add($data);
			//}
		}
		//更新最后一章信息到Book表
		$last_chapter['last_chapter_title'] = $data['title'];
		$last_chapter['last_chapter_index'] = $data['index'];
		$last_chapter['last_chapter_update_time'] = NOW_TIME;
		D('Book')->where('id='.$bookid)->save($last_chapter);
		return $content['data']['title'].'添加成功 最新章节：'.$last_chapter['last_chapter_title'] ;
	}
	
	//保存post信息
	public function save($data = null){
		if(!$data){
			if(IS_POST){
				$data = file_get_contents("php://input"); //接收post数据
			}else{
				$data = $_GET;
			}
		}
		//$file_in = file_get_contents("php://input"); //接收post数据
		$info['data'] = serialize($data);
		$info['create_time'] = NOW_TIME;
		$info['ip'] = get_client_ip(0, true);
		$result = M('post_log')->add($info);
			
	}
	
	public function _curl($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$txt = curl_exec($ch);
		if (curl_errno($ch)) {
			return false;
		}
		curl_close($ch);
		return json_decode($txt, true);
	}
	
	
}