<?php
namespace plugins\login\index;
use app\common\controller\IndexBase;
use app\common\model\User AS UserModel;
use plugins\login\model\Scan AS ScanModel;

class Scan extends IndexBase
{
    protected static $sid=null;
    
    protected function _initialize(){
        parent::_initialize();
        self::$sid = get_cookie('user_sid');
        if(empty(self::$sid )){
            self::$sid = rands(10);
            set_cookie('user_sid', self::$sid );
        }
    }
    
    /**
     * 生成登录二维码给手机扫描
     * @param string $type 默认是微信,也可以是APP
     */
    public function qrcode($type='wx'){
        $url = $this->request->domain() . purl('login/scan/in_app') . '?type=' . $type . '&code=' . mymd5(time() . "\t" . self::$sid ."\t" .get_ip());        
        $url = iurl('index/qrcode/index') . '?url=' . urlencode($url);
        header('location:'.$url);
        exit;
    }
    
    /**
     * PC端刷新数据库手机端是否已登录成功
     * @param string $type
     * @return string
     */
    public function cklogin($type=''){
        if($type=='success'){            
            $this->success('登录成功',iurl('index/index/index'));
        }
        if ($this->user) {
            die('ok');
        }
        $info = getArray( ScanModel::where('sid',self::$sid )->find() );
        if($info['uid']){
            if($info['ip']!=$this->request->ip()){
                //return 'error IP';
            }
            if(time() - $info['posttime']>300){
                return 'overtime';
            }            
            UserModel::login($info['uid'], '', 3600*24*30,true,'uid');
            ScanModel::where('sid',self::$sid)->delete();
            die('ok');
        }
        return '数据为空';
    }
    
    /**
     * 手机端访问执行登录操作
     * @param string $type 主要是指定如果没有登录的时候,以什么方式登录
     * @param string $code
     */
    public function in_app($type='wx',$code=''){
        if(empty($this->user)){
            if($type=='wx'){
                $url = iurl('weixin/login/index') . '?fromurl=' . urlencode($this->weburl);
                echo "<META HTTP-EQUIV=REFRESH CONTENT='0;URL=".$url."'>";
                exit;
            }
        }
        if($code){
            list($time,$usrID,$ip) = explode("\t",mymd5($code,'DE'));
            if(!$usrID){
                $this->error("参数有误！");
            }elseif( (time()-$time)>600 ){
                $this->error("超时了，10分钟内有效，请再次刷新一下电脑页面再扫描！");
            }
            $usrID = filtrate($usrID);
            if($ip!=get_ip()){
                //$this->error('PC端的IP与手机端的IP不一致,请把手机连接到WIFI,再重新扫码!');
            }
            ScanModel::where('sid',$usrID)->delete();
            $data = [
                    'uid'=>$this->user['uid'],
                    'sid'=>$usrID,
                    'ip'=>get_ip(),
                    'posttime'=>time(),
            ];
            ScanModel::create($data);
            $this->success('电脑端登录成功，你可以关闭本页面' , get_url('member') , 20);            
        }
    }
}