<?php
/*
 * IKPHP爱客网 安装程序 @copyright (c) 2012-3000 IKPHP All Rights Reserved @author 小麦
* @Email:810578553@qq.com
* @小麦 修改时间2014年3月19日晚 用户基础类
*/
namespace Common\Model;
use Think\Model;
use User\Api\UserApi;

class UserModel extends Model
{
    /* 用户模型自动完成 */
    protected $_auto = array(
        array('count_login', 0, self::MODEL_INSERT),
        array('reg_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('reg_time', NOW_TIME, self::MODEL_INSERT),
        array('last_login_ip', 0, self::MODEL_INSERT),
        array('last_login_time', 0, self::MODEL_INSERT),
        array('update_time', NOW_TIME),
        array('status', 0, self::MODEL_INSERT),
    );	
    
    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid){
        /* 检测是否在当前应用注册 */
        $user = $this->field(true)->find($uid);
        if(!$user){ //未注册
            /* 在当前应用中注册用户 */
        	$Api = new UserApi();
        	$info = $Api->info($uid); 
            $user = $this->create(array('username'=>$info[1],'email'=>$info[2], 'status' => 0));
            $user['userid'] = $uid;
            if(!$this->add($user)){
                $this->error = '前台用户信息注册失败，请重试！';
                return false;
            }
        } elseif(0 != $user['status']) {
            $this->error = '用户未激活或已禁用！'; //应用级别禁用
            return false;
        }

        /* 登录用户 */
        $this->autoLogin($user);

        //记录行为
       // action_log('user_login', 'member', $uid, $uid);

        return true;
    }
    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user){
        /* 更新登录信息 */
        $data = array(
            'userid'          => $user['userid'], 	
            'login'           => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
        );
        $this->save($data);
		$doname = empty($user['doname']) ? $user['userid'] : $user['doname'];
        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'userid'             => $user['userid'],
            'username'        => $user['username'],
        	'email'        => $user['email'],
			'doname'        => $doname,        
            'last_login_time' => $user['last_login_time'],
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));

    }    
    /**
     * 注销当前用户
     * @return void
     */
    public function logout(){
        session('user_auth', null);
        session('user_auth_sign', null);
    }
    /**
     * 获取字段信息
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */ 
    public function get($key = null, $id = null){
    	$uid = is_null($id) ? is_login() : $id ;
    	if(empty($uid)) return false;
    	if(is_null($key)){
    		$info = $this->find($uid);
    	}else{
    	    //获取用户表字段
    		$fields = $this->getDbFields();
    		if (!is_null(array_search($key, $fields))) { 
    			$info = $this->where(array('userid' => $uid))->getField($key);
    		}   		
    	}
    	return $info;
    }
	//获取活跃会员
	public function getHotUser($limit){
		$where = array (
				'isenable' => 0, // 0 表示正常 1停用
		);
		$arrUserid = $this->field('userid')->where($where)->order('last_login_time desc')->limit($limit)->select();
		foreach($arrUserid as $item){
			$result[] = $this->getOneUser($item['userid']);
		}
		return $result;
	}
	//获取一个用户的信息
	public function getOneUser($userid){
	
		$strUser = $this->where(array('userid'=>$userid))->find();
		if(empty($strUser)){
			return false;
		}
		$strUser['face'] = $userid;
		$strUser['face'] = avatar($userid, 48);
		$strUser['face_160'] = avatar($userid, 160);
		
		//地区
		if($strUser['areaid'] > 0){
			$strUser['area'] = D('Common/Area')->getOneArea($strUser['areaid']);
		}else{
			$strUser['area'] = array(
				'areaid'	=> '0',
				'areaname' => '火星',
			);
		}
		//在线状态
		$time = time() - 15 * 60;
		$isonline = M('user_online')->where(array('userid'=>$userid,'ctime'=>array('gt',$time)))->count();
		$strUser['isonline'] = $isonline > 0 ? 1 : 0 ;
		//个性域名
        $strUser['doname'] = empty($strUser['doname']) ? $userid : $strUser['doname'];	
		//签名
		$pattern='/(http:\/\/|https:\/\/|ftp:\/\/)([\w:\/\.\?=&-_]+)/is';

		$strUser['signed'] = hview(preg_replace($pattern, '<a rel="nofollow" target="_blank" href="\1\2">\1\2</a>', $strUser['signed']));
		
		return $strUser;
	}
	// 判断是否存在该用户
	public function isUser($userid){
		$where = array (
				'userid' => $userid,
		);
		$result = $this->where($where)->count('userid');
		if($result){
			return true;
		}else{
			return false;
		}
	}
	// 判断我是否已经关注过他
	public function isFollow($userid,$userid_follow){
		$where = array (
				'userid' => $userid,
				'userid_follow' => $userid_follow,
		);
		$result = M('user_follow')->where($where)->count('*');
		if($result){
			return true;
		}else{
			return false;
		}
	}
	// 关注用户
	public function follow_user($userid, $userid_follow){
		$data = array (
				'userid' => $userid,
				'userid_follow' => $userid_follow,
				'addtime'=>time(),
		);		
		$user_follow_mod = M('user_follow'); 
		if (false !== $user_follow_mod->create ( $data )) {
			$id = $user_follow_mod->add ();
			//更新关注数
			$this->where(array('userid'=>$userid))->setInc('count_follow'); //关注数
			$this->where(array('userid'=>$userid_follow))->setInc('count_followed'); //被关注数
		}
		return true;
	}
	// 取消关注用户
	public function unfollow_user($userid, $userid_follow){
		$where = array (
				'userid' => $userid,
				'userid_follow' => $userid_follow,
		);
		M('user_follow')->where($where)->delete();
		//更新关注数
		$this->where(array('userid'=>$userid))->setDec('count_follow'); //关注数
		$this->where(array('userid'=>$userid_follow))->setDec('count_followed'); //被关注数

		return true;
	}
	// 判断是否已经取消
	public function isunFollow($userid,$userid_follow){
		$where = array (
				'userid' => $userid,
				'userid_follow' => $userid_follow,
		);
		$result = M('user_follow')->where($where)->count('*');
		if($result){
			return true;
		}else{
			return false;
		}
	}
	// 获取我关注的用户
	public function getfollow_user($userid, $limit){
		$where = array (
				'userid' => $userid,
		);
		$followUsers = M('user_follow')->where($where)->order('addtime desc')->limit($limit)->select();
		if(is_array($followUsers)){
			foreach($followUsers as $item){
				$result[] =  $this->getOneUser($item['userid_follow']);
			}
		}
		return $result;
	}
	// 获取某人被关注 用户
	public function getUserFollow($userid_follow,$limit){
		$where = array (
				'userid_follow' => $userid_follow,
		);
		$arrUser = M('user_follow')->where($where)->order('addtime desc')->limit($limit)->select();
		if(is_array($arrUser)){
			foreach($arrUser as $key=>$item){
				$result[$key] =  $this->getOneUser($item['userid']);
			}
		}
		return $result;
	}
	//根据用户积分获取用户角色
	public function getRole($score){
		$arrRole = F('user_role');
		foreach($arrRole as $key=>$item){
			if($score > $item['score_start'] && $score <= $item['score_end'] || $score > $item['score_start'] && $item['score_end']==0 || $score >=0 && $score <= $item['score_end']){
				return $item['rolename'];
			}
		}
	}
}