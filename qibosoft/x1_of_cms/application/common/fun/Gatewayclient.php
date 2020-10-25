<?php
namespace app\common\fun;

use GatewayClient\Gateway;
use WebSocket\Client;

class Gatewayclient{
    
    private static $other_server = true;    //是否使用其它服务端做中转
    private static $server_url = 'http://x1.soyixia.net:8383/ws_server.php';
    private static $client_url = 'wss://x1.soyixia.net:8282'; //客户端请求端口
    
    public function __construct(){
        Gateway::$registerAddress = '127.0.0.1:1234';   //服务端通信IP及端口
        if (empty(self::$other_server)) {
            self::$client_url = 'ws://127.0.0.1:2345';
        }      
    }
    
    public function client_url(){
        return self::$client_url;
    }
    
    /**
     * 模拟浏览器请求websocket服务器
     * @param array $array
     */
    public function ask_ws($array=[]){
        if (is_array($array)) {
            $array['url'] = $array['url']?:request()->domain();
            $array['my_uid'] = $array['my_uid']?:login_user('uid');
            $array = json_encode($array);
        }
        $client = new Client($this->client_url());
        $string = $client->receive();
        //$info = json_decode($string,true);
        //$client_id = $info['client_id'];
        $client->send($array);
        $string = $client->receive();die($string);
        $info = json_decode($string,true);
        return is_array($info)?$info:$string;
    }
    
    /**
     * 给当前圈子群发信息
     * @param number $my_uid 当前用户自己的ID
     * @param number $uid 负数是圈子ID,正数是私人聊天对方的UID
     * @param string $json_msg 消息内容,必须是json格式的数据
     */
    public function send_to_group($my_uid=0,$uid=0,$json_msg=''){
        if (is_array($json_msg)) {
            $json_msg = json_encode($json_msg);
        }
        $group_key = $this->get_group_key($my_uid,$uid);
        if (self::$other_server) {
            $data = [
                'type'=>'sendToGroup',
                'uid'=>$uid,
                'my_uid'=>$my_uid,
                'group_key'=>$group_key,
                'json_msg'=>$json_msg,
                'url'=>request()->domain(),
            ];
            $result = http_curl(self::$server_url,$data);
        }else{
            Gateway::sendToGroup($group_key,$json_msg);
            $all_json_msg = json_encode([
                'type'=>'msglist',
                'uid'=>$uid,
                'from_uid'=>$my_uid,
            ]);
            Gateway::sendToAll($all_json_msg);  //提醒所有用户需要更新列表数据.客户端可以根据$uid做判断是否需要更新
        }
    }
    
    /**
     * 发信息给指定用户
     * @param number $uid 用户UID
     * @param string $json_msg
     */
    public function send_to_user($uid=0,$json_msg=''){
        if (is_array($json_msg)) {
            $json_msg = json_encode($json_msg);
        }
        if (self::$other_server) {
            $data = [
                'type'=>'sendToUser',
                'uid'=>$uid,
                'json_msg'=>$json_msg,
                'url'=>request()->domain(),
            ];
            $result = http_curl(self::$server_url,$data);
        }else{           
        }
    }
    
    /**
     * 把当前用户加入群发组
     * @param number $my_uid 当前用户自己的ID
     * @param number $uid 负数是圈子ID,正数是私人聊天对方的UID
     * @param string $client_id WS生成的客户ID
     */
    public function user_join_group($my_uid=0,$uid=0,$client_id=''){        
        $group_key = $this->get_group_key($my_uid,$uid);        
        if (self::$other_server) {
            $data = [
                'type'=>'joinGroup',
                'group_key'=>$group_key,
                'client_id'=>$client_id,
                'url'=>request()->domain(),
            ];
            $result = http_curl(self::$server_url,$data);
        }else{
            Gateway::joinGroup($client_id,$group_key);
        }
    }
    
    /**
     * 获取用户组的区分关键字
     * @param number $my_uid 当前用户自己的ID
     * @param number $uid 负数是圈子ID,正数是私人聊天对方的UID
     */
    public function get_group_key($my_uid=0,$uid=0){
        static $webkey = null;
        if(empty($webkey)){
            $webkey = substr(md5_file(APP_PATH.'database.php'),5,10);
        }
        $group_key = $webkey.'__';
        if ($uid<0) {   //代表是圈子
            $group_key .= 'qun'.abs($uid);
        }else{
            $group_key .= 'msg'.($my_uid*$uid+$my_uid+$uid);
        }
        return $group_key;
    }
}
