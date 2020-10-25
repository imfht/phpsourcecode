<?php
namespace plugins\weixin\index;

use app\common\controller\IndexBase;
use plugins\weixin\model\User AS UserModel;


class Reg extends IndexBase
{
    public function index(){
        
        if(!in_weixin()){
            $this->error('当前页面只能用手机微信访问！');
        }elseif($this->user){
            $this->error('你已经登录了,不能注册!');
        }
        
        $state = input('state');
        $code = input('code');
        $openid = input('openid');
        
        $check_attention = 0;
        if($openid){
            $check_attention = wx_check_attention($openid);	//检查一下是否已关注过的老用户但系统里还没有注册
        }
        
        if($state==1 || $check_attention){
            
            if($state && !$check_attention){
                if(!$code){
                    $this->error('code 值不存在！');
                }
                $string = file_get_contents('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.config('webdb.weixin_appid').'&secret='.config('webdb.weixin_appsecret').'&code='.$code.'&grant_type=authorization_code');
                
                $array = json_decode($string,true);
                
                if(!$array['access_token']){
                    $this->error('access_token 值不存在！');
                }elseif(!$array['openid']){
                    $this->error('openid 值不存在！');
                }
                
                $string2 = file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$array['access_token'].'&openid='.$array['openid'].'&lang=zh_CN');
                
                $data = json_decode($string2,true);
                
                if($data['nickname']=='' || $data['openid']==''){
                    $this->error('nickname 值不存在！');
                }
                $check_attention = wx_check_attention($data['openid']);
                $openid = $data['openid'];
            }else{
                $data = [];
                $check_attention = 1;
            }
            
            //统计分销系统的UID
            $Mdb = $introducer_1 = $introducer_2 = $introducer_3 = '';
            $introducer_uid = get_cookie('IntroducerUid');
            if( $introducer_uid && $Mdb = UserModel::get_info($introducer_uid) ){
                $introducer_1 = $introducer_uid;
                $Mdb['introducer_1'] && $introducer_2 = $Mdb['introducer_1'];
                $Mdb['introducer_2'] && $introducer_3 = $Mdb['introducer_2'];
            }
            
            $Marray = array(
                    'check_attention'=>$check_attention,
                    'introducer_1'=>$introducer_1,
                    'introducer_2'=>$introducer_2,
                    'introducer_3'=>$introducer_3,
            );
            
            //$__FromPage && $Marray['pageid'] = intval($__FromPage);
            
            $userdb = UserModel::weixin_reg($openid,$data,$Marray);	//注册，数据入库
            
            if(!is_array($userdb)){
                if (strstr($userdb,'已经注册过')) {    //这里最好重新查数据
                    $url = iurl('index/login/index',$array).'?fromurl='.urlencode(get_url('home'));
                    $this->success('当前帐号已注册过了,请重新登录',$url);
                }
                $this->error('注册失败,详情如下：'.$userdb);
            }
            
            //用户登录
            UserModel::login($userdb['username'],'',3600*24,true);
            
            $jumpto = input('jumpto');
            $jumpto && $jumpto=urldecode($jumpto);
            $fromurl = get_cookie('From_url');
            if( $fromurl ){
                set_cookie('From_url','');
                $jumpto = $fromurl;
            }elseif(!$jumpto){
                $jumpto = '/';
            }
            
            $this->success('恭喜你，注册成功',$jumpto);
            
        }else{
            //跳转到微信服务器再返回来，将会得到一个有效的code值。
            $url = urlencode($this->weburl);
            header('location:https://open.weixin.qq.com/connect/oauth2/authorize?appid='.config('webdb.weixin_appid').'&redirect_uri='.$url.'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect');
            exit;
        }
    }
}