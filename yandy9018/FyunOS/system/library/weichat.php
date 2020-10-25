<?php
final class Wechat
{
    public function __construct($registry) {
        $this->config = $registry->get('config');
        $this->session = $registry->get('session');
        $this->db = $registry->get('db');

       }

    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET value = '1' WHERE `key`='config_wechat_status'");
            exit;
        }
    }

    public function responseMsg()
    {
       //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)){
                
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $RX_TYPE = trim($postObj->MsgType);

                switch($RX_TYPE)
                {
                    case "text":
                        $resultStr = $this->handleText($postObj);
                        break;
                    case "event":
                        $resultStr = $this->handleEvent($postObj);
                        break;
                    default:
                        $resultStr = "Unknow msg type: ".$RX_TYPE;
                        break;
                }
                echo $resultStr;
        }else {
            echo "";
            exit;
        }
    }
     public function handleText($postObj)
    {
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $keyword = trim($postObj->Content);
        $time = time();
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>0</FuncFlag>
                    </xml>";             
        if(!empty( $keyword ))
        {
            $msgType = "text";
            $contentStr = $this->config->get('config_wechat_reply')."<a href='".$this->config->get('config_url')."index.php?route=common/home&openId=".$postObj->FromUserName."'>【开始点餐】</a>";
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
            echo $resultStr;
        }else{
            echo "Input something...";
        }
    }

    public function handleEvent($object)
    {
        $contentStr = "";
        if($this->config->get('config_bind_status')==1){
              switch ($object->Event)
              {
                  case "subscribe":
                      $this->data['json_acctoken'] = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->config->get('config_wechat_appid')."&secret=".$this->config->get('config_wechat_appsecret'));
                      $this->data['acctoken'] = json_decode($this->data['json_acctoken'],true);
                      $this->data['json_uinfo'] = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->data['acctoken']['access_token']."&openid=".$object->FromUserName."&lang=zh_CN");
                      $this->data['uinfo'] = json_decode($this->data['json_uinfo'],true);
                      $save1 =  $this->addWeixinCustomer($this->data['uinfo']);
                      $contentStr = $this->config->get('config_wechat_attention');
                      break;
                  case "unsubscribe":
                   $this->DeleteWeixinOpenid($object->FromUserName);
                      break;
                  default :
                      $contentStr = "Unknow Event: ".$object->Event;
                      break;
              }
        }else{
            switch ($object->Event)
              {
                  case "subscribe":
                      $data['nickname'] = "微信用户";
                      $data['openid'] = $object->FromUserName;
                      $this->addWeixinCustomer($data);
                         $contentStr = $this->config->get('config_wechat_reply')."<a href='".$this->config->get('config_url')."index.php?route=common/home&openId=".$object->FromUserName."'>【开始点餐】</a>";
                      break;
                  case "unsubscribe":
                      $this->DeleteWeixinOpenid($object->FromUserName);
                      break;
                  default :
                      $contentStr = "Unknow Event: ".$object->Event;
                      break;
              }
            
        }
        $resultStr = $this->responseText($object, $contentStr);
        return $resultStr;
    }
    
    public function addWeixinCustomer($data) {
        $active_code = md5(uniqid());
        $status=1;
        if($this->config->get('config_active')=='1'){
            $status=0;
        }
        $this->db->query("INSERT INTO " . DB_PREFIX . "customer SET active_code = '" . $active_code . "', store_id = '" . (int)$this->config->get('config_store_id') . "',  open_id = '" . $data['openid'] . "',firstname = '" . $data['nickname'] . "', newsletter = '" . (isset($data['newsletter']) ? (int)$data['newsletter'] : 0) . "', customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "', status = '" . (int)$status . "', date_added = NOW()");
        return 1;
    }
    
    public function DeleteWeixinOpenid($open_id) {
        $this->db->query("DELETE from " . DB_PREFIX . "customer WHERE open_id = '" .$open_id. "'");
    }

    public function responseText($object, $content, $flag=0)
    {
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[text]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    <FuncFlag>%d</FuncFlag>
                    </xml>";
        $resultStr = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content, $flag);
        return $resultStr;
    }  
    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];    
                
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}
?>