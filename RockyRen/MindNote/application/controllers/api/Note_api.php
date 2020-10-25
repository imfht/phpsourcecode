<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';

class Note_api extends REST_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->database();
    //$this->load->database('testing');

    $this->load->library('session');
    $this->load->library('check_data');

    //获取参数
    $this->user_id = $this->session->userdata('userId');
  }

  /**
   * 获得某个笔记本里的一篇笔记
   *
   * 返回的json格式如下
   * 查找成功时
   *    {
   *      id:"笔记id",
   *      name: "笔记名",
   *      creator_id:"用户id",
   *      created_at:"笔记创建时间",
   *      updated_at:"更新时间"
   *      notebook_id:"从属笔记本id"
   *      content:"笔记内容"
   *    }
   * 查找失败时
   *    {
   *      status:"fail",
   *      message:"错误信息"
   *    }
   *
   */
  function note_get()
  {
    //获取参数
    $note_id = $this->get('note_id');

    if(!$this->check_data->check_need_data(array($this->user_id, $note_id)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }

    //加载模型
    $this->load->model('Note_model', 'note');

    //$note值为关于某个笔记本组里某个笔记本的某个笔记
    $note = $this->note->get_note($this->user_id, $note_id);

    if($note)
    {
      //查找成功
      return $this->response($note, 200); // 200 being the HTTP response code
    }
    else
    {
      //查找失败
      return $this->response(array('status' => 'fail', 'message' => '不能找到该笔记'), 404);
    }
  }

  /**
   * 获得某个笔记本里的所有笔记
   *
   * 返回的json格式如下
   * 查找成功时
   * [
   *    {
   *      id:"笔记id",
   *      name: "笔记名",
   *      creator_id:"用户id",
   *      created_at:"笔记创建时间",
   *      updated_at:"更新时间"
   *      notebook_id:"从属笔记本id"
   *      content:"笔记内容"
   *    }
   * ]
   * 查找失败时
   *    {
   *      status:"fail",
   *      message:"错误信息"
   *    }
   *
   */
  function notes_get()
  {
    //获取参数
    $group_id = $this->get('group_id');
    $notebook_id = $this->get('notebook_id');

    if(!$this->check_data->check_need_data(array($this->user_id)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }

    //加载模型
    $this->load->model('Note_model', 'note');

    $notes = $this->note->get_notes($this->user_id, $group_id, $notebook_id);

    if(is_array($notes))
    {
      return $this->response($notes, 200);
    }
    else
    {
      return $this->response(array('status' => 'fail', 'message' => '没有找到笔记'), 404);
    }

  }

  /**
   * 在某个笔记本里添加一篇笔记
   *
   * 请求的json格式如下
   *    {
   *      name: "笔记名"(必需)
   *      content: "笔记本内容"(可选)
   *      notebook_id:"笔记本id"(可选)
   *      type: 'note' | 'map',  默认为'note'
   *    }
   *
   * 返回的json格式如下
   * 添加成功时
   *    {
   *      id:"笔记id",
   *      name: "笔记名",
   *      creator_id:"用户id",
   *      created_at:"笔记创建时间",
   *      updated_at:"更新时间"
   *      notebook_id:"从属笔记本id"
   *      content:"笔记内容"
   *    }
   * 添加失败时
   *    {
   *      status:"fail",
   *      message:"错误信息"
   *    }
   *
   */
  function note_post()
  {

    //获取参数
    $name = $this->post('name');
    //字符串反引用
    //$content = stripslashes($this->post('content'));
    $content = $this->post('content');

    //从post中取得的notebook_id，为空时将笔记本的notebook_id设为null
    $addin_notebook_id = $this->post('notebook_id');

    $type = $this->post('type');

    if( !$this->check_data->check_need_data(array($this->user_id, $name)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }

    //加载模型
    $this->load->model('Note_model', 'note');

    //$bool为布尔值，返回增加笔记是否成功
    $new_note_id = $this->note->add_note($this->user_id , $addin_notebook_id, $name, $content, $type);

    if($new_note_id)
    {
      //获取添加成功的笔记
      $new_note = $this->note->get_note($this->user_id, $new_note_id);
      //增加成功
      return $this->response($new_note, 200); // 200 being the HTTP response code
    }
    else
    {
      //增加失败
      return $this->response(array('status' => 'fail', 'message' => '增加失败'), 404);
    }
  }

  /**
   * 修改某个笔记本里的一篇笔记
   *
   * 请求的json格式如下
   *    {
   *      name: "笔记名",(可选)
   *      content:"笔记内容"(可选)
   *      notebook_id:"笔记本id"(可选，但三者必需有一个)
   *      type: 'note' | 'map', 若为'map'，则序列化content;不指定或为'note'时使用字符串反引用
   *    }
   *
   * 返回的json格式如下
   * 修改成功时
   *    {
   *      id:"笔记id",
   *      name: "笔记名",
   *      creator_id:"用户id",
   *      created_at:"笔记创建时间",
   *      updated_at:"更新时间"
   *      notebook_id:"从属笔记本id"
   *      content:"笔记内容"
   *    }
   * 修改失败时
   *    {
   *      status:"fail",
   *      message:"错误信息"
   *    }
   *
   */
  function note_put()
  {
    //获取参数
    $note_id = $this->get('note_id');

    $name = $this->put('name');
    //字符串反引用
//    $content = stripslashes($this->put('content', false));
    $content = $this->put('content', false);

    $type = $this->put('type');
    $alter_notebook_id = $this->put('notebook_id');


    if(!$this->check_data->check_need_data(array($this->user_id, $note_id)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }

    if(!$this->check_data->check_optional_data(array($name, $content, $alter_notebook_id)))
    {
      return $this->response(array('status' => 'fail1', 'message' => '没有提供参数'), 400);
    }

    //加载模型
    $this->load->model('Note_model', 'note');

    $updateData = $this->makeUpdateData($name, $content, $alter_notebook_id, $type);


    //$bool为布尔值，返回修改笔记是否成功
    $bool = $this->note->change_note($this->user_id , $note_id, $updateData);

    if($bool)
    {
      //获取添加成功的笔记
      $note = $this->note->get_note($this->user_id, $note_id);
      //修改成功
      return $this->response($note, 200); // 200 being the HTTP response code
    }
    else
    {
      //修改失败
      return $this->response(array('status' => 'fail', 'message' => '修改失败'), 404);
    }
  }

  /**
   * 生成更新数据
   * @param $name
   * @param $content
   * @param $alter_notebook_id
   * @return array
   */

  private function makeUpdateData($name, $content, $alter_notebook_id, $type)
  {
    $updateData = array();
    if($name !== false)
    {
      $updateData['name'] = $name;
    }

    if($content !== false)
    {
      $updateData['content'] = $content;
    }else{
      $updateData['content'] = '';
    }

    if(!$type || $type == 'note'){
      $updateData['content'] = stripslashes($updateData['content']);
    }else if($type == 'map'){
      $updateData['content'] = serialize($updateData['content']);
    }

    if($alter_notebook_id !== false)
    {
      $updateData['notebook_id'] = $alter_notebook_id;
    }
    return $updateData;
  }

  /**
   * 删除某个笔记本里的一篇笔记
   *
   * 返回的json格式如下
   *
   * 删除成功时
   *    null
   * 删除失败时
   *    {
   *      status:"fail",
   *      message:"错误信息"
   *    }
   *
   */
  function note_delete()
  {
    //获取参数
    $note_id = $this->get('note_id');

    if(!$this->check_data->check_need_data(array($this->user_id, $note_id)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }

    //加载模型
    $this->load->model('Note_model', 'note');

    //$bool为布尔值，返回删除笔记是否成功
    $bool = $this->note->delete_note($this->user_id, $note_id);

    if($bool)
    {
      //删除成功
      return $this->response(null, 200); // 200 being the HTTP response code
    }
    else
    {
      //删除失败
      return $this->response(array('status' => 'fail', 'message' => '删除失败'), 404);
    }
  }

  private $user_id;

}