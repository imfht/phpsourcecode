<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/5/8
 * Time: 17:16
 */
namespace app\chat\controller;
use think\App;
use think\Controller;
use think\facade\Cache;
use app\admin\model\Admin;
use Firebase\Token\TokenGenerator;

class Base extends Controller{

    protected $isSink = false;
    protected $sinkMethods = [];
    protected $sign='';
    protected static $token;
    protected static $user;

   public function __construct(App $app = null){
       ini_set('memory_limit', '-1');
       parent::__construct($app);
       header("Content-type:application/json");
       // 指定允许其他域名访问
       header('Access-Control-Allow-Origin:*');
       // 响应类型
       header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE');
       // 响应头设置
       header('Access-Control-Allow-Headers:x-requested-with,content-type');
       if(!$this->isSink){
           if(!in_array(request()->action(),$this->sinkMethods)){
//               $this->_sign();
//               $this->_auth();
           }
       }
   }

    /**
     * 重置密码
     * @param $password
     * @param bool $flag
     * @return bool|string
     */
   protected function _password($password,$flag=false){
       return $flag?substr($password,10,15):substr(md5($password),10,15);
   }

    /**
     * 生成token
     * @param int $uid
     * @return array|mixed
     * @throws \Firebase\Token\TokenException
     */
   public function _token($uid=0){
       if(!$uid) {
           $this->__e('请先登录');
       }
       $token = Cache::store('redis')
           ->get(self::$token['access_token']."_token");
       $time = time();
       $expire = $time + 60 * 60 * 1.5;
       if(!$token){
           $generator = new TokenGenerator('<YOUR_FIREBASE_SECRET>');
           $_token = $generator
               ->setData(array('uid' => "{$uid}"))
               ->create();
           $token = [
               'access_token'=>$_token,
               'expires_in'=>$expire,
               'uid'=>$uid
           ];
           self::$token = $token;
           Cache::store('redis')->set("{$_token}_token",$token);
       }else if($token && $token['expires_in'] < $time){
           $this->_removeToken();
           exit(json_encode([
               'status'=>0,
               'code'=>Notify::$Notify_Token失效
           ]));
       }
       return $token;
   }

    /**
     * 清除token
     */
   protected function _removeToken(){
       $token = self::$token;
       Cache::store('redis')->rm("{$token['access_token']}_token");
   }

    /**
     * 签名验证
     */
   protected function _sign(){
       $params = request()->param();
       $sign = $params['sign'];
       unset($params['sign']);
       $query = '';
       foreach ($params as $k => $v){
           if(is_array($v)){
               $query1 = '';
               foreach ($v as $v1){
                   $query1 .= ','.$v1;
               }
               $query .= '&'.$k.'=['.substr($query1,1).']';
           }else{
               $query .= "&{$k}={$v}";
           }
       }
       if($params['domain']!=config('api.domain')){
           exit(json_encode([
               'status'=>0,
               'code'=>Notify::$Notify_非法请求原,
               'msg'=> '非法的请求'
           ]));
       }
       if(time()-ceil($params['timestamp']/1000)>1000){
           exit(json_encode([
               'status'=>0,
               'code'=>Notify::$Notify_请求超时,
               'msg'=>'请求超时'
           ]));
       }
       $query =substr($query,1);
       $base64 = str_replace('==','',base64_encode($query));
       $_sign = substr(md5($base64),15);
       if($sign!=$_sign){
           exit(json_encode([
               'status'=>0,
               'code'=>Notify::$Notify_签名错误,
               'msg'=> '签名错误'
           ]));
       }
   }
    /**
     * 检测权限
     */
   protected function _auth(){
       if(!isset(request()->header()['authorization'])){
           exit(json_encode([
               'status'=>0,
               'code'=>Notify::$Notify_Token为空
           ]));
       }
       $token = request()->header()['authorization'];
       if(empty($token)){
           exit(json_encode([
               'status'=>0,
               'code'=>Notify::$Notify_Token为空
           ]));
       }
       $_token = Cache::store('redis')->get("{$token}_token");
       if($_token['expires_in']<time()){
           $this->_removeToken();
           exit(json_encode([
               'status'=>0,
               'code'=>Notify::$Notify_Token失效
           ]));
       }
       self::$token = $_token;
   }

    /**
     * 获取用户
     * @return mixed
     */
   public function getUser(){
       $id = self::$token['uid'];
       $admin = new Admin;
       self::$user = $admin::get($id);
       unset(self::$user['password']);
       unset(self::$user['hash']);
       return self::$user;
   }

    /**
     * 成功返回
     * @param string $msg
     * @param array $data
     * @param int $code
     * @param array $header
     * @param string $type
     * @return \think\Response
     */
    public function __s($msg='',$data=null,$code=200,$header=[],$type=''){
        $data = is_object($data) ? $data->toArray() : $data;
        $msg = $msg ? $msg : 'success';
        $data = (is_array($data) && $data) ? ['code'=>0,'msg'=>$msg,'data'=>$data] : ['code'=>0,'msg'=>$msg];
        return json($data)->send();
    }

    /***
     * 失败返回
     * @param string $msg
     * @param array $data
     * @param int $code
     * @param array $header
     * @param $type
     * @return \think\Response
     */
   public function __e($msg='',$data=null,$code=200,$header=[],$type=''){
       $data = ($data && is_object($data)) ? $data->toArray() : $data;
       $msg = $msg ? $msg : 'error';
       $data = (is_array($data) && $data) ? array_merge($data,['code'=>1]) : ['code'=>1,'msg'=>$msg];
       return json($data)->send();
   }
}