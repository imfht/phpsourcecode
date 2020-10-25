<?php
namespace app\member\service;
/**
 * 会员处理
 */
class MemberService extends \app\base\service\BaseService {


    /**
     * 登录用户
     * @param string $username
     * @param string $password
     * @param string $type
     * @param string $platform
     * @return bool
     */
    public function loginUser($username = '', $password = '', $type = 'all', $platform = 'web') {
        if (empty($username) || empty($password)) {
            return $this->error('请输入帐号和密码!');
        }
        $config = target('member/MemberConfig')->getConfig();
        if (empty($type)) {
            $type = $config['reg_type'];
        }
        $type = strtolower($type);
        if ($type <> 'tel' && $type <> 'email' && $type <> 'all') {
            return $this->error('登录类型错误!');
        }
        if ($type == 'all') {
            if (!filter_var($username, \FILTER_VALIDATE_EMAIL)) {
                $type = 'tel';
            } else {
                $type = 'email';
            }
        }
        if ($type == 'tel') {
            if (!preg_match('/(^1[3|4|5|7|8][0-9]{9}$)/', $username)) {
                return $this->error('手机号码错误!');
            }
        }
        if ($type == 'email') {
            if (!filter_var($username, \FILTER_VALIDATE_EMAIL)) {
                return $this->error('邮箱账号不正确!');
            }
        }
        if (!preg_match('/^[a-zA-Z\d_]{6,18}$/', $password)) {
            return $this->error('密码必须为6-12个字符！');
        }
        $info = target('member/MemberUser')->getWhereInfo([
            $type => $username
        ]);
        if (empty($info)) {
            return $this->error('帐号或者密码输入错误!');
        }
        $password = md5($password);
        if ($info['password'] <> $password) {
            return $this->error('帐号或者密码输入错误!');
        }

        $data = [];
        $data['user_id'] = $info['user_id'];
        $data['login_time'] = time();
        $data['login_ip'] = \dux\lib\Client::getUserIp();
        if (!target('member/MemberUser')->edit($data)) {
            return $this->error('系统繁忙,请稍后登录!');
        }

        $config = \dux\Config::get('dux.use');
        $loginData = array();
        $loginData['uid'] = $info['user_id'];
        $loginData['token'] = sha1($password . $config['safe_key']);

        $hookList = run('service', 'member', 'login', [$info['user_id'], $platform]);
        foreach ($hookList as $app => $vo) {
            if (!$vo) {
                target('member/MemberUser')->rollBack();
                return $this->error(target($app . '/Member', 'service')->getError());
            }
        }

        $loginData['data'] = $info;

        return $this->success($loginData);
    }



    /**
     * 获取账户类型
     * @param $username
     * @return bool
     */
    public function getUserType($username) {
        if (empty($username)) {
            return $this->error('请输入邮箱或手机号码!');
        }

        if (!filter_var($username, \FILTER_VALIDATE_EMAIL)) {
            $type = 'tel';
        } else {
            $type = 'email';
        }
        switch ($username) {
            case 'tel':
                if (!preg_match('/(^1[3|4|5|7|8][0-9]{9}$)/', $username)) {
                    return $this->error('手机号码错误!');
                }
                break;
            case 'email' :
                if (!filter_var($username, \FILTER_VALIDATE_EMAIL)) {
                    return $this->error('邮箱账号不正确!');
                }
                break;
        }
        return $this->success($type);
    }

    /**
     * 重置密码
     * @param $username
     * @param $password
     * @param $code
     * @return bool
     */
    public function forgotUser($username = '', $password = '', $code = '') {
        if (empty($username) || empty($password)) {
            return $this->error('请输入新密码!');
        }
        if (!filter_var($username, \FILTER_VALIDATE_EMAIL)) {
            $type = 'tel';
        } else {
            $type = 'email';
        }
        if ($type == 'tel') {
            if (!preg_match('/(^1[3|4|5|6|7|8|9][0-9]{9}$)/', $username)) {
                return $this->error('手机号码错误!');
            }
        }
        if ($type == 'email') {
            if (!filter_var($username, \FILTER_VALIDATE_EMAIL)) {
                return $this->error('邮箱账号不正确!');
            }
        }

        $password = trim($password);
        if (!preg_match('/^[a-zA-Z\d_]{6,18}$/', $password)) {
            return $this->error('密码必须为6-12个字符！');
        }
        $info = target('member/MemberUser')->getWhereInfo([
            $type => $username
        ]);
        if (empty($info)) {
            return $this->error('该用户不存在!');
        }

        $status = $this->checkVerify($username, $code);
        if (!$status) {
            return $status;
        }
        $data = array();
        $data['user_id'] = $info['user_id'];
        $data['password'] = md5($password);
        if (!target('member/MemberUser')->edit($data)) {
            return $this->error('重置密码失败,请稍候再试!');
        }
        return $this->success();
    }

