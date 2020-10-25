<?php
namespace app\index\controller;

use app\common\controller\BaseHome;
use expand\Curl;
use app\common\model\User as Users;
use think\captcha\Captcha;

class Login extends BaseHome {
    private $cModel;   //当前控制器关联模型

    protected $qq_login_data = [];	//	应用的appid  分配给网站的appkey 成功授权后的回调地址
    protected $user_qqinfo = array();	//用户信息

    public function initialize(){
        parent::initialize();
        $this->cModel = new Users;   //别名：避免与控制名冲突
		$this->qq_login_data = cache('qq_login_data');
        $this->qq_login_data['state'] = md5(time());	//当前的时间戳
		if(empty($this->qq_login_data['qq_appid'])){
			$qqdata['qq_appid'] = confv('qq_appid','system');	//应用的appid
			$qqdata['qq_appkey'] = confv('qq_appkey','system');	//分配给网站的appkey
			$qqdata['callback'] = H_NAME . '/index.php?s=/' . M_NAME . '/' . C_NAME . '/' . 'getAccessToken';	//成功授权后的回调地址
			$this->qq_login_data = cache('qq_login_data',$qqdata);
		}
    }

    public function index() {
		if( request()->isPost() ){
			$data = input('post.');
			if( $data['type'] == 'login' ){	//登录
				$login = $this->cModel->login( $data['username'],md5($data['password']) );
				if( $login ){
					return ajaxReturn( '登录成功','',3 );
				}else{
					return ajaxReturn($this->cModel->error);
				}
			}else{	//注册
				$result = $this->validate($data,'user.register');
				if( true !== $result ){
					return ajaxReturn($result);
				}else{
					$add = $this->cModel->allowField(true)->save($data);
					$uid = $this->cModel->getLastInsID();
				}
				if( $add ){
					$this->cModel->userInfo()->save( ['uid'=>$uid] );
					return ajaxReturn( '注册成功,现在去登录','goDengLu',2 );
				}else{
					return ajaxReturn('注册失败');
				}
			}
		}else{
	        if (!empty($this->uid)){
	            $this->redirect('member/index/index');
	        }else{
	            return $this->fetch();
	        }
		}
    }

    public function getCode() {
        $url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" .$this->qq_login_data['qq_appid']. "&redirect_uri=" . urlencode($this->qq_login_data['callback']). "&state=" .$this->qq_login_data['state'];
        header("location: $url");
        exit;
    }
    public function getAccessToken() {
    	$this->user_qqinfo['code'] = input('get.code');
        $url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id=" .$this->qq_login_data['qq_appid']. "&client_secret=" .$this->qq_login_data['qq_appkey']. "&code=" .$this->user_qqinfo['code']. "&redirect_uri=" .$this->qq_login_data['callback'];
        $Curl = new Curl();
        $result = $Curl->get($url);
        parse_str($result, $datas);
		if( empty($datas['access_token']) ){
			$this->error('没有获取到access_token,请重新登录授权','login/index');
		}
        $this->user_qqinfo['access_token'] = $datas['access_token'];
		$this->getOpenId();
    }
    public function getOpenId() {
        $url = "https://graph.qq.com/oauth2.0/me?access_token=" .$this->user_qqinfo['access_token'];
		$Curl = new Curl();
        $result = $Curl->get($url);
        if ($result) {
            $lpos = strpos($result, "(");
            $rpos = strrpos($result, ")");
            $result = substr($result, $lpos + 1, $rpos - $lpos - 1);
            $result = json_decode($result,true);
			if( !empty($result['openid']) ){
				$this->user_qqinfo['openId'] = $result['openid'];
				$urls = "https://graph.qq.com/user/get_user_info?access_token=" . $this->user_qqinfo['access_token'] ."&oauth_consumer_key=" . $this->qq_login_data['qq_appid'] . "&openid=" . $this->user_qqinfo['openId'];
				$user_infos = $Curl->get($urls);
				$user_infoarr = json_decode($user_infos,true);
				$this->user_qqinfo['nickname'] = $user_infoarr['nickname'];	//昵称
				$this->user_qqinfo['figureurl_qq_1'] = $user_infoarr['figureurl_qq_1'];	//小头像
				$this->user_qqinfo['figureurl_qq_2'] = $user_infoarr['figureurl_qq_2'];	//大头像
				$this->user_qqinfo['gender'] = $user_infoarr['gender'];	//性别
//				$this->user_qqinfo['year'] = $user_infoarr['year'];	//出生年份
//				$this->user_qqinfo['province'] = $user_infoarr['province'];	//省份
//				$this->user_qqinfo['city'] = $user_infoarr['city'];	//市区
				if( !session('user_qqinfo') || session('user_qqinfo') != $this->user_qqinfo ){
					session('user_qqinfo',$this->user_qqinfo);
				}
				$userarr = $this->cModel->where( array('openId'=>$this->user_qqinfo['openId']) )->find();
				if( empty($userarr) ){	//没有数据去注册
					$codes = mt_rand(10,99)  * date("is");	//随机生成一个数字
					session('codes',$codes);
					$this->redirect('login/qq_login',array('codes'=>$codes));	//没有数据去注册
				}else{	//直接登录
					$logins = $this->cModel->login( $userarr['username'],$userarr['password'] );
					if( $logins ){
						$this->redirect('member/index/index');
					}else{
						$this->error($this->cModel->error);
					}
				}
			}else{
				$this->error('没有获取到 openId,请重新登录授权','login/index');
			}
        }else{
        	$this->error('没有获取到 openId,请重新登录授权','login/index');
        }
    }

