<?php


class Group_model extends CI_Model
{
  function __construct()
  {
    parent::__construct();
    $this->CI = & get_instance();
  }


  /**
   * 通过id获得某个笔记本组
   */
  public function get_group($user_id, $group_id)
  {
    //在表site_group中查找id对应相同的结果
    $res = $this->CI->db->where( array('creator_id' => $user_id, 'id' => $group_id) )
      ->get('group');

    //返回数组,数组中是一个一个的数组
    $result = $res->result_array();

    //查找结果不为空
    if( !empty($result) )
    {
      //返回数组中的第一个元素，该元素为一个数组
      $row = $res->row_array();
      return $row;
    }

    //如果找不到对应笔记本组,则返回null
    return null;
  }


  /**
   * 获得所有的笔记本组
   */
  public function get_groups($user_id)
  {
    //在表site_group中查找所有笔记本组
    $res = $this->CI->db->where( array('creator_id' => $user_id) )->get('group');

    //返回数组,数组中是一个一个的数组
    $result = $res->result_array();

    //查找结果不为空
    if( !empty($result) )
    {
      return $result;
    }

    //如果找不到对应笔记本组,则返回null
    return null;
  }


  /**
   * 添加一个笔记本组
   */
  public function add_group($user_id, $group_name)
  {
    $date = date('Y-m-d H:i:s', time());
    $data = array(
      'name' => $group_name ,
      'creator_id' => $user_id ,
      'created_at' => $date,
      'updated_at' => $date
    );

    //在表site_group中插入一条数据
    $this->CI->db->insert('group', $data);

    //返回插入是否成功
    return $this->CI->db->insert_id();
  }


  /**
   * 删除一个笔记本组，会级联的删除其下的笔记本和笔记
   */
  public function delete_group($user_id, $group_id)
  {
    //在表site_group中删除id为$id的数据
    $bool = $this->CI->db->delete('group', array('creator_id' => $user_id, 'id' => $group_id));

    //返回删除是否成功
    return $bool;
  }


  /**
   * 修改一个笔记本组
   */
  public function change_group($user_id, $group_id, $group_name)
  {

    $date = date('Y-m-d H:i:s', time());

    //$data表示要修改的属性
    $data = array(
      'name' => $group_name,
      'updated_at' => $date,
    );

    //在表site_group中修改id为$id的数据
    $this->db->where(array('creator_id' => $user_id, 'id' => $group_id));
    $bool = $this->CI->db->update('group', $data);

    //返回修改是否成功
    return $bool;
  }

  /**
   * 查看笔记本组中是否有默认笔记本
   */
  public function check_default_notebook_in_group($user_id, $group_id){
    $this->CI->db->where(array('creator_id' => $user_id, 'group_id' => $group_id, 'default' => 1));
    $res = $this->CI->db->get('notebook')->result();


    if(empty($res)){
      return false;
    }
    //如果有默认笔记本，则返回true
    else{
      return true;
    }
  }

  private $CI;
}