    /**
     * 用户注册
     * @param string $username
     * @param string $password
     * @param string $code
     * @return bool
     */
    public function regUser($username = '', $password = '', $code = '', $nickname ='') {
        if (empty($username) || empty($password)) {
            return $this->error('请输入帐号和密码!');
        }
        $config = target('member/MemberConfig')->getConfig();
        $type = $config['reg_type'];
        $userType = $this->getUserType($username);
        if (!$userType) {
            return false;
        }
        if ($userType <> $type && $type <> 'all') {
            $this->error('该账号类型禁止注册');
        }

        $password = trim($password);
        if(strlen($password) < 6 || strlen($password) > 18) {
            return $this->error('密码必须为6-12个字符！');
        }

        if ($config['reg_status'] == 0) {
            return $this->error('系统暂时停止开放注册!');
        }
        if (in_array($username, explode('|', $config['reg_ban_name']))) {
            return $this->error('当前账户名被禁止注册!');
        }
		if($config['reg_ban_ip']){
			if (in_array(\dux\lib\Client::getUserIp(), explode('|', $config['reg_ban_ip']))) {
				return $this->error('当前IP被禁止注册!');
			}
		}
        if ($config['verify_status']) {
            $status = $this->checkVerify($username, $code);
            if (!$status) {
                return $status;
            }
        }
        $role = intval($config['reg_role']);
        if (empty($role)) {
            return $this->error('未设置正确角色!');
        }
        $info = target('member/MemberUser')->getWhereInfo([
            $userType => $username
        ]);
        if (!empty($info)) {
            return $this->error('该用户已被注册!');
        }
        $data = array();
        $data[$userType] = $username;
        $data['password'] = $password;
        $data['role_id'] = intval($config['reg_role']);
        $userId = target('member/MemberUser')->saveData('add', $data);

        if (!$userId) {
            return $this->error(target('member/MemberUser')->getError());
        }

        $hookList = run('service', 'member', 'reg', [$userId, $nickname ? $nickname : $username]);
        foreach ($hookList as $app => $vo) {
            if (!$vo) {
                return $this->error(target($app . '/Member', 'service')->getError());
            }
        }

        $config = \dux\Config::get('dux.use');
        $loginData = array();
        $loginData['uid'] = $userId;
        $loginData['token'] = sha1(md5($password) . $config['safe_key']);

        $loginData['data'] = target('member/MemberUser')->getUser($userId);

        return $this->success($loginData);
    }