    public function qq_login() {
    	$user_qqinfo = session('user_qqinfo');
    	if( request()->isPost() ){
    		$data = input('post.');
			$where = ['username' => $data['username'] ];
			$user_data = $this->cModel->where($where)->find();
			if( !empty($user_data) ){
				if( $user_data['password'] == md5($data['password']) ){
					$addopenId = $this->cModel->save(['openId'=>$user_qqinfo['openId'],'zhuce_type'=>'qq'],['id'=>$user_data['id']]);
					if($addopenId){
						$los = $this->cModel->login( $data['username'],md5($data['password']) );
						if( $los ){
							return ajaxReturn( '成功绑定帐号和登录成功',H_NAME.url('member/index/index') );
						}
						return ajaxReturn('登录失败');
					}
				}else{
					return ajaxReturn('用户名已经存在,请重新填写');
				}
			}else{	//新增用户
				if( $user_qqinfo['gender'] == '男' ){
					$data['sex' ] = 1;
				}else{
					$data['sex' ] = 0;
				}
				$data['openId' ]		= $user_qqinfo['openId'];
				$data['zhuce_type' ]	= 'qq';
				$data['name' ]			= $user_qqinfo['nickname'];
				$add = $this->validate($data,'user.login');
				if( true !== $add ){
					return ajaxReturn($add);
				}else{
					$add = $this->cModel->allowField(true)->save($data);
				}
				if( $add ){
					if( !empty($user_qqinfo['figureurl_qq_2']) ){
						$this->cModel->userInfo()->save( ['avatar' => $user_qqinfo['figureurl_qq_2'] ] );
					}else{
						$this->cModel->userInfo()->save( ['avatar' => $user_qqinfo['figureurl_qq_1'] ] );
					}
					$dl = $this->cModel->login( $data['username'],md5($data['password']) );
					if( $dl ){
						return ajaxReturn( '登录成功',url('member/index/index') );
					}else{
						return ajaxReturn($this->cModel->error);
					}
				}else{
					return ajaxReturn('注册失败');
				}
			}
    	}else{
			if( empty($user_qqinfo) ){
				session('user_qqinfo',null);
				$this->error('请重新授权','login/index');
			}
	    	$codes = input('param.codes');
			$codess = session('codes');
			if( empty($codes)  || $codess != $codes){
				session('codes',null);
				$this->error('请重新授权','login/index');
			}
			$this->assign('user_qqinfo',$user_qqinfo);
    		return $this->fetch();
    	}
    }

    public function loginOut() {
        session('userId', null);
        session('user_token', null);
		return ajaxReturn('成功退出','',3);
    }

	public function verify() {	//验证码图片地址
		$config =    [
		    // 验证码字体大小
		    'fontSize'	=>    26,
		    // 验证码位数
		    'length'	=>    4,
		    // 关闭验证码杂点
		    'useNoise'	=>    false,
		    //图片高度
//		    'imageH'	=>	40,
		    //图片宽度
//		    'imageW'	=>	120,

		];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }

}
