<?php

/**
 * 用户管理
 */
namespace app\member\model;

use app\system\model\SystemModel;

class MemberUserModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'user_id',
        'validate' => [
            'role_id' => [
                'empty' => ['', '角色不正确!', 'must', 'all'],
            ],
            'email' => [
                'email' => ['', '邮箱填写不正确!', 'value', 'all'],
                'unique' => ['', '已存在相同的用户名!', 'value', 'all'],
            ],
            'tel' => [
                'phone' => ['', '手机号码填写不正确!', 'value', 'all'],
                'unique' => ['', '已存在相同的用户名!', 'value', 'all'],
            ],
            'nickname' => [
                'len' => ['2,50', '昵称只能为2~30个字符!', 'value', 'all'],
            ],
            'password' => [
                'len' => ['6,18', '请输入6~18位密码!', 'must', 'add'],
            ]
        ],
        'format' => [
            'nickname' => [
                'function' => ['html_clear', 'all'],
            ],
            'province' => [
                'function' => ['html_clear', 'all'],
            ],
            'city' => [
                'function' => ['html_clear', 'all'],
            ],
            'region' => [
                'function' => ['html_clear', 'all'],
            ],
            'password' => [
                'ignore' => ['', 'edit'],
            ],
            'reg_time' => [
                'function' => ['time', 'add'],
            ]
        ],
        'into' => '',
        'out' => '',
    ];

    protected function _saveBefore($data) {
        if (empty($data['tel']) && empty($data['email']) && empty($data['nickname'])) {
            $this->error = '手机号或邮箱或昵称必须存在一个';
            return false;
        }
        if ($data['password']) {
            $data['password'] = md5($data['password']);
        } else {
            unset($data['password']);
        }
        return $data;
    }

    protected function base($where) {
        return $this->table('member_user(A)')
            ->join('member_role(B)', ['B.role_id', 'A.role_id'])
            ->join('member_grade(C)', ['C.grade_id', 'A.grade_id'])
            ->field(['A.*', 'B.name(role_name)', 'C.name(grade_name)'])
            ->where((array)$where);
    }

    public function loadList($where = array(), $limit = 0, $order = '') {
        $list = $this->base($where)
            ->limit($limit)
            ->order($order)
            ->select();
        foreach ($list as $key => $vo) {
            $list[$key]['show_name'] = $this->getNickname($vo['nickname'], $vo['tel'], $vo['email']);
            $list[$key]['avatar'] = url('controller/member/avatar/index', ['id' => $vo['user_id']], true, false);
        }
        return $list;
    }

    public function countList($where = array()) {
        return $this->base($where)->count();
    }

    public function getWhereInfo($where) {
        $info = $this->base($where)->find();
        if ($info) {
            $info['show_name'] = $this->getNickname($info['nickname'], $info['tel'], $info['email']);
            $info['avatar'] = url('controller/member/avatar/index', ['id' => $info['user_id']], true, false);
        }
        return $info;
    }


    protected function _saveAfter($type, $data) {
        if ($type == 'edit') {
            return true;
        }
        if (!target('member/PayAccount')->add(['user_id' => $data['user_id']])) {
            return false;
        }
        if (!target('member/PointsAccount')->add(['user_id' => $data['user_id']])) {
            return false;
        }
        return true;
    }

    protected function _delBefore($id) {
        target('member/MemberUser')->beginTransaction();
        return true;
    }

    protected function _delAfter($id) {
        $hookList = run('service', 'member', 'del', ['user_id' => $id]);
        foreach ($hookList as $app => $vo) {
            if (!$vo) {
                target('member/MemberUser')->rollBack();
                $this->error = target($app . '/Member', 'service')->getError();
                return false;
            }
        }
        if (!target('member/PointsAccount')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        if (!target('member/PayAccount')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        if (!target('member/MemberConnect')->where(['user_id' => $id])->delete()) {
            target('member/MemberUser')->rollBack();
            return false;
        }
        target('member/MemberUser')->commit();
        return true;
    }

    /**
     * 获取当前用户ID
     */
    public function getUid() {
        $login = \dux\Dux::cookie()->get('user_login');
        if(!target('member/MemberUser')->checkUser($login['uid'], $login['token'])) {
            return false;
        }
        return $login['uid'];
    }

    /**
     * 获取账户信息
     * @param $uid
     * @return array|bool
     */
    public function getUser($uid) {
        $info = $this->getInfo($uid);
        if (empty($info)) {
            $this->error = '用户不存在！';
            return false;
        }
        $infoData = [
            'user_id' => $info['user_id'],
            'tel' => $info['tel'],
            'email' => $info['email'],
            'nickname' => $info['nickname'],
            'show_name' => $info['show_name'],
            'avatar' => $info['avatar'],
            'province' => $info['province'],
            'city' => $info['city'],
            'region' => $info['region'],
            'reg_time' => $info['reg_time'],
            'login_time' => $info['login_time'],
            'login_ip' => $info['login_ip'],
            'sex' => $info['sex'],
            'birthday' => $info['birthday'] ? date('Y-m-d', $info['birthday']) : '',
            'role_name' => $info['role_name'],
			'work' => $info['work'],
			'income' => $info['income'],
            'hometown_province' => $info['hometown_province'],
            'hometown_city' => $info['hometown_city'],
            'hometown_region' => $info['hometown_region'],
			'age' => $info['age'],
			'marry' => $info['marry'],
			'hobby' => $info['hobby'] 
        ];
        $infoData['avatar'] = $this->getAvatar($uid, 64);
		
		$accountInfo = target('member/PayAccount')->getWhereInfo(['A.user_id' => $uid]);
        if (empty($accountInfo)) {
            $this->error = '资金账户不存在!';
            return false;
        }
        $realInfo = target('member/MemberReal')->getWhereInfo([
            'A.user_id' => $uid,
            'A.status' => 2
        ]);
        $infoData['real_status'] = $realInfo ? true : false;
        $infoData['finance_account_id'] = $accountInfo['account_id'];
        $infoData['money'] = $accountInfo['money'];
        $infoData['money_spend'] = $accountInfo['spend'];
        $infoData['money_charge'] = $accountInfo['charge'];
        $infoData['money_recharge'] = $accountInfo['recharge'];
		
        return $infoData;
    }

    /**
     * 验证用户登录
     * @param $uid
     * @param $token
     * @return bool
     */
    public function checkUser($uid = '', $token = '') {
        if (empty($uid) || empty($token)) {
            $this->error = '帐号登录失效!';
            return false;
        }
        $info = target('member/MemberUser')->getWhereInfo([
            'user_id' => $uid
        ]);
        if (empty($info)) {
            $this->error = '用户不存在!';
            return false;
        }
        $config = \dux\Config::get('dux.use');
        $verify = sha1($info['password'] . $config['safe_key']);
        if ($token <> $verify) {
            $this->error = '登录验证失败,请重新登录!';
            return false;
        }
        return true;
    }

    /**
     * 获取用户昵称
     * @param $nickname
     * @param $tel
     * @param $email
     * @return mixed
     */
    public function getNickname($nickname, $tel, $email) {
        if ($nickname) {
            return $nickname;
        }
        if ($tel) {
            return $tel;
        }
        if ($email) {
            return $email;
        }
    }

    public function getAvatar($id, $type = 0) {
        switch ($type) {
            case 1:
                $type = 32;
                break;
            case 3:
                $type = 128;
                break;
            case 2:
            default:
                $type = 64;
                break;

        }
        $avatar = ROOT_PATH . 'upload/avatar/' . $id . '/'. $type . '.jpg';
        if(!is_file($avatar)) {
            $avatar = DOMAIN_HTTP . '/' . ROOT_URL . 'public/member/images/avatar.jpg';
        }else {
            $avatar = DOMAIN_HTTP . '/' . ROOT_URL . 'upload/avatar/' . $id . '/'. $type . '.jpg';
        }
        return $avatar;
    }

    /**
     * 生成用户头像
     * @param $userId
     * @param $image
     * @return bool
     * @throws \Exception
     */
    public function avatarUser($userId, $image) {
        $url = 'upload/avatar/' . $userId . '/';
        $dir = ROOT_PATH . $url;
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true)) {
                $this->error = '头像目录没有写入权限!';
                return false;
            }
        }
        $parse = explode('.', $image);
        $ext = strtolower(end($parse));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'bmp'])) {
            $ext = 'jpg';
        }
        if (strpos($image, 'http') !== false || strpos($image, 'https') !== false) {
            $data = \dux\lib\Http::doGet($image);
            if (empty($data)) {
                $this->error = '头像文件获取失败!';
                return false;
            }
            $image = $dir . 'cache.' . $ext;
            if (!file_put_contents($image, $data)) {
                $this->error = '头像文件无法写入!';
                return false;
            }
        }else {
            $image = realpath(ROOT_PATH . $image);
        }
        $image = new \dux\lib\Image($image);
        $image->thumb(128, 128, 'center');
        if (!$image->output($dir . '128.jpg', 'jpg')) {
            $this->error = '头像保存失败!';
            return false;
        }
        $image->thumb(64, 64, 'center');
        if (!$image->output($dir . '64.jpg', 'jpg')) {
            $this->error = '头像保存失败!';
            return false;
        }
        $image->thumb(32, 32, 'center');
        if (!$image->output($dir . '32.jpg', 'jpg')) {
            $this->error = '头像保存失败!';
            return false;
        }
        $status = target('member/MemberUser')->edit([
            'user_id' => $userId,
            'avatar' => ROOT_URL . '/' . $url . '128.jpg'
        ]);
        if (empty($status)) {
            $this->error = '头像保存失败!';
            return false;
        }
        return true;
    }


    /**
     * 获取记录接口
     * @return array
     */
    public function typeList() {
        $list = hook('service', 'Type', 'PayLog');
        $data = [];
        foreach ($list as $value) {
            $data = array_merge_recursive((array)$data, (array)$value);
        }
        return $data;
    }

}