    /**
     * 更新资料
     * @param $uid
     * @param array $data
     * @return bool
     */
    public function updateUser($uid, $data = []) {
        if (empty($data)) {
            return $this->error('账号资料获取失败!');
        }
        foreach ($data as $key => $vo) {
            $data[$key] = html_clear($vo);
        }
        $data = [
            'user_id' => $uid,
            'nickname' => $data['nickname'],
            'province' => $data['province'],
            'city' => $data['city'],
            'region' => $data['region'],
            'email' => $data['email'],
            'tel' => $data['tel'],
            'sex' => intval($data['sex']) ? 1 : 0,
            'birthday' => $data['birthday'] ? strtotime($data['birthday']) : 0
        ];
        if (empty($data['nickname'])) {
            return $this->error('请填写用户昵称!');
        }
        $data['nickname'] = \dux\lib\Str::symbolClear($data['nickname']);
        if (empty($data['nickname'])) {
            return $this->error('昵称填写不正确，请勿使用符号昵称!');
        }
        if (empty($data['user_id'])) {
            return $this->error('用户获取错误!');
        }
        $info = target('member/MemberUser')->getUser($uid);


        $config = target('member/MemberConfig')->getConfig();
        if (in_array($data['nickname'], explode('|', $config['reg_ban_name']))) {
            return $this->error('当前昵称被禁止使用!');
        }
        $count = target('member/MemberUser')->where(['nickname' => $data['nickname'], '_sql' => 'user_id <> ' . $data['user_id']])->count();
        if ($count) {
            return $this->error('该昵称已被使用!');
        }


        if(!empty($info['email'])) {
            unset($data['email']);
        }
        if(!empty($info['tel'])) {
            unset($data['tel']);
        }
        if(!empty($data['email'])) {
            if(!filter_var($info['email'], \FILTER_VALIDATE_EMAIL)) {
                return $this->error('邮箱格式输入错误!');
            }
            $count = target('member/MemberUser')->where(['email' => $data['emall'], '_sql' => 'user_id <> ' . $data['user_id']])->count();
            if ($count) {
                return $this->error('该邮箱已被使用!');
            }
        }

        if(!empty($data['tel'])) {
            if(!preg_match('/(^1[3|4|5|6|7|8|9][0-9]{9}$)/', $info['tel'])) {
                return $this->error('手机号码输入错误!');
            }
            $count = target('member/MemberUser')->where(['tel' => $data['tel'], '_sql' => 'user_id <> ' . $data['user_id']])->count();
            if ($count) {
                return $this->error('该手机已被使用!');
            }
        }

        if (!target('member/MemberUser')->edit($data)) {
            return $this->error(target('member/MemberUser')->getError());
        }
        return $this->success($data);
    }

    /**
     * 用户名获取账号信息
     * @param $username
     * @return bool
     */
    public function isUser($username) {
        $info = target('member/MemberUser')->getWhereInfo([
            'tel' => $username
        ]);
        if(empty($info)) {
            $info = target('member/MemberUser')->getWhereInfo([
                'email' => $username
            ]);
        }
        if(empty($info)) {
            return $this->error('用户不存在');
        }
        return $this->success();

    }

    /**
     * 获取验证码
     * @param string $receive
     * @param string $content
     * @param int $type
     * @return bool
     */
    public function getVerify($receive = '', $content = '', $code = 0, $type = 0, $verifyType = '') {
        if(empty($receive)) {
            return $this->error('接受账号不正确');
        }
        $userType = $this->getUserType($receive);
        if (!$userType) {
            return false;
        }
        if (empty($verifyType)) {
            if ($userType == 'tel') {
                $verifyType = 'sms';
            }
            if ($userType == 'email') {
                $verifyType = 'mail';
            }
        }
        $config = target('member/MemberConfig')->getConfig();
        $typeInfo = target('tools/ToolsSendConfig')->defaultType($verifyType);

        if (!target($typeInfo['target'], 'send')->check(['receive' => $receive])) {
            return $this->error(target($typeInfo['target'], 'send')->getError());
        }
        $info = target('member/MemberVerify')->where([
            'receive' => $receive,
            'type' => $type
        ])->order('verify_id desc')->find();
        if (!empty($info)) {
            if ($info['time'] + intval($config['verify_second']) > time()) {
                return $this->error($config['verify_second'] . '秒内无法再次获取验证码!');
            }
            $where = array();
            $where['receive'] = $receive;
            $where['type'] = $type;
            $where['_sql'] = 'time > ' . (time() - $info['verify_minute'] * 60);
            $count = target('member/MemberVerify')->where($where)->count();
            if ($count >= $config['verify_minute_num']) {
                return $this->error('验证码获取太频繁,请过段时间再试!');
            }
        }
        if (!$code) {
            $code = $this->getCode(6);
        }
        target('member/MemberVerify')->beginTransaction();
        $data = array();
        $data['time'] = time();
        $data['code'] = $code;
        $data['receive'] = $receive;
        $data['expire'] = $config['verify_expire'];
        $data['type'] = $type;
        $data['status'] = 0;
        if (!target('member/MemberVerify')->add($data)) {
            target('member/MemberVerify')->rollBack();
            return $this->error('验证码获取失败,请稍候再试!');
        }
        $siteConfig = target('site/SiteConfig')->getConfig();
        if (empty($content)) {
            if ($verifyType == 'sms') {
                $content = $config['verify_sms_tpl'];
            } else {
                $content = html_out($config['verify_mail_tpl']);
            }
        }
        $content = str_replace('[验证码]', $code, $content);
        $content = str_replace('[有效期]', $config['verify_expire'] / 60, $content);

        $status = target('tools/Tools', 'service')->sendMessage([
            'receive' => $receive,
            'class' => $verifyType,
            'title' => $siteConfig['info_name'] . '会员验证码',
            'content' => $content,
			'param' => ['code' => $code]
        ]);
        if (!$status) {
            target('member/MemberVerify')->rollBack();
            return $this->error(target('tools/Tools', 'service')->getError());
        }
        target('member/MemberVerify')->commit();
        return $this->success();
    }

