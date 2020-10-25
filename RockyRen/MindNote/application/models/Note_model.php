<?php


class Note_model extends CI_Model
{
  function __construct()
  {
    parent::__construct();
    $this->CI = & get_instance();
    $this->CI->load->model('Notebook_model', 'notebook');
  }


  /**
   * 通过id获得某个笔记本里的某个笔记
   */
  public function get_note($user_id, $note_id)
  {
    $res = $this->CI->db->where( array('creator_id' => $user_id, 'id' => $note_id) )
      ->get('note');

    //返回数组,数组中是一个一个的数组
    $result = $res->result_array();

    //查找结果不为空
    if( !empty($result) )
    {
      //返回数组中的第一个元素，该元素为一个数组
      $row = $res->row_array();
      if($row['type'] == 'map')
      {
        $row['content']=unserialize($row['content']);
      }
      return $row;
    }

    //如果找不到对应笔记,则返回null
    return null;
  }


  /**
   * 获得某个笔记本组里某个笔记本的所有笔记
   * @params $user_id: 用户id
   * @params $group_id: 笔记本组id,当指定该id时，需要查找notebook表
   * @params $notebook_id: 笔记本id
   */
  public function get_notes($user_id, $group_id, $notebook_id)
  {
    //如果指定了$notebook_id，则只需根据notebook_id在note表中在找到对应笔记
    if($notebook_id)
    {
      $this->CI->db->where( array('creator_id' => $user_id, 'notebook_id' => $notebook_id));
      $this->CI->db->order_by('updated_at', 'DESC');
      $res = $this->CI->db->get('note');
    }
    else
    {
      $this->CI->db->
        select('note.id,note.name,note.creator_id,note.created_at,note.updated_at,note.notebook_id,note.content,note.type');
      $this->CI->db->from('note');
      $this->CI->db->join('notebook', 'notebook.id = note.notebook_id');

      $this->CI->db->where('note.creator_id', $user_id);

      if($group_id)
      {
        $this->CI->db->where('notebook.group_id', $group_id);
      }

      $this->CI->db->order_by('updated_at', 'DESC');
      $res = $this->CI->db->get();
    }

    $result = $res->result_array();
    foreach($result as &$row)
    {
      if($row['type'] == 'map')
      {
        $row['content'] = unserialize($row['content']);
      }
    }

    return $result;
  }


  /**
   * 添加一个笔记本
   */
  public function add_note($user_id, $notebook_id, $name, $content, $type)
  {
    if(!$notebook_id )
    {
      $notebook_id = $this->CI->notebook->get_default_notebook_id();
    }
    if(!$type){
      $type = 'note';
      $content = stripslashes($content);
    }else if($type == 'note'){
      $content = stripslashes($content);
    }else if($type == 'map')
    {
      $type = 'map';
      $content = serialize($content);;
    }

    $date = date('Y-m-d H:i:s', time());
    $data = array(
      'name' => $name ,
      'content' => $content,
      'type' => $type,
      'creator_id' => $user_id ,
      'created_at' => $date,
      'updated_at' => $date,
      'notebook_id' => $notebook_id
    );

    //在表site_note中插入一条数据
    $this->CI->db->insert('note', $data);

    //返回插入是否成功
    return $this->CI->db->insert_id();;
  }



  /**
   * 修改一个笔记
   */
  public function change_note($user_id, $note_id, $updateData)
  {
    $updateData['updated_at'] = date('Y-m-d H:i:s', time());


    //在表site_note中修改id为$id的数据
    $this->CI->db->where(array('creator_id' => $user_id, 'id' => $note_id));
    $bool = $this->CI->db->update('note', $updateData);

    //返回修改是否成功
    return $bool;
  }

  /**
   * 删除一个笔记
   */
  public function delete_note($user_id, $note_id)
  {
    //在表site_note中删除id为$id的数据
    $bool = $this->CI->db->delete('note', array('creator_id' => $user_id, 'id' => $note_id));

    //返回删除是否成功
    return $bool;
  }


  private $CI;

}


















