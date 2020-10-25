<?php
    class MY_Controller extends CI_Controller {

        function __construct(){
            parent::__construct();
            /*开启Session*/
            @session_start();
            // 图片基础参数设置
            define('DIR_STATIC',base_url().'static/');
            define('DIR_IMG', base_url().'static/img/');
            define('DIR_BT', base_url().'static/bootstrap/');
            define('DIR_PLUGIN', base_url().'static/plugins/');
            define('DIR_CSS', base_url().'static/css');
            define('DIR_JS', base_url().'static/js');

            // 字符编码
            header ( 'Content-Type: text/html; charset=UTF-8' );
        }


        function privileges_check(){
            /*访问控制*/
            $action = $this->uri->segment(3);
            if(!isset($_SESSION['info'])){
                redirect(base_url().'user_manager/login_page','location',301);
            }
        }

        // 通用插入函数
        function all_insert($table,$data,$key=''){
            if($key !=''){
                $res = $this->db->where($key)->update($table,$data);
            }else{
                $res = $this->db->insert($table,$data);
            }
            return $res;
        }


        // 通用读取函数

    }

?>