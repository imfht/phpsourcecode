<?php

/**
 * 消息处理
 */
class Msg_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 获取1条发送的消息详情
     * @param  int $id 消息id
     * @return array   消息详情,没有则返回0
     */
    public function get($id)
    {
        $query = $this->db->select('A.*, B.nickname')
            ->from('msg_tb A')
            ->join('user_tb B', 'A.receiver_user_id = B.id', 'inner')
            ->where('A.id', $id)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 获取1条接收的消息详情
     * @param  int $id 消息id
     * @return array   消息详情,没有则返回0
     */
    public function get_to_me($id)
    {
        $query = $this->db->select('A.*, B.nickname')
            ->from('msg_tb A')
            ->join('user_tb B', 'A.sender_user_id = B.id', 'inner')
            ->where('A.id', $id)
            ->limit(1)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return 0;
    }

    /**
     * 发送的消息记录
     * @param  int $user_id    发信人id
     * @param  int $page_index 页码,从1开始
     * @param  int $page_size  每页显示数量
     * @return array           发送的消息记录,没有则返回0
     */
    public function gets_from_me($user_id, $page_index, $page_size)
    {
        $start = ($page_index - 1) * $page_size;
        $query = $this->db->select('A.*, B.nickname')
            ->from('msg_tb A')
            ->join('user_tb B', 'A.receiver_user_id = B.id', 'inner')
            ->where('sender_user_id', $user_id)
            ->order_by('id', 'DESC')
            ->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 发送的消息记录总数
     * @param  int $user_id 发信人id
     * @return int          发送的消息记录总数
     */
    public function gets_from_me_count($user_id)
    {
        return $this->db->select('id')
            ->from('msg_tb')
            ->where('sender_user_id', $user_id)
            ->count_all_results();
    }

    /**
     * 收到的消息记录
     * @param  int $user_id    收信人id
     * @param  int $page_index 页码,从1开始
     * @param  int $page_size  每页显示数量
     * @param  int $msg_status 消息类型,0=所有,1=未读,2=已读
     * @return array           收到的消息记录,没有则返回0
     */
    public function gets_to_me($user_id, $page_index, $page_size, $msg_status = 0)
    {
        $start = ($page_index - 1) * $page_size;
        $where['receiver_user_id'] = $user_id;
        if ($msg_status != 0) {
            $where['msg_status'] = $msg_status;
        }
        $query = $this->db->select('A.*, B.nickname')
            ->from('msg_tb A')
            ->join('user_tb B', 'A.sender_user_id = B.id', 'inner')
            ->where($where)
            ->order_by('id', 'DESC')
            ->limit($page_size, $start)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return 0;
    }

    /**
     * 收到的消息记录总数
     * @param  int $user_id 发信人id
     * @param  int $msg_status 消息类型,0=所有,1=未读,2=已读
     * @return int          收到的消息记录总数
     */
    public function gets_to_me_count($user_id, $msg_status = 0)
    {
        $where['receiver_user_id'] = $user_id;
        if ($msg_status != 0) {
            $where['msg_status'] = $msg_status;
        }
        return $this->db->select('id')
            ->from('msg_tb')
            ->where($where)
            ->count_all_results();
    }

    /**
     * 添加消息
     * @param array $msg 消息
     */
    public function add($msg)
    {
        if ($this->db->insert('msg_tb', $msg)) {
            $id = $this->db->insert_id();
            return $this->get($id);
        }
        return 0;
    }

    /**
     * 更新消息
     * @param  array $msg 消息
     * @return bool       成功返回true,失败返回false
     */
    public function update($msg)
    {
        $where = array(
            'id' => $msg['id'],
        );
        return $this->db->update('msg_tb', $msg, $where);
    }

    /**
     * 删除消息
     */
    public function del($id)
    {
        $where = array(
            'id' => $id,
        );
        return $this->db->delete('msg_tb', $where);
    }

    /**
     * 将消息状态设置为已读
     */
    public function view_msg_by_range($receiver_user_id, $min, $max)
    {
        $where = array(
            'receiver_user_id' => $receiver_user_id,
            'id >=' => $min,
            'id <=' => $max,
            'msg_status' => 1,
        );
        $this->db->set('msg_status', 2);
        $this->db->where($where);
        return $this->db->update('msg_tb');
    }
}
