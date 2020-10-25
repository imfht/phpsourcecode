<?php

/**
 * Github登录用户处理
 */
class GithubUser_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 根据openid获取用户信息
     * @param  string $openid 用户openid
     * @return array          用户信息,没有结果返回0
     */
    public function get_by_openid($openid)
    {
        $query = $this->db->select('*')
            ->from('github_user_tb')
            ->where('openid', $openid)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 根据user_id获取用户信息
     * @param  string $user_id 用户user_id
     * @return array           用户信息,没有结果返回0
     */
    public function get_by_userId($user_id)
    {
        $query = $this->db->select('*')
            ->from('github_user_tb')
            ->where('user_id', $user_id)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 添加用户
     * @param  array $open_user 待添加的用户
     * @return array            新添加的用户信息,添加失败返回0
     */
    public function add($open_user)
    {
        if ($this->db->insert('github_user_tb', $open_user)) {
            return $this->get_by_openid($open_user['openid']);
        }
        return 0;
    }

    /**
     * 更新用户
     * @param  array $open_user 待更新用户
     * @return array $open_user 更新后的用户信息,更新失败返回0
     */
    public function update($open_user)
    {
        $where = array('openid' => $open_user['openid']);
        if ($this->db->update('github_user_tb', $open_user, $where)) {
            return $this->get_by_openid($open_user['openid']);
        }
        return 0;
    }

    /**
     * 根据用户id删除绑定账号
     * @param  int $id      用户id
     * @return bool         成功返回true,失败返回false
     */
    public function del_by_userId($user_id)
    {
        $where = array(
            'user_id' => $user_id,
        );
        return $this->db->delete('github_user_tb', $where);
    }
}
