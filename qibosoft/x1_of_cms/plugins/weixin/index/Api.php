<?php
namespace plugins\weixin\index;

use app\common\controller\IndexBase;
use plugins\weixin\model\User AS UserModel;


class Api extends IndexBase
{
    //protected static $instance;    
    protected static $xml_obj;
    protected $wx_apiId;        //公众号自身的ID
    protected $user_appId;      //粉丝用户的唯一ID
    protected $From_content;    //用户回复的内容
    protected $EventType;
    protected $EventKey;
    protected $MsgType;
    protected $MediaId;
    protected $ThumbMediaId;
    protected $PicUrl;
    protected $user_token; 
    
    /**
     * 对接微信公众号的唯一入口
     * @return string|\plugins\weixin\index\unknown
     */
    public function index(){        
        if(input('echostr')){	//首次绑定接口地址时，微信要用到的测试接口是否正常
            echo input('echostr');
            exit;
        }
        //微信POST过来的非PHP标准数据
        $responseObj = simplexml_load_string(file_get_contents("php://input"), 'SimpleXMLElement', LIBXML_NOCDATA);
        //$responseObj = simplexml_load_string($HTTP_RAW_POST_DATA, 'SimpleXMLElement', LIBXML_NOCDATA);

        //用户点击菜单事件
        if($responseObj->Event=='CLICK'){
            $responseObj->Content = $responseObj->EventKey;
        }
        self::$xml_obj = $responseObj;
        
        //用户关注，取消关注，扫码，公众号开启的强制获取位置
        if( !empty($responseObj->Event)&&in_array($responseObj->Event,['subscribe','unsubscribe','SCAN','location_select','LOCATION']) ){
            return self::make($responseObj->Event);
        //发送声音，图片，小视频，地址，扫码
        }elseif( !empty($responseObj->MsgType)&&in_array($responseObj->MsgType,['voice','image','shortvideo','location']) ){
            return self::make($responseObj->MsgType);
        }elseif($responseObj->Content!=''){     //回复关键字或内容
            return self::make('keyword');
        }else{
            //
        }
    }
    
    /**
     * 各个事件的入口，需要重写，实现各自的逻辑内容
     */
    public function execute(){
        if($this->checkSignature()!=true){
            echo $this->give_text("key验证失败!!");
            exit;
        }
    }
    
    /**
     * 常用的微信提交过来的变量
     */
    protected function set_value(){
        $obj = self::$xml_obj;
        $this->wx_apiId = $obj->ToUserName;
        $this->user_appId = $obj->FromUserName;
        $this->From_content = $obj->Content;
        $this->EventType = $obj->Event;
        $this->EventKey = $obj->EventKey;
        $this->MsgType = $obj->MsgType;
        $this->MediaId = $obj->MediaId;
        $this->ThumbMediaId = $obj->ThumbMediaId;
        $this-> PicUrl = $obj->PicUrl;
    }
    
    //模拟用户登录或注册，与用户实际隔离开的。
    protected function check_user(){
        $this->user = UserModel::get_info(['weixin_api'=>$this->user_appId]);
        if (empty($this->user)) {
            $this->user = UserModel::weixin_reg($this->user_appId);
            define('NewUser',true);     //声明是新用户注册，方便后续判断调用
        }
        if ($this->user) {
			$user = $this->user;
			//把关注事件提前到这里
			$result = wx_check_attention($user['uid']);
			if ($result===true&&empty($user['wx_attention'])) {
				 edit_user([
						'uid'=>$user['uid'],
						'wx_attention'=>1
					]);
			}elseif($result===false&&$user['wx_attention']){
				 edit_user([
						'uid'=>$user['uid'],
						'wx_attention'=>0
					]);
			}
            $this->user_token = md5( mymd5($this->wx_apiId . $user['lastip'] . $user['lastvist']) );
            cache($this->user_token,"{$user['uid']}\t{$user['username']}\t".mymd5($user['password'],'EN')."\t",1800);
        }
    }
    
    /**
     * 实例化微信提供的各种接口执行操作入口
     * @param unknown $type
     * @return string|unknown
     */
    public static function make($type)
    {
        $class = "plugins\\weixin\\index\\".'Api_'.strtolower($type);
        if(!class_exists($class)){
            return '类不存在！'; //前台是不可见的，最好写日志
        }
        $obj = new $class();
        $obj -> set_value();
        $obj -> check_user();     //注意 run_model() 里边需要重复执行 因为 他又重新实例化一次类的原因
        return $obj->execute();
        //if (is_null(self::$instance)) {
        //    self::$instance = new static();
        //}
        //return self::$instance;
    }
    
    /**
     * 实例化微信某个接口下所有模块里边的应用，方便扩展
     */
    public function run_model()
    {
        $name = substr(strrchr(get_class($this),'\\'),5);
        $path = opendir(ROOT_PATH.'plugins/weixin/libs/'.$name);
        while($file = readdir($path)){
            if(preg_match('/\.php$/', $file)){
                $class = "plugins\\weixin\\libs\\$name\\".substr($file, 0,-4);
                if (class_exists($class) && method_exists($class,'run')) {
                    $obj = new $class;
                    $obj -> set_value();
                    $obj -> check_user();   //获取登录用户信息
                    $obj -> run();
                }                
            }
        }
    }
    
    /**
     * 权限判断， 是不是微信真实POST过来的数据
     * @return boolean
     */
    protected function checkSignature()
    {
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        
        $token = $this->webdb['weixin_token'];
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
    
    /**
     * 给用户回复纯文本信息，非认证的订阅号也能用
     * @param string $MSG
     * @return string
     */
    protected function give_text($MSG=''){
        $timestamp = time();
        return "<xml>
        <ToUserName><![CDATA[{$this->user_appId}]]></ToUserName>
        <FromUserName><![CDATA[{$this->wx_apiId}]]></FromUserName>
        <CreateTime>$timestamp</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[{$MSG}]]></Content>
        </xml>";
    }
    
    /**
     * 给用户回复图文信息，非认证的订阅号也能用
     * @param array $array
     * @return string
     */
    protected function give_news($array=[]){
        $timestamp = time();
        if (!$array[0]) {
            $array = [$array];
        }
        $num = count($array);
        foreach( $array AS $rs){
            $rs['picurl'] && $rs['picurl'] = tempdir($rs['picurl']);
            $string.="<item><Url><![CDATA[{$rs[url]}]]></Url>
            <PicUrl><![CDATA[{$rs[picurl]}]]></PicUrl>
            <Description><![CDATA[{$rs[about]}]]></Description>
            <Title><![CDATA[{$rs[title]}]]></Title></item>\r\n\r\n";
        }
        return "<xml><ToUserName><![CDATA[{$this->user_appId}]]></ToUserName>
        <FromUserName><![CDATA[{$this->wx_apiId}]]></FromUserName>
        <CreateTime>$timestamp</CreateTime>
        <MsgType><![CDATA[news]]></MsgType>
        <ArticleCount>$num</ArticleCount>
        <Articles>
        $string
        </Articles>
        <FuncFlag>0</FuncFlag>
        </xml>";
    }
}