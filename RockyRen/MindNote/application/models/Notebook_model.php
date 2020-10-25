<?php


class Notebook_model extends CI_Model
{
  function __construct()
  {
    parent::__construct();
    $this->CI = & get_instance();
    $this->load->library('session');
  }


  /**
   * 通过id获得某个笔记本组里的某个笔记本
   */
  public function get_notebook($user_id, $notebook_id)
  {
    $res = $this->CI->db->where( array('creator_id' => $user_id, 'id' => $notebook_id) )
        ->get('notebook');

    //返回数组,数组中是一个一个的数组
    $result = $res->result_array();

    //查找结果不为空
    if( !empty($result) )
    {
      //返回数组中的第一个元素，该元素为一个数组
      $row = $res->row_array();
      return $row;
    }

    //如果找不到对应笔记本,则返回null
    return null;
  }



  /**
   * 获得某个笔记本组里的所有笔记本
   */
  public function get_notebooks($user_id, $group_id)
  {
    //在表site_notebook中查找某个笔记本组里的所有笔记本
    if($group_id === false){
      $res = $this->CI->db->where( array('creator_id' => $user_id))->get('notebook');

    }else{
      $res = $this->CI->db->where( array('creator_id' => $user_id, 'group_id' => $group_id) )->get('notebook');

    }

    //返回数组,数组中是一个一个的数组
    $result = $res->result_array();

    //查找结果不为空
    if( !empty($result) )
    {
       return $result;
    }

    //如果找不到对应笔记本,则返回null
    return null;
  }


  /**
   * 修改一个笔记本的笔记本
   */
  public function change_notebook($user_id, $notebook_id, $updateData)
  {
    $updateData['updated_at'] = date('Y-m-d H:i:s', time());

    $this->CI->db->where(array('creator_id' => $user_id, 'id' => $notebook_id));

    $bool = $this->CI->db->update('notebook', $updateData);

    //返回修改是否成功
    return $bool;
  }

//  /**
//   * 修改一个笔记本为默认笔记本
//   */
//  public function change_default_notebook($user_id, $notebook_id, $default)
//  {
//    $date = date('Y-m-d H:i:s', time());
//
//    $bool = false;
//    if($default == "true")
//    {
//      $bool = true;
//    }
//    else
//    {
//      return false;
//    }
//
//    //$data表示要修改的属性
//    $data = array(
//      'updated_at' => $date,
//      'default' => $bool
//    );
//
//    /*
//     * 将原默认笔记本的default属性改为false
//     */
//    $this->CI->db->where(array('creator_id' => $user_id, 'default' => true));
//    //update上一条语句查找到的记录
//    $this->CI->db->update('notebook', array('default' => false));
//
//    /*
//     * 更新当前笔记本的date属性和default属性（true）
//     */
//    $this->CI->db->where(array('creator_id' => $user_id, 'id' => $notebook_id));
//    //update上一条语句查找到的记录
//    $bool = $this->CI->db->update('notebook', $data);
//
//    //返回修改是否成功
//    return $bool;
//  }

  /**
   * 添加一个笔记本
   */
  public function add_notebook($user_id, $group_id, $notebook_name)
  {
    $date = date('Y-m-d H:i:s', time());
    $data = array(
      'name' => $notebook_name,
      'creator_id' => $user_id,
      'created_at' => $date,
      'updated_at' => $date,
      'group_id' => $group_id
    );

    //在表site_notebook中插入一个新笔记本($data)
    $this->CI->db->insert('notebook', $data);

    //返回一个插入后的笔记本的id
    return $this->CI->db->insert_id();
  }

  /**
   * 添加一个默认笔记本
   */
  public function add_default_notebook($user_id)
  {
    $date = date('Y-m-d H:i:s', time());
    $data = array(
      'name' => '默认笔记本',
      'creator_id' => $user_id,
      'created_at' => $date,
      'updated_at' => $date,
      'group_id' => null,
      'default' => true
    );

    //在表site_notebook中插入一个新笔记本($data)
    $this->CI->db->insert('notebook', $data);

    $default_notebook_id = $this->CI->db->insert_id();

    //返回插入是否成功
    return $default_notebook_id;
  }


  /**
   * 删除一个笔记本
   */
  public function delete_notebook($user_id, $notebook_id)
  {
    $notebook = $this->get_notebook($user_id, $notebook_id);
    if($notebook['default'] == 1)
      return false;
    //在表site_notebook中删除id为$id的数据
    $bool = $this->CI->db->delete('notebook', array('creator_id' => $user_id, 'id' => $notebook_id, 'group_id' => $group_id));

    //返回删除是否成功
    return $bool;
  }


  /**
   * 获取默认笔记本的id
   */
  public function get_default_notebook_id()
  {
    $user_id = $this->session->userdata('userId');
    $res = $this->CI->db->where( array('creator_id' => $user_id, 'default' => true ) )
      ->get('notebook');

    //返回数组,数组中是一个一个的对象
    $result = $res->result();

    //查找结果不为空
    if( !empty($result) )
    {
      $row = $res->row_array();

      return $row['id'];
    }

    //如果找不到对应笔记本,则返回null
    return null;
  }

  private $CI;
//  private $default_notebook_id;

}