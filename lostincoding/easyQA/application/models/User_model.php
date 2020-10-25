<?php

/**
 * 用户处理
 */
class User_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据id获取用户信息
     * @param  int   $id       用户id
     * @param  bool  $show_pwd 是否返回pwd
     * @return array           用户信息,没有结果返回0
     */
    public function get($id, $show_pwd = false)
    {
        $pwd = '';
        if ($show_pwd) {
            $pwd = ', pwd';
        }
        $query = $this->db->select('id, email' . $pwd . ', mobile, nickname, avatar_ext, gender, country, province, city, birthday, sexual, relationship_status, blood_type, profession, blog, brief, verify_type, verify_details, verify_time, email_status, mobile_status, freeze_status, freeze_time, signup_time, signin_time, signin_count, points')
            ->from('user_tb')
            ->where('id', $id)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 根据nickname获取用户信息
     * @param  string $nickname 用户昵称
     * @return array            用户信息,没有结果返回0
     */
    public function get_by_nickname($nickname)
    {
        $query = $this->db->select('id, email, mobile, nickname, avatar_ext, gender, country, province, city, birthday, sexual, relationship_status, blood_type, profession, blog, brief, verify_type, verify_details, verify_time, email_status, mobile_status, freeze_status, freeze_time, signup_time, signin_time, signin_count, points')
            ->from('user_tb')
            ->where('nickname', $nickname)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 根据email获取用户信息
     * @param  string $email 用户email
     * @return array         用户信息,没有结果返回0
     */
    public function get_by_email($email)
    {
        $query = $this->db->select('id, email, mobile, nickname, avatar_ext, gender, country, province, city, birthday, sexual, relationship_status, blood_type, profession, blog, brief, verify_type, verify_details, verify_time, email_status, mobile_status, freeze_status, freeze_time, signup_time, signin_time, signin_count, points')
            ->from('user_tb')
            ->where('email', $email)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * @param  int   $email  用户注册的email
     * @param  int   $pwd    用户密码
     * @return array         用户信息,没有结果返回0
     */
    public function get_by_email_and_pwd($email, $pwd)
    {
        $where = array(
            'email' => $email,
            'pwd' => $pwd,
        );
        $query = $this->db->select('id, email, mobile, nickname, avatar_ext, gender, country, province, city, birthday, sexual, relationship_status, blood_type, profession, blog, brief, verify_type, verify_details, verify_time, email_status, mobile_status, freeze_status, freeze_time, signup_time, signin_time, signin_count, points')
            ->from('user_tb')
            ->where($where)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 添加用户
     * @param  array $user 待添加用户
     * @return array       新添加的用户信息,添加失败返回0
     */
    public function add($user)
    {
        if ($this->db->insert('user_tb', $user)) {
            $id = $this->db->insert_id();
            return $this->get($id);
        }
        return 0;
    }

    /**
     * 更新用户
     * @param  array $user 待更新用户
     * @return array $user 更新后的用户信息,更新失败返回0
     */
    public function update($user)
    {
        $where = array('id' => $user['id']);
        if ($this->db->update('user_tb', $user, $where)) {
            return $this->get($user['id']);
        }
        return 0;
    }

    /**
     * 最新用户列表
     */
    public function gets_by_latest($page_index, $page_size)
    {
        $start = ($page_index - 1) * $page_size;
        $query = $query = $this->db->select('id, email, mobile, nickname, avatar_ext, gender, country, province, city, birthday, sexual, relationship_status, blood_type, profession, blog, brief, verify_type, verify_details, verify_time, email_status, mobile_status, freeze_status, freeze_time, signup_time, signin_time, signin_count, points')
            ->from('user_tb')
            ->order_by('id', 'DESC')
            ->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 用户总数
     */
    public function get_counts()
    {
        return $this->db->select('id')
            ->from('user_tb')
            ->count_all_results();
    }

    /**
     * 增加用户积分
     */
    public function add_points($user_id, $points)
    {
        $this->db->set('points', "points + $points", false);
        $this->db->where('id', $user_id);
        return $this->db->update('user_tb');
    }

    /**
     * 减少用户积分
     */
    public function reduce_points($user_id, $points)
    {
        $this->db->set('points', "points - $points", false);
        $this->db->where('id', $user_id);
        return $this->db->update('user_tb');
    }

}
