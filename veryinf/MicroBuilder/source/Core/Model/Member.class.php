<?php
namespace Core\Model;
use Think\Log;
use Think\Model;

class Member extends Model {
    protected $autoCheckFields = false;

    const STATUS_DISABLED = '-1';
    const STATUS_ENABLED = '0';
     /**
     * 选项: 会员策略
     */
    const OPT_POLICY = 'POLICY';
    const OPT_POLICY_UNION = 'union';
    const OPT_POLICY_CLASSICAL = 'classical';

    /**
     * 选项: 会员积分选项
     */
    const OPT_CREDITS = 'CREDITS';

    /**
     * 选项: 会员积分策略
     */
    const OPT_CREDITPOLICY = 'CREDITPOLICY';
    const OPT_CREDITPOLICY_ACTIVITY = 'activity';
    const OPT_CREDITPOLICY_CURRENCY = 'currency';

    /**
     * 微信客户端 Auth 方式, 指定具有oAuth权限的服务号
     */
    const OPT_AUTH_WEIXIN = 'AUTH.WEIXIN';

    private static function getOptions() {
        $keys = array();
        $keys[] = self::OPT_POLICY;
        $keys[] = self::OPT_CREDITS;
        $keys[] = self::OPT_CREDITPOLICY;
        $keys[] = self::OPT_AUTH_WEIXIN;
        return $keys;
    }
    
    public static function loadSettings($flush = false) {
        $s = C('MS');
        if(empty($s) || $flush) {
            $keys = self::getOptions();
            $s = Utility::loadSettings('Member', $keys);
            if(empty($s[self::OPT_POLICY])) {
                $s[self::OPT_POLICY] = self::OPT_POLICY_UNION;
            }
            if(empty($s[self::OPT_CREDITS]) || !is_array($s[self::OPT_CREDITS])) {
                $s[self::OPT_CREDITS] = array(
                    array(
                        'name'      => 'credit1',
                        'title'     => '积分',
                        'enabled'   => true,
                        'issystem'  => true,
                    ),
                    array(
                        'name'      => 'credit2',
                        'title'     => '余额',
                        'enabled'   => true,
                        'issystem'  => true,
                    ),
                    array(
                        'name'      => 'credit3',
                        'title'     => '',
                        'enabled'   => false,
                        'issystem'  => false,
                    ),
                    array(
                        'name'      => 'credit4',
                        'title'     => '',
                        'enabled'   => false,
                        'issystem'  => false,
                    ),
                    array(
                        'name'      => 'credit5',
                        'title'     => '',
                        'enabled'   => false,
                        'issystem'  => false,
                    ),
                );
            }
            if(empty($s[self::OPT_CREDITPOLICY]) || !is_array($s[self::OPT_CREDITPOLICY])) {
                $s[self::OPT_CREDITPOLICY] = array(
                    self::OPT_CREDITPOLICY_ACTIVITY => 'credit1',
                    self::OPT_CREDITPOLICY_CURRENCY => 'credit2',
                );
            }
            if(empty($s[self::OPT_AUTH_WEIXIN])) {
                $s[self::OPT_AUTH_WEIXIN] = 0;
            }
            C('MS', $s);
        }
    }

    public static function saveSettings($settings) {
        $keys = self::getOptions();
        $settings = coll_elements($keys, $settings);
        return Utility::saveSettings('Member', $settings);
    }

