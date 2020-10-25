<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';

class Notebook_api extends REST_Controller
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
   * 获得获得某个笔记本组里的一个笔记本
   *
   * 返回的json格式如下
   * 查找成功时
   *    {
   *      id:"笔记本id",
   *      name: "笔记本名",
   *      creator_id:"用户id",
   *      created_at:"笔记本创建时间",
   *      updated_at:"更新时间",
   *      group_id:"从属笔记本组id",
   *      default:1(true)|0(false)
   *    }
   * 查找失败时
   *    {
   *      status:"fail",
   *      message:"错误信息"
   *    }
   *
   */
  function notebook_get()
  {
    //获取参数
    $notebook_id = $this->get('notebook_id');

    if(!$this->check_data->check_need_data(array($this->user_id, $notebook_id)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }

    //加载模型
    $this->load->model('Notebook_model', 'notebook');

    //$notebook值为关于某个笔记本组里某个笔记本的数组，元素为笔记本的各个属性，非数组
    $notebook = $this->notebook->get_notebook($this->user_id, $notebook_id);

    if($notebook)
    {
      //查找成功
      return $this->response($notebook, 200); // 200 being the HTTP response code
    }
    else
    {
      //查找失败
      return $this->response(array('status' => 'fail', 'message' => '不能找到该笔记本'), 404);
    }
  }

  /**
   * 获得某个笔记本组里的所有笔记本
   *
   * 返回的json格式如下
   * 获取成功时
   * [
   *    {
   *      id:"笔记本id",
   *      name: "笔记本名",
   *      creator_id:"用户id",
   *      created_at:"笔记本创建时间",
   *      updated_at:"更新时间",
   *      group_id:"从属笔记本组id",
   *      default:1(true)|0(false)
   *    }
   * ]
   * 获取失败时
   *    {
   *      status:"fail",
   *      message:"错误信息"
   *    }
   */
  function notebooks_get()
  {
    //获取参数
    $group_id = $this->get('group_id');

    if(!$this->check_data->check_need_data(array($this->user_id, $group_id)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }

    //加载模型
    $this->load->model('Notebook_model', 'notebook');

    //$notebooks值为关于某个笔记本组里所有笔记本的数组，元素为笔记本，也是一个数组
    $notebooks = $this->notebook->get_notebooks($this->user_id, $group_id);

    if($notebooks)
    {
      //查找成功
      return $this->response($notebooks, 200); // 200 being the HTTP response code
    }
    else
    {
      //查找失败
      return $this->response(array('status' => 'fail', 'message' => '不能找到笔记本'), 404);
    }
  }


  /**
   * 添加一个笔记本
   *
   * 请求的json格式如下
   *    {
   *      name: "笔记本名"(必需)
   *      group_id:"笔记本组id"(可选)
   *    }
   *
   * 返回的json格式如下
   * 添加成功时
   *    {
   *      id:"笔记本id",
   *      name: "笔记本名",
   *      creator_id:"用户id",
   *      created_at:"笔记本创建时间",
   *      updated_at:"更新时间",
   *      group_id:"从属笔记本组id",
   *      default:1(true)|0(false)
   *    }
   * 添加失败时
   *    {
   *      status:"fail",
   *      message:"错误信息"
   *    }
   */
  function notebook_post()
  {
    //获取参数
    $name = $this->post('name');
    //从post中取得的groupId，为空时将笔记本组的group_id设为null
    $addin_group_id = $this->post('group_id');
    if($addin_group_id === false)
    {
      $addin_group_id = null;
    }

    if(!$this->check_data->check_need_data(array($this->user_id, $name)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }

    //加载模型
    $this->load->model('Notebook_model', 'notebook');

    //$bool为布尔值，返回增加笔记本组是否成功
    $new_notebook_id = $this->notebook->add_notebook($this->user_id, $addin_group_id, $name);

    if($new_notebook_id)
    {
      $new_notebook = $this->notebook->get_notebook($this->user_id, $new_notebook_id);
      return $this->response($new_notebook, 200);
    }
    else
    {
      //增加失败
      return $this->response(array('status' => 'fail', 'message' => '增加失败'), 404);
    }
  }




  /**
   * 修改某个笔记本组里的一个笔记本
   *
   * 请求的json格式如下
   *    {
   *      name: "笔记本名"(可选)
   *      default:"true"|"false",(可选)
   *      group_id: number,当group_id为 -1 时转化为null(可选，但三者必需有一个)
   *    }
   *
   * 返回的json格式如下
   * 修改成功时
   *    {
   *      id:"笔记本id",
   *      name: "笔记本名",
   *      creator_id:"用户id",
   *      created_at:"笔记本创建时间",
   *      updated_at:"更新时间",
   *      group_id:"从属笔记本组id",
   *      default:1(true)|0(false)
   *    }
   * 添加失败时
   *    {
   *      status:"fail",
   *      message:"错误信息"
   *    }
   *
   */
  function notebook_put()
  {
    //获取参数
    $notebook_id = $this->get('notebook_id');

    $name = $this->put('name');
    $default = $this->put('default');
    $alter_group_id = $this->put('group_id');

    if(!$this->check_data->check_need_data(array($this->user_id, $notebook_id)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }

    if(!$this->check_data->check_optional_data(array($name, $default, $alter_group_id)))
    {
      return $this->response(array('status' => 'fail1', 'message' => '没有提供参数'), 400);
    }

    //加载模型
    $this->load->model('Notebook_model', 'notebook');

    $updateData = $this->makeUpdateData($name, $alter_group_id);

    $bool = $this->notebook->change_notebook($this->user_id, $notebook_id, $updateData);

    if($bool)
    {
      //获取修改成功的笔记本组
      $notebook = $this->notebook->get_notebook($this->user_id, $notebook_id);
      //修改成功
      return $this->response($notebook, 200); // 200 being the HTTP response code
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
   * @param $alter_group_id
   * @return array
   */

  private function makeUpdateData($name, $alter_group_id)
  {
    $updateData = array();
    if($name !== false)
    {
      $updateData['name'] = $name;
    }

    if($alter_group_id !== false)
    {
      if($alter_group_id == -1){
        $updateData['group_id'] = null;
      }else{
        $updateData['group_id'] = $alter_group_id;
      }

    }
    return $updateData;
  }

  /**
   * 删除某个笔记本组里的一个笔记本
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
  function notebook_delete()
  {
    $notebook_id = $this->get('notebook_id');

    if(!$this->check_data->check_need_data(array($this->user_id, $notebook_id)))
    {
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }



    //加载模型
    $this->load->model('Notebook_model', 'notebook');

    //如果笔记本为默认笔记本，则不能删除
    $default_notebook_id = $this->notebook->get_default_notebook_id();
    if($notebook_id == $default_notebook_id){
      return $this->response(array('status' => 'fail', 'message' => '不能删除默认笔记本'), 400);
    }

    //$bool为布尔值，返回删除笔记本是否成功
    $bool = $this->notebook->delete_notebook($this->user_id, $notebook_id);

    if($bool)
    {
      //删除成功
      return $this->response(null, 200); // 200 being the HTTP response code
    }
    else
    {
      //删除失败（删除默认笔记本）
      return $this->response(array('status' => 'fail', 'message' => '删除失败'), 404);
    }
  }



}