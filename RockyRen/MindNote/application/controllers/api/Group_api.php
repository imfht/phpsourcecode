<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';

class Group_api extends REST_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->database();
    //$this->load->database('testing');x

    $this->load->library('session');
    $this->load->library('check_data');

    //获取参数
    $this->user_id = $this->session->userdata('userId');

  }


  /**
   * 查找一个笔记本组
   *
   * 返回的json格式如下
   * 查找成功时
   *    {
   *      id:"笔记本组id",
   *      name: "笔记本组名",
   *      creator_id:"用户id",
   *      created_at:"笔记本组创建时间",
   *      updated_at:"更新时间"
   *    }
   * 查找失败时
   *    {
   *      status:"fail",
   *      message:"错误信息"
   *    }
   *
   */
  public function group_get()
  {
    //获取参数
    $group_id = $this->get('group_id');

    if(!$this->check_data->check_need_data(array($this->user_id, $group_id)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }

    //加载模型
    $this->load->model('Group_model', 'group');

    //$group值为关于某个笔记本组的数组，元素为笔记本组的各个属性，非数组
    $group = $this->group->get_group($this->user_id, $group_id);

    if($group)
    {
      //查找成功
      return $this->response($group, 200); // 200 being the HTTP response code
    }
    else
    {
      //查找失败
      return $this->response(array('status' => 'fail', 'message' => '不能找到该笔记本组'), 404);
    }

  }

  /**
   * 添加一个笔记本组
   *
   * 请求的json格式如下
   *    {
   *      name: "笔记本组名"
   *    }
   *
   * 返回的json格式如下
   * 添加成功时
   *    {
   *      id:"笔记本组id",
   *      name: "笔记本组名",
   *      creator_id:"用户id",
   *      created_at:"笔记本组创建时间",
   *      updated_at:"更新时间"
   *    }
   * 添加失败时
   *    {
   *      status:"fail",
   *      message:"错误信息"
   *    }
   */
  function group_post()
  {
    //获取参数
    $name = $this->post('name');

    if(!$this->check_data->check_need_data(array($this->user_id, $name)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }

    //加载模型
    $this->load->model('Group_model', 'group');

    //$bool为布尔值，返回增加笔记本组是否成功
    $new_group_id = $this->group->add_group($this->user_id, $name);

    if($new_group_id)
    {
      //获取添加成功的笔记本组(元组)
      $group = $this->group->get_group($this->user_id, $new_group_id);
      //增加成功
      return $this->response($group, 200); // 200 being the HTTP response code
    }
    else
    {
      //增加失败
      return $this->response(array('status' => 'fail', 'message' => '增加失败'), 404);
    }
  }

  /**
   * 修改一个笔记本组
   *
   * 请求的json格式如下
   *    {
   *      id: "笔记本组id"
   *      name: "笔记本组名"
   *    }
   *
   * 返回的json格式如下
   * 修改成功时
   *    {
   *      id:"笔记本组id",
   *      name: "笔记本组名",
   *      creator_id:"用户id",
   *      created_at:"笔记本组创建时间",
   *      updated_at:"更新时间"
   *    }
   * 修改失败时
   *    {
   *      status:"fail",
   *      message:"错误信息"
   *    }
   *
   */
  function group_put()
  {
    //获取参数
    $group_id = $this->get('group_id');
    $name = $this->put('name');

    if(!$this->check_data->check_need_data(array($this->user_id, $group_id, $name)))
    {
      return $this->response(array('status' => 'fail','message' => '获取参数失败'), 400);
    }

    //加载模型
    $this->load->model('Group_model', 'group');

    //$bool为布尔值，返回修改笔记本组是否成功
    $bool = $this->group->change_group($this->user_id, $group_id, $name);

    if($bool)
    {
      //获取添加成功的笔记本组(元组)
      $group = $this->group->get_group($this->user_id, $group_id);
      //修改成功
      return $this->response($group, 200); // 200 being the HTTP response code
    }
    else
    {
      //修改失败
      return $this->response(array('message' => '修改失败'), 404);
    }
  }

  /**
   * 删除一个笔记本组
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
  function group_delete()
  {
    //获取参数
    $group_id = $this->get('group_id');

    if(!$this->check_data->check_need_data(array($this->user_id, $group_id)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 404);
    }




    //加载模型
    $this->load->model('Group_model', 'group');


    //如果笔记本组中有默认笔记本，则不能删除
    if($this->group->check_default_notebook_in_group($this->user_id, $group_id)){
      return $this->response(array('status' => 'fail', 'message' => '不能删除包含默认笔记本的笔记本组'), 400);
    }

    //$bool为布尔值，返回删除笔记本组是否成功
    $bool = $this->group->delete_group($this->user_id, $group_id);

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

  /**
   * 获得所有笔记本组
   *
   * 返回的json格式如下
   * 获取成功时
   * [
   *    {
   *      id:"笔记本组id",
   *      name: "笔记本组名",
   *      creator_id:"用户id",
   *      created_at:"笔记本组创建时间",
   *      updated_at:"更新时间"
   *    }
   * ]
   * 获取失败时
   *    {
   *      status:"fail",
   *      message:"错误信息"
   *    }
   */
  function groups_get()
  {
    if(!$this->check_data->check_need_data(array($this->user_id)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }

    //加载模型
    $this->load->model('Group_model', 'group');

    //$groups值为关于所有笔记本组的数组，元素为笔记本组，也是一个数组
    $groups = $this->group->get_groups($this->user_id);

    if($groups)
    {
      //查找成功
      return $this->response($groups, 200); // 200 being the HTTP response code
    }
    else
    {
      //查找失败
      return $this->response(array('status' => 'fail', 'message' => '不能找到笔记本组'), 404);
    }
  }


  private $user_id;
}