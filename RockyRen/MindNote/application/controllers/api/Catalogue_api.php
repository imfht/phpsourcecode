<?php
/**
 * Created by PhpStorm.
 * User: rockyren
 * Date: 15/5/19
 * Time: 09:56
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';

class Catalogue_api extends REST_Controller {
  function __construct(){
    parent::__construct();
    $this->load->database();

    $this->load->library('session');
    //获取参数
    $this->user_id = $this->session->userdata('userId');
  }


  /**
   * 取得笔记目录
   *
   * 返回的json格式如下
   * 添加成功时
   *  {
   *    groups:
   *    [
   *      {
   *        id:"笔记本组id",
   *        name: "笔记本组名",
   *        creator_id:"用户id",
   *        created_at:"笔记本组创建时间",
   *        updated_at:"更新时间",
   *        notebooks: [ {//参考notebook_get} ]
   *      }
   *    ],
   *    singleNotebooks: [
   *      {//参考notebook_get}
   *    ]
   *  }
   */
  public function catalogues_get(){
    if(!$this->user_id){
      return $this->response(array('status' => 'fail', 'message' => '获取参数失败'), 400);
    }

    $this->load->model('Group_model', 'group');
    $this->load->model('Notebook_model', 'notebook');

    $catalogue = array();
    $groupList = array();
    //取得笔记本组
    $groups = $this->group->get_groups($this->user_id);

    if($groups){
      foreach($groups as $group){
        $group_id = $group['id'];

        $notebooks = $this->notebook->get_notebooks($this->user_id, $group_id);
        $group['notebooks'] = $notebooks;
        array_push($groupList, $group);
      }
    }


    $catalogue['groups'] = $groupList;

    //取得不属于任何笔记本组的笔记本
//    $singleNotebookList = $this->notebook->get_notebooks($this->user_id, null);
//    $catalogue['singleNotebooks'] = array();
//    if($singleNotebookList){
//      array_push($catalogue['singleNotebooks'], $singleNotebookList);
//    }

    $notebooks = $this->notebook->get_notebooks($this->user_id, null);
    if($notebooks){
      $catalogue['singleNotebooks'] = $notebooks;
    }else{
      $catalogue['singleNotebooks'] = array();
    }



    return $this->response($catalogue, 200);

  }

}