    /**
     * 验证验证码
     * @param $receive
     * @param $code
     * @param int $type
     * @return bool
     */
    public function checkVerify($receive, $code, $type = 0) {
        $info = target('member/MemberVerify')->where([
            'receive' => $receive,
            'code' => $code,
            'type' => $type
        ])->order('verify_id desc')->find();
        if (empty($info)) {
            return $this->error('验证码不正确!');
        }
        if ($info['status']) {
            return $this->error('验证码已使用!');
        }
        if ($info['time'] + $info['expire'] < time()) {
            return $this->error('验证码已过期!');
        }
        $data = array();
        $data['verify_id'] = $info['verify_id'];
        $data['status'] = 1;
        target('member/MemberVerify')->edit($data);
        return $this->success();
    }

    /**
     * 生成验证码
     * @param int $length
     * @return int
     */
    public function getCode($length = 6) {
        return rand(pow(10, ($length - 1)), pow(10, $length) - 1);
    }

    /**
     * 账户充值
     * @param $rechargeNo
     * @param $money
     * @param $payName
     * @param $payNo
     * @param $title
     * @param $remark
     * @return bool
     */
    public function payRecharge($rechargeNo, $money, $payName, $payNo = '', $title = '', $remark = '') {
        if (empty($rechargeNo) || empty($money) || empty($payName)) {
            return $this->error('充值信息错误!');
        }
        $info = target('member/PayRecharge')->getWhereInfo([
            'recharge_no' => $rechargeNo
        ]);

        if(empty($info)) {
            return $this->error('充值单不存在！');
        }
        if ($money < $info['money']) {
            return $this->error('充值金额不正确!');
        }

        $status = target('member/Finance', 'service')->account([
            'user_id' => $info['user_id'],
            'money' => $money,
            'pay_no' => $payNo,
            'pay_name' => $payName,
            'title' => $title ? $title : '在线充值',
            'remark' => $remark,
            'deduct' => true
        ]);

        if (!$status) {
            return $this->error('充值支付失败!');
        }

        $status = target('member/PayRecharge')->where([
            'recharge_id' => $info['recharge_id']
        ])->data([
            'status' => 1,
            'pay_no' => $payNo ? $payNo : log_no(),
            'pay_name' => $payName,
            'remark' => $remark ? $remark : '第三方充值',
            'complete_time' => time()
        ])->update();
        if (!$status) {
            return $this->error('充值支付失败!');
        }

        $this->noticeMember('recharge', $info['user_id'], [
            '充值金额' => $money,
            '充值备注' => $remark ? $remark :'第三方充值',
            '交易编号' => $rechargeNo,
            '交易名' => $payName,
            '充值时间' => date('Y-m-d H:i:s', time()),
        ]);

        return $this->success();
    }

    /**
     * 第三方登录
     * @param $type
     * @param $openId
     * @param $nickname
     * @param string $avatar
     * @return bool
     */
    public function oauthUser($type, $openId, $payId, $nickname = '', $avatar = '') {
        if (empty($openId) || empty($type)) {
            return $this->error('平台ID为空');
        }
        $info = target('member/MemberConnect')->getWhereInfo([
            'open_id' => $openId,
            'type' => $type
        ]);
        if (empty($info)) {
            target('member/MemberUser')->beginTransaction();

            $nickname = preg_replace_callback(
                '/./u',
                function (array $match) {
                    return strlen($match[0]) >= 4 ? '' : $match[0];
                },
                $nickname);
            $data = [
                'open_id' => $openId,
                'pay_id' => $payId,
                'type' => $type,
                'data' => serialize([
                    'nickname' => $nickname,
                    'avatar' => $avatar
                ])
            ];
            if (!target('member/MemberConnect')->add($data)) {
                target('member/MemberUser')->rollBack();
                return $this->error('保存登录数据失败!');
            }
            target('member/MemberUser')->commit();
        }
        if (empty($info['user_id'])) {
            return $this->success([
                'status' => 'bind',
                'data' => [
                    'type' => $type,
                    'open_id' => $openId,
                    'pay_id' => $payId
                ]
            ]);
        } else {
            $userInfo = target('member/MemberUser')->getInfo($info['user_id']);
            $userId = $info['user_id'];
            $password = $userInfo['password'];
            $config = \dux\Config::get('dux.use');
            $loginData = array();
            $loginData['uid'] = $userId;
            $loginData['token'] = sha1($password . $config['safe_key']);
            $loginData['data'] = $userInfo;
            return $this->success([
                'status' => 'login',
                'data' => $loginData
            ]);
        }

    }