    /**
     * 粉丝用户配置信息所有字段
     * @return array
     */
    public static function fields() {
        return array(
            'mobile' => array(
                'name'		=> 'mobile',
                'title'		=> '手机号码',
                'icon'		=> '',
                'regex'     => '',
            ),
            'email' => array(
                'name'		=> 'email',
                'title'		=> '电子邮箱',
                'icon'		=> '',
            ),
            'realname' => array(
                'name'		=> 'realname',
                'title'		=> '真实姓名',
                'icon'		=> 'user',
            ),
            'nickname' => array(
                'name'		=> 'nickname',
                'title'		=> '昵称',
                'icon'		=> '',
            ),
            'avatar' => array(
                'name'		=> 'avatar',
                'title'		=> '头像',
                'icon'		=> '',
            ),
            'qq' => array(
                'name'		=> 'qq',
                'title'		=> 'QQ号',
                'icon'		=> '',
            ),
            'vip' => array(
                'name'		=> 'vip',
                'title'		=> 'VIP级别,0为普通会员',
                'icon'		=> '',
            ),
            'gender' => array(
                'name'		=> 'gender',
                'title'		=> '性别(0:保密 1:男 2:女)',
                'icon'		=> '',
            ),
            'birth' => array(
                'name'		=> 'birth',
                'title'		=> '生日',
                'icon'		=> '',
            ),
            'constellation' => array(
                'name'		=> 'constellation',
                'title'		=> '星座',
                'icon'		=> '',
            ),
            'zodiac' => array(
                'name'		=> 'zodiac',
                'title'		=> '生肖',
                'icon'		=> '',
            ),
            'telephone' => array(
                'name'		=> 'telephone',
                'title'		=> '固定电话',
                'icon'		=> 'phone',
            ),
            'idcard' => array(
                'name'		=> 'idcard',
                'title'		=> '证件号码',
                'icon'		=> '',
            ),
            'studentid' => array(
                'name'		=> 'studentid',
                'title'		=> '学号',
                'icon'		=> '',
            ),
            'grade' => array(
                'name'		=> 'grade',
                'title'		=> '班级',
                'icon'		=> '',
            ),
            'address' => array(
                'name'		=> 'address',
                'title'		=> '邮寄地址',
                'icon'		=> 'building',
            ),
            'zip' => array(
                'name'		=> 'zip',
                'title'		=> '邮编',
                'icon'		=> '',
            ),
            'nationality' => array(
                'name'		=> 'nationality',
                'title'		=> '国籍',
                'icon'		=> '',
            ),
            'state' => array(
                'name'		=> 'state',
                'title'		=> '居住省份',
                'icon'		=> 'map-marker',
            ),
            'city' => array(
                'name'		=> 'city',
                'title'		=> '居住城市',
                'icon'		=> 'map-marker',
            ),
            'district' => array(
                'name'		=> 'district',
                'title'		=> '居住区',
                'icon'		=> 'map-marker',
            ),
            'graduateschool' => array(
                'name'		=> 'graduateschool',
                'title'		=> '毕业学校',
                'icon'		=> '',
            ),
            'company' => array(
                'name'		=> 'company',
                'title'		=> '公司',
                'icon'		=> '',
            ),
            'education' => array(
                'name'		=> 'education',
                'title'		=> '学历',
                'icon'		=> '',
            ),
            'occupation' => array(
                'name'		=> 'occupation',
                'title'		=> '职业',
                'icon'		=> '',
            ),
            'position' => array(
                'name'		=> 'position',
                'title'		=> '职位',
                'icon'		=> '',
            ),
            'revenue' => array(
                'name'		=> 'revenue',
                'title'		=> '年收入',
                'icon'		=> '',
            ),
            'affectivestatus' => array(
                'name'		=> 'affectivestatus',
                'title'		=> '情感状态',
                'icon'		=> '',
            ),
            'lookingfor' => array(
                'name'		=> 'lookingfor',
                'title'		=> ' 交友目的',
                'icon'		=> '',
            ),
            'bloodtype' => array(
                'name'		=> 'bloodtype',
                'title'		=> '血型',
                'icon'		=> '',
            ),
            'height' => array(
                'name'		=> 'height',
                'title'		=> '身高',
                'icon'		=> '',
            ),
            'weight' => array(
                'name'		=> 'weight',
                'title'		=> '体重',
                'icon'		=> '',
            ),
            'alipay' => array(
                'name'		=> 'alipay',
                'title'		=> '支付宝帐号',
                'icon'		=> '',
            ),
            'msn' => array(
                'name'		=> 'msn',
                'title'		=> 'MSN',
                'icon'		=> '',
            ),
            'taobao' => array(
                'name'		=> 'taobao',
                'title'		=> '阿里旺旺',
                'icon'		=> '',
            ),
            'site' => array(
                'name'		=> 'site',
                'title'		=> '主页',
                'icon'		=> '',
            ),
            'introduce' => array(
                'name'		=> 'introduce',
                'title'		=> '自我介绍',
                'icon'		=> '',
            ),
            'interest' => array(
                'name'		=> 'interest',
                'title'		=> '兴趣爱好',
                'icon'		=> '',
            ),
        );
    }

