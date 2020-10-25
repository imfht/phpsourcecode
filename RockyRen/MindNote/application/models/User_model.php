<?php
/**
 * Created by PhpStorm.
 * User: wakeup333
 * Date: 15-4-25
 * Time: 上午9:52
 */

  class User_model extends CI_Model
  {
    function __construct()
    {
        parent::__construct();
    }


    /**
     * 用户验证(是否存在查找username和password都对应相同的用户)
     */
    public function validate()
    {
      $username = $this->security->xss_clean($this->input->post('username'));
      $password = $this->security->xss_clean($this->input->post('password'));


      //在表site_user中查找username和password都对应相同的结果
      $res = $this->db->where( array('username' => $username,
          'password' => hash("sha256", $password)) )->get('user');



      //返回数组,数组中是一个一个的对象
      $result = $res->result();

      //查找结果不为空
      if( !empty($result) )
      {
        // If there is a user,then create session data
        //$res->row()返回第一条数据(对象)
        $row = $res->row();
        $data = array( 'userId' => $row->id, 'username' => $row->username);
        $this->session->set_userdata($data);
        return true;
      }

      //如果找不到对应用户,则返回false
      return false;
    }

    /**
     * 添加用户
     */
    public function add_user()
    {
      $username = $this->security->xss_clean($this->input->post('username'));
      $password = $this->security->xss_clean($this->input->post('password'));

      //对密码进行加密
      $encrypt_password = hash("sha256", $password);

      //将用户信息写进数据库
      $user_isAdd = $this->db->insert('user', array('username' => $username, 'password' => $encrypt_password));

      //在表site_user中查找此用户
      $res = $this->db->where( array('username' => $username, 'password' => $encrypt_password))
        ->get('user');
      $row = $res->row();
      $data = array( 'userId' => $row->id, 'username' => $row->username);
      $this->session->set_userdata($data);
    }

    /**
     * 取得登录用户的信息
     */
    public function get_current_user(){
      $userId = $this->session->userdata('userId');
      if($userId){
        $res = $this->db->where('id', $userId)->get('user');
        $result = $res->result();

        if($result){
          $user_row = $res->row();
          return $user_row;
        }
      }

      return null;
    }

    public function getUser(){
      $res = $this->db->where('username', 'admin1')->get('user');
      $result = $res->result();
      return $res->row();
    }

  }