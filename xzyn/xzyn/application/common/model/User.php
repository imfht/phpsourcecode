<?php
namespace app\common\model;
// 用户模型
use think\Model;
use app\common\model\TokenUser;

class User extends Model
{
	public $error = null; //如果存在错误的时候返回一下错误

    protected $readonly = ['username'];	// 只读字段

    protected $insert  = ['logins', 'reg_ip', 'last_time', 'last_ip','status'=>1,'is_share'];	// 新增自动完成列表
    protected $update = [];	//更新自动完成列表
	// 设置json类型字段
//	protected $json = ['openId'];

    public function userInfo()
    {
        return $this->hasOne('userInfo', 'uid', 'id');
    }
    public function Focus()
    {
        return $this->hasOne('Focus', 'fuid', 'id');
    }

    public function userGroup()
    {
        return $this->hasMany('authGroupAccess', 'uid', 'id');
    }

    protected function setIsShareAttr($value){	// is_share 字段 [修改器]
    	if( empty($value) ){
    		$arr = ["email_is_share"=>"1","moblie_is_share"=>"1","sex_is_share"=>"1","qq_is_share"=>"1","wx_is_share"=>"1","birthday_is_share"=>"1"];
			$json = json_encode($arr);
    	}else{
    		$json = $value;
    	}
    	return $json;
    }

    protected function setPasswordAttr($value){	// password 字段 [修改器]
    	return md5($value);
    }
    protected function setLoginsAttr()	// logins 字段 [修改器]
    {
        return '0';
    }
    protected function setRegIpAttr()	// reg_ip 字段 [修改器]
    {
        return request()->ip();
    }
    protected function setLastTimeAttr()	// last_time 字段 [修改器]
    {
        return time();
    }
    protected function setLastIpAttr()	// last_ip 字段 [修改器]
    {
        return request()->ip();
    }

    public function getStatusTurnAttr($value, $data)
    {
        $turnArr = [0=>'停用', 1=>'在用'];
        return $turnArr[$data['status']];
    }
    public function getLastTimeTurnAttr($value, $data)
    {
        return date('Y-m-d H:i:s', $data['last_time']);
    }
    public function getIsShareAttr($value, $data) {	// is_share 字段[获取器]
    	$is_share = json_decode($data['is_share'],true);
        return $is_share;
    }

    public function getFocusNumAttr($value, $data) {	// focus_num 关注的用户数量 [获取器]
        $Focus = new \app\common\model\Focus;
		$num = $Focus->where(['fuid'=>$data['id']])->count();
		return $num;
    }

    public function getFocusbNumAttr($value, $data) {	// focusb_num 被关注的用户数量 [获取器]
        $Focus = new \app\common\model\Focus;
		$num = $Focus->where(['uid'=>$data['id']])->count();
		return $num;
    }

	//用户登录
    public function login($username, $password) {
		if( empty($username) || empty($password) ){
			$this->error = '请填写用户名或密码';
			return false;
		}
		$user = User::get(['username'=>$username]);
//		$user->userInfo;
		if( !empty($user) ){
            if($user['status'] != '1'){
                $this->error = '该账户已经禁用';
				return false;
            }elseif( $user['password'] != $password ){
                $this->error = '密码错误';
				return false;
            }else{
                // 更新登陆信息
                $ip = request()->ip();
                $updata = [
                    'logins' => $user['logins']+1,
                    'last_time' => time(),
                    'last_ip' => $ip,
                ];
                $where = ['id' => $user['id']];
                User::where($where)->update($updata);
                //设置session,cookie
                session('userId', $user['id']);
                $config = new \app\common\model\Config();
				$tkModel = new TokenUser();
                $login_time = $config->where(['type'=>'system', 'k'=>'login_time'])->value('v');
                $user_token = $tkModel->createToken($user['id'], 1, $login_time);	//生成 user_token 并保存到数据库
                session('user_token', $user_token);
                //登陆日志
                $ipStr = @file_get_contents("http://api.map.baidu.com/location/ip?ak=yw5mX6eKfaCeH6iHemVEP6GiL7CAfS7t&ip=".$ip);
                $ipStr = json_decode($ipStr);
                $llModel = new \app\common\model\LoginLog();
                if ($ipStr->status == 0){
                    $ipinfo_obj = $ipStr->content->address_detail;
                    $logData = [
                            'uid' => $user['id'],
                            'ip' => $ip,
                            'country' => '中国',
                            'province' => $ipinfo_obj->province,
                            'city' => $ipinfo_obj->city,
                            'district' => ''
                    ];
					$llModel->save($logData);
                }else{
                	if( $ip == '127.0.0.1' || $ip == 'localhost' ){
                		$logData = [
                            'uid' => $user['id'],
                            'ip' => $ip,
                            'country' => '本地',
                            'province' => '',
                            'city' => '',
                            'district' => ''
	                    ];
						$llModel->save($logData);
                	}
                }
                return true;
			}
		}else{
			$this->error = '用户不存在';
			return false;
		}
    }




}