    /**
     * 获取当前会员身份
     */
    public function auth() {
        $uid = session('__:uid');
        if(!empty($uid)) {
            $profile = $this->profile($uid);
            if(!empty($profile)) {
                C('MEMBER', $profile);
                return true;
            }
        }
        if(IN_CONTAINER_WEIXIN) {
            Member::loadSettings();
            $setting = C('MS');
            $auth = $setting[Member::OPT_AUTH_WEIXIN];
            if($auth == '0') {
                $account = null;
            } else {
                $a = new Account();
                $account = $a->getAccount($auth, Account::ACCOUNT_WEIXIN);
                $callback = urlencode(__HOST__ . U('/auth/weixin'));
            }
            if(empty($account)) {
                $account = array();
                $account['appid'] = 'wx2f9d7b7b086d0fd8';
                $callback = urlencode('http://cloud.microb.cn/proxy/auth/weixin?site=' . C(''));
            }
            
            $state = $_SERVER['REQUEST_URI'];
            $stateKey = substr(md5($state), 0, 8);
            session('auth:forward', array($stateKey, $state));
            $forward = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$account['appid']}&redirect_uri={$callback}&response_type=code&scope=snsapi_base&state={$stateKey}#wechat_redirect";
            redirect($forward);
        } elseif (IN_CONTAINER_ALIPAY) {
            
        } else {
            $state = $_SERVER['REQUEST_URI'];
            $stateKey = substr(md5($state), 0, 8);
            session('auth:forward', array($stateKey, $state));
            $url = U('/auth/basic?forward=' . $stateKey);
            redirect($url);
        }
        return false;
    }
    
    public function profile($uid, $fields = array()) {
        $uid = intval($uid);
        $member = $this->table('__MMB_MEMBERS__')->where("`uid`='{$uid}'")->find();
        if(!empty($member) && !empty($fields))  {
            $f = "'realname', 'nickname', 'avatar', 'qq', 'vip', 'gender', 'birthyear', 'birthmonth', 'birthday', 'constellation', 'zodiac', 'telephone', 'idcard', 'studentid', 'grade', 'address', 'zipcode', 'nationality', 'resideprovince', 'residecity', 'residedist', 'graduateschool', 'company', 'education', 'occupation', 'position', 'revenue', 'affectivestatus', 'lookingfor', 'bloodtype', 'height', 'weight', 'alipay', 'msn', 'taobao', 'site', 'bio', 'interest', 'field1', 'field2', 'field3', 'field4', 'field5', 'field6', 'field7', 'field8'";
            $fs = array_intersect($f, $fields);
            $profile = $this->table('__MMB_PROFILES__')->field($fs)->where("`uid`='{$uid}'")->find();
            $member = array_merge($member, $profile);
            $member = coll_elements($fields, $member);
        }
        return $member;
    }

    /**
     * 获取当前会员的必须资料
     */
    public function required($uid, $fields = array(), $message = '', $force = false, $forward = '') {
        if(empty($message)) {
            $message = '您好, 需要完善个人资料才能继续';
        }
        if(empty($fields) || !is_array($fields)) {
            return false;
        }
        $profiles = $this->profile($uid, $fields);
        if(empty($profiles)) {
            return false;
        }
        $isError = false;
        foreach($profiles as $key => $profile) {
            if(!in_array($key, array('gender')) && empty($profile)) {
                $isError = true;
                break;
            }
        }
        if(!$isError && !$force) {
            return $profiles;
        }
        
        if(isset($fields['birth']) || isset($fields['birthyear']) || isset($fields['birthmonth']) || isset($fields['birthday'])) {
            unset($fields['birthyear'], $fields['birthmonth'], $fields['birthday']);
            $fields[] = 'birthyear';
            $fields[] = 'birthmonth';
            $fields[] = 'birthday';
        }
        if(isset($fields['receiver']) || isset($fields['state']) || isset($fields['city']) || isset($fields['district'])) {
            unset($fields['state'], $fields['city'], $fields['district']);
            $fields[] = 'state';
            $fields[] = 'city';
            $fields[] = 'district';
        }

        $require = array();
        $require['message'] = $message;
        $require['fields'] = $fields;
        session('__:require', $require);
        if(empty($forward)) {
            $forward = $_SERVER['REQUEST_URI'];
        }
        session('require:forward', $forward);
        redirect(U('auth/require?force=' . ($force ? 'true' : 'false')));
    }
    
    public function login($uid) {
        session('__:uid', $uid);
    }
    
    public function create($member, $fan = null) {
        if(!empty($member['mobile']) && !empty($member['password'])) {
            if(!preg_match('/^1\d{10}$/', $member['mobile'])) {
                return error(-1, '你输入的手机号格式不正确');
            }
            $condition = '`mobile`=:mobile';
            $pars = array();
            $pars[':mobile'] = $member['mobile'];
            $exist = $this->table('__MMB_MEMBERS__')->where($condition)->bind($pars)->find();
            if(!empty($exist)) {
                return error(-2, '你输入的手机号已经注册过, 请直接登陆或者更换后重试');
            }

            $rec = coll_elements(array('mobile', 'password'), $member, '');
            $rec['salt'] = util_random(8);
            $rec['password'] = Utility::encodePassword($rec['password'], $rec['salt']);
        } else {
            $rec = array();
            $rec['mobile'] = '';
            $rec['password'] = '';
            $rec['salt'] = util_random(8);
        }
        
        $condition = '`isdefault`=1';
        $pars = array();
        $group = $this->table('__MMB_GROUPS__')->where($condition)->bind($pars)->find();
        if(empty($group)) {
            $group['id'] = 0;
        }
        $rec['groupid'] = $group['id'];
        $rec['createtime'] = TIMESTAMP;
        $rec['joinfrom'] = $member['from'];
        if(empty($rec['joinfrom'])) {
            $rec['joinfrom'] = '';
        }
        $ret = $this->table('__MMB_MEMBERS__')->data($rec)->add();
        if(empty($ret)) {
            return error(-2, '系统错误, 创建会员失败, 请稍后重试');
        }
        $uid = $this->getLastInsID();
        $this->table('__MMB_PROFILES__')->data(array('uid'=>$uid))->add();
        if(!empty($fan) && empty($fan['uid'])) {
            if($rec['joinfrom'] == 'weixin') {
                $record = array();
                $record['uid'] = $uid;
                $this->table('__MMB_MAPPING_FANS__')->data($record)->where("`fanid`='{$fan['fanid']}' OR `unionid`='{$fan['unionid']}'")->save();
            }
        }
        return $uid;
    }
    
    
    
    public function update($uid, $profiles) {
        $uid = intval($uid);
        $struct = array_keys(self::fields());
        $members = array();
        foreach ($profiles as $field => $value) {
            if(!in_array($field, $struct)) {
                unset($profiles[$field]);
                continue;
            }
            if(in_array($field, array('mobile', 'email'))) {
                $members[$field] = $value;
                unset($profiles[$field]);
            }
        }
        
        $exists = $this->table('__MMB_MEMBERS__')->where("`uid`='{$uid}'")->find();
        if(empty($exists)) {
            return error(-1, '指定的用户不存在');
        }
        $ret = $this->table('__MMB_PROFILES__')->data($profiles)->where("`uid`='{$uid}'")->save();
        return $ret !== false;
    }

    public function getGroups() {
        $ret = array();
        $condition = '';
        $pars = array();
        $roles = $this->table('__MMB_GROUPS__')->where($condition)->bind($pars)->order('`orderlist`')->select();
        if(!empty($roles)) {
            $ret = array_merge($ret, $roles);
        }
        return $ret;
    }

    public function removeGroup($id) {
        $id = intval($id);
        $ret = $this->table('__MMB_GROUPS__')->where("`id`={$id}")->delete();
        return !!$ret;
    }
    
    public function fetchFan($uid, $platform) {
        $condition = "`platformid`=:platformid AND `uid`=:uid";
        $pars = array();
        $pars[':platformid'] = $platform;
        $pars[':uid'] = $uid;
        $fan = $this->table('__MMB_MAPPING_FANS__')->where($condition)->bind($pars)->find();
        return $fan;
    }
}
