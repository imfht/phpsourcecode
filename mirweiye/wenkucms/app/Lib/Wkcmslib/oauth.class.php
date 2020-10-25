<?php

/**
 * 第三方登陆
 *
 * @author andery
 */
class oauth {

    private $_type = '';
    private $_setting = array();

    public function __construct($type) {
        $this->_type = $type;
        //加载登陆接口配置
        $setting = M('oauth')->where(array('code' => $type))->getField('config');
        $this->_setting = unserialize($setting);
        //导入接口文件
        include_once LIB_PATH . 'Wkcmslib/oauth/' . $type . '/' . $type . '.php';
        $om_class = $type . '_oauth';
        $this->_om = new $om_class($this->_setting);
    }

    /**
     * 跳转到授权页面
     */
    public function authorize() {
        redirect($this->_om->getAuthorizeURL());
    }

    /**
     * 登陆回调
     */
    public function callbackLogin($request_args) {
        $user = $this->_om->ogetUserInfo($request_args);
        $bind_user = $this->_checkBind($this->_type, $user['keyid']);
        if ($bind_user) {
            //已经绑定过则更新绑定信息 自动登陆
            $this->_updateBindInfo($user);
            $user_info = M('user')->field('uid,username')->where(array('uid' => $bind_user['uid']))->find();
            //登陆
            $this->_oauth_visitor()->assign_info($user_info);

            $refer = session('refer');
            if ($refer) {
                $refer = base64_decode($refer);
                $ret_url = $refer;
                session('refer', null);
            } else {
                $ret_url = U('ucenter/index');
            }

            return $ret_url;
        } else {
            //处理用户名
            if (M('user')->where(array('username' => $user['keyname']))->count()) {
                $user['Wkcms_user_name'] = $user['keyname'] . '_' . mt_rand(99, 9999);
            } else {
                $user['Wkcms_user_name'] = $user['keyname'];
            }
            $user['Wkcms_user_name'] = urlencode($user['Wkcms_user_name']);
            $user['keyname'] = urlencode($user['keyname']);
            if ($user['keyavatar_big']) {
                //下载原始头像到本地临时储存  用日期文件夹分类  方便清理
                $user['temp_avatar'] = '';
                $avatar_temp_root = C('Wkcms_attach_path') . 'avatar/';
                $temp_dir = date('ymd', time()) . '/';
                $file_name = date('ymdhis' . mt_rand(1000, 9999)) . '.jpg';

                mkdir($avatar_temp_root . $temp_dir);
                
                $down = new Http();
                $down->curlDownload($user['keyavatar_big'],$avatar_temp_root . $temp_dir . $file_name);
               // file_put_contents($avatar_temp_root . $temp_dir . $file_name, $image_content);
                $user['temp_avatar'] = $temp_dir . $file_name;
            }
            $user['type'] = $this->_type;
            //把第三方的数据存到COOKIE
            session('user_bind_info', $user);
            return U('user/binding'); //跳转到绑定注册页
        }
    }

    /**
     * 绑定回调
     */
    public function callbackBind($request_args) {
        if (!session('user_info')) {
            return U('user/login');
        }
        $mt_user = session('user_info');
        $user = $this->_om->ogetUserInfo($request_args);
        $bind_user = $this->_checkBind($this->_type, $user['keyid']);
        if ($bind_user['uid'] && $bind_user['uid'] != $mt_user['uid']) {
            die('此帐号已经绑定过本站');
        }
        $user['wkcms_uid'] = $mt_user['uid'];
        $this->bindUser($user);
        return U('ucenter/bangding');
    }

    /**
     * 更新绑定信息
     */
    private function _updateBindInfo($user) {
        $info = serialize($user['bind_info']);
        M('user_bind')->where(array('keyid' => $user['keyid']))->save(array('info' => $info));
    }

    /**
     * 绑定帐号
     */
    public function bindUser($user) {
        $bind_info = serialize($user['bind_info']);
        $bind_user = array(
            'uid' => $user['wkcms_uid'],
            'type' => $this->_type,
            'keyid' => $user['keyid'],
            'info' => $bind_info
        );
        M('user_bind')->add($bind_user);
    }

    //用户完善信息之后绑定 需要手动增加qp_uid值
    public function bindByData($user) {
        $this->bindUser($user);
    }

    /**
     * 检测用户是否已经绑定过本站
     */
    private function _checkBind($type, $key_id) {
        return M('user_bind')->where(array('type' => $type, 'keyid' => $key_id))->find();
    }

    /**
     * 访问者
     */
    private function _oauth_visitor() {
        include_once (WKCMS_PATH . 'app/Lib/Wkcmslib/user_visitor.class.php');
        return new user_visitor();
    }

    /**
     * 返回需要的参数
     */
    public function NeedRequest() {
        return $this->_om->NeedRequest();
    }

}