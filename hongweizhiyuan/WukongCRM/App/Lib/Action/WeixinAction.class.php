<?php 
// weixin 
class WeixinAction extends Action {

	private $_token;
	private $_post;
	private $bd_url;
	
	private function checkSignature() {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
		$echostr = $_GET["echostr"];		
        		
		$token = $this->_token;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature && $echostr != ""){
			echo $echostr;
			die();
		}elseif( $tmpStr == $signature){
			return true;
		}else{
			return false;
		}
	}
	
	public function _initialize(){
		$weixin = M('Config')->where('name = "weixin"')->getField('value');
		$weixin_config = unserialize($weixin);
		$this->_token = $weixin_config['WEIXIN_TOKEN'];
		$this->bd_url = $weixin_config['WEIXIN_BD_URL'];
		if(!$this->checkSignature()){
        	echo 'illegal origin';
        	exit;
        }
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
		$this->_post = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
		
	}
	
	private function responseMsg($data = array("content" => "Welcome to 5KCRM!", 'type'=> "text"))
    {
		$fromUsername = $this->_post->FromUserName;
        $toUsername = $this->_post->ToUserName;
		$time = time();
		if ($data['type'] == "text") {
			$tpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Content><![CDATA[%s]]></Content>
						<FuncFlag>0</FuncFlag>
						</xml>";             

			echo sprintf($tpl, $fromUsername, $toUsername, $time, $data['type'], $data['content']);
		} elseif ($data['type'] == "music") {
			$tpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<Music>
						<Title><![CDATA[%s]]></Title>
						<Description><![CDATA[%s]]></Description>
						<MusicUrl><![CDATA[%s]]></MusicUrl>
						<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
						</Music>
						<FuncFlag>0</FuncFlag>
						</xml>";  
			echo sprintf($tpl, $fromUsername, $toUsername, $time, $data['type'], $data['title'], $data['description'], $data['musicurl'], $data['hqmusicurl']);
		} elseif ($data['type'] == "news") {
			$tpl = "<xml>
						<ToUserName><![CDATA[%s]]></ToUserName>
						<FromUserName><![CDATA[%s]]></FromUserName>
						<CreateTime>%s</CreateTime>
						<MsgType><![CDATA[%s]]></MsgType>
						<ArticleCount>%d</ArticleCount>
						<Articles>";
			foreach($data['articles'] AS $v) {
				$tpl .=	"<item>
						 <Title><![CDATA[".$v['title']."]]></Title>
						 <Description><![CDATA[".$v['description']."]]></Description>
						 <PicUrl><![CDATA[".$v['picurl']."]]></PicUrl>
						 <Url><![CDATA[".$v['url']."]]></Url>
						 </item>";	
			}			
						
			$tpl .=		"<FuncFlag>0</FuncFlag>
						</xml>";  
			echo sprintf($tpl, $fromUsername, $toUsername, $time, $data['type'], $data['articlecount']);
		}
    }
	
	public function index(){
		if ($this->_post->MsgType == "text") {
			$content = $this->_post->Content;
			$user = M('user')->where(array('weixinid'=>(string)$this->_post->FromUserName))->find();
			if(!is_array($user) || ($content=="bd" || $content=="绑定")){
				$data = array('type'=>'news','articlecount'=>'2','articles'=>array(
					array('title'=>'绑定CRM账号后，您才可以获得更多服务！',
						'url'=>U('user/weixinbinding', 'id='.$this->_post->FromUserName, '', '', true),
					),
					array('title'=>'点此绑定',
						'url'=>U('user/weixinbinding', 'id='.$this->_post->FromUserName, '', '', true),
					)
				));
			}else{
				if($content == "xx" || $content == "信息") {
					$m_message = M('message');
					$list = $m_message->where(array('to_role_id' => $user['role_id'],'read_time'=>0))->select();
					if(count($list) == 0){
						$message[] = array('title'=>'你没有未读消息');
					}else{
						$message[] = array('title'=>'你有'.count($list).'条未读消息');
						foreach($list as $r){
							$user = M('user')->where(array('role_id'=>$r['from_role_id']))->find();
							$message[] = array('title'=>++$i.'.'.$r['content'].'----'.$user['name'].'于'.date('Y-m-d H:i:s',$r['send_time']).'发送');
						}
					}
					$data = array('type'=>'news','articlecount'=>count($list)+1,'articles'=>$message);
				}elseif($content == "xrz" || $content == "写日志"){
					// $data = explode ("#", $content);
					// if(!empty($data)){
						// $m_log = M('Log');
						// $log['subject'] = $data[1];
						// $log['content'] = $data[2];
						// $log['create_date'] = time();
						// $log['role_id'] = $user['role_id'];
						// $log['update_date'] = time();
						// if($m_log->add($log)){
							// $data = array('type'=>'text','content'=>'写日志成功！');
						// }else{
							// $data = array('type'=>'text','content'=>'写日志失败！');
						// }
					// }else{
						// $data = array('type'=>'text','content'=>'写日志请按照以下格式输入："#标题#内容"');
					// }
					$data = array('type'=>'news','articlecount'=>'1','articles'=>array(
						array('title'=>'写日志',
							'url'=>U('log/wxadd', 'id='.$user['role_id'], '', '', true),
						)
					));
				} else {
					$data = array('type'=>'text','content'=>'欢迎关注5KCRM！回复[bd]或[绑定]绑定微信到悟空crm账号,回复[xx]或[消息]查看最新消息(需要您绑定账号)'.$command);
				}
			}
		
		} elseif ($this->_post->MsgType == "image") {
			$data = array('type'=>'text','content'=>'欢迎关注5KCRM');
		} elseif ($this->_post->MsgType == "location") {
			$data = array('type'=>'text','content'=>'欢迎关注5KCRM');
		} elseif ($this->_post->MsgType == "link") {
			$data = array('type'=>'text','content'=>'欢迎关注5KCRM');
		} elseif ($this->_post->MsgType == "event" && $this->_post->Event == "subscribe") {
			$data = array('type'=>'news','articlecount'=>'3','articles'=>array(
				array('title'=>'欢迎关注悟空CRM！',
					'url'=>U('user/weixinbinding', 'id='.$this->_post->FromUserName, '','', true),
				),
				array('title'=>'绑定CRM账号后，您可以随时查询最新消息',
					'url'=>U('user/weixinbinding', 'id='.$this->_post->FromUserName, '', '', true),
				),
				array('title'=>'点此绑定',
					'url'=>U('user/weixinbinding', 'id='.$this->_post->FromUserName, '', '', true),
				)
			));
		} elseif ($this->_post->MsgType == "event" && $this->_post->Event == "unsubscribe") {
			$data = array('type'=>'news','articlecount'=>'1','articles'=>array(
				array('title'=>'欢迎关注5KCRM',
				'description'=>'',
				'picurl'=>'',
				'url'=>''),
			));
		} 
		
		$this->responseMsg($data);
		
    }
}