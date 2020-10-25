<?php defined('BASEPATH') OR exit('No direct script access allowed');

class user_model extends MY_Model {
    const TB_USER = CIPLUS_DB_PREFIX . 'user';
    const TB_USER_INFO = CIPLUS_DB_PREFIX . 'user_info';

    public function __construct() {
        parent::__construct();
    }

    /**
     * 验证账号
     * @param $account
     * @param $password
     * @return int|null
     */
    public function verify_account($account, $password) {
        $this->db->where('usable', 1);
        $this->db->where('account', $account);
        $user = $this->row(self::TB_USER);
        return $this->compare_password($user, $password);
    }

    /**
     * 验证email
     * @param $email
     * @param $password
     * @return int|null
     */
    public function verify_email($email, $password) {
        $this->db->where('usable', 1);
        $this->db->where('email', $email);
        $user = $this->row(self::TB_USER);
        return $this->compare_password($user, $password);
    }

    /**
     * 验证手机号
     * @param $phone
     * @param $password
     * @return int|null
     */
    public function verify_phone($phone, $password) {
        $this->db->where('usable', 1);
        $this->db->where('phone', $phone);
        $user = $this->row(self::TB_USER);
        return $this->compare_password($user, $password);
    }

    /**
     * 比对密码
     * @param $user
     * @param $password
     * @return int|null
     */
    private function compare_password($user, $password) {
        if ($this->decrypt($user['password']) === $password) {
            return $user['id'];
        }
        return null;
    }

    /**
     * 添加用户账号
     * @param $account
     * @param $email
     * @param $phone
     * @param $password
     * @return mixed
     */
    public function add($account, $email, $phone, $password) {
        $data = array(
            'account' => $account,
            'email' => $email,
            'phone' => $phone,
            'password' => $this->encrypt($password),
            'create_time' => time()
        );
        $re = $this->insert(self::TB_USER, $data);
        return $re;
    }

    /**
     * 获取用户相关信息
     * @param $id
     * @return bool|mixed
     */
    public function getInfo($id) {
        if (!empty($id)) {
            $this->db
                ->select('name,avatar,introduction,sex,area,city,province,country')
                ->where('id', $id);
            return $this->row(self::TB_USER_INFO);
        }
        return [];
    }

    /**
     * 设置用户相关信息
     * @param $id
     * @param $info
     * @return mixed
     */
    public function setInfo($id, $info) {
        $exist = $this->getInfo($id);
        $data = empty($exist) ? [] : $exist;
        $data['name'] = key_exists('name', $info) ? $info['name'] : '';
        $data['avatar'] = key_exists('avatar', $info) ? $info['avatar'] : '';
        $data['introduction'] = key_exists('introduction', $info) ? $info['introduction'] : '';
        $data['sex'] = key_exists('sex', $info) ? $info['sex'] : 2;
        $data['area'] = key_exists('area', $info) ? $info['area'] : '';
        $data['city'] = key_exists('city', $info) ? $info['city'] : '';
        $data['province'] = key_exists('province', $info) ? $info['province'] : '';
        $data['country'] = key_exists('country', $info) ? $info['country'] : '';
        if (empty($exist)) {
            $data['id'] = $id;
            return $this->insert(self::TB_USER_INFO, $data);
        } else {
            return $this->update(self::TB_USER_INFO, $data, ['id' => $id]);
        }
    }

    /**
     * 修改密码
     * @param $id
     * @param $old_password
     * @param $new_password
     * @return bool|mixed
     */
    public function changePassword($id, $old_password, $new_password) {
        $this->db->where('id', $id);
        $m = $this->row(self::TB_USER);
        if (!empty($m) && $this->decrypt($m['password']) === $old_password) {
            return $this->update(self::TB_USER, ['password' => $this->encrypt($new_password)], ['id' => $id]);
        }
        return false;
    }

    /**
     * 对成员密码二次加密
     * @param $password
     * @return string
     */
    private function encrypt($password) {
        $this->load->library('encryption');
        return $this->encryption->encrypt($password);
    }

    /**
     * 对成员密码二次解密
     * @param $password
     * @return string
     */
    private function decrypt($password) {
        $this->load->library('encryption');
        return $this->encryption->decrypt($password);
    }
}