    /**
     * 关联账户信息
     * @param $connectId
     * @param $userId
     * @param string $nickname
     * @param string $avatar
     * @return bool
     */
    public function connectUser($connectId, $userId, $nickname = '', $avatar = '') {
        if (empty($connectId)) {
            return $this->error('关联信息不正确!');
        }
        if (empty($userId)) {
            return $this->error('用户信息无法获取！');
        }
        if (!empty($nickname)) {
            $data = [
                'user_id' => $userId,
                'nickname' => $nickname
            ];
            if (!target('member/MemberUser')->edit($data)) {
                return $this->error('账户信息更新失败!');
            }
        }
        $data = [
            'connect_id' => $connectId,
            'user_id' => $userId,
        ];
        if (!target('member/MemberConnect')->edit($data)) {
            return $this->error('关联登录信息失败!');
        }

        if (!empty($avatar)) {
            if (!target('member/MemberUser')->avatarUser($userId, $avatar)) {
                return $this->error($this->getError());
            }
        }
        return $this->success();
    }

    /**
     * 注册第三方账号
     * @param $connectId
     * @param string $nickname
     * @return array|bool
     */
    public function regOauthUser($connectId, $nickname = '') {
        if (empty($connectId)) {
            return $this->error('关联信息不正确!');
        }
        $config = target('member/MemberConfig')->getConfig();
        if(empty($nickname)) {
            $nickname = '蒙面人';
        }
        $password = \dux\lib\Str::randStr(15);
        $data = array();
        $data['nickname'] = $nickname;
        $data['password'] = $password;
        $data['role_id'] = intval($config['reg_role']);
        $userId = target('member/MemberUser')->saveData('add', $data);
        if (!$userId) {
            return $this->error(target('member/MemberUser')->getError());
        }
        $hookList = run('service', 'member', 'reg', [$userId]);
        foreach ($hookList as $app => $vo) {
            if (!$vo) {
                target('member/MemberUser')->rollBack();
                return $this->error(target($app . '/Member', 'service')->getError());
            }
        }
        $config = \dux\Config::get('dux.use');
        $password = md5(\dux\lib\Str::randStr(15));
        $token = sha1(md5($password) . $config['safe_key']);
        return [
            'uid' => $userId,
            'token' => $token
        ];
    }

    /**
     * 会员通知
     * @param $name
     * @param $userId
     * @param array $data
     * @return bool
     */
    public function noticeMember($name, $userId, $data = []) {
        $config = target('member/memberConfig')->getConfig();

        $status = $config['notice_' . $name . '_status'];
        $class = unserialize($config['notice_' . $name . '_class']);
        $title = $config['notice_' . $name . '_title'];

        if (!$status) {
            return $this->error('通知类型未开启!');
        }
        if (empty($class) || empty($title)) {
            return $this->error('通知内容未设置完整!');
        }

        foreach ($class as $vo) {
            $content = $config['notice_' . $name . '_' . $vo . '_tpl'];
            foreach ($data as $key => $v) {
                $content = str_replace('[' . $key . ']', $v, $content);
            }
            if(LAYER_NAME == 'mobile') {
                $layer = 'mobile';
            }else {
                $layer = 'controller';
            }
            $url = url($layer . '/member/index/index', [], true);
            $status = target('tools/Tools', 'service')->sendMessage([
                'receive' => $userId,
                'class' => $vo,
                'title' => $title,
                'content' => $content,
                'user_status' => 1,
                'param' => [
                    'url' => $url
                ]
            ]);
            if (!$status) {
                return $this->error(target('tools/Tools', 'service')->getError());
            }
        }
        return $this->success();

    }


}
