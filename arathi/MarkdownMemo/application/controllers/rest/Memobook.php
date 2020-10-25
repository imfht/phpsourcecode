<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by PhpStorm.
 * User: Arathi
 * Date: 2015/7/26
 * Time: 19:12
 */
class Memobook extends CI_Controller{
    public function __construct(){
        parent::__construct();
    }

    public function listmb(){
        //只接受POST请求
        //$mbid = $this->input->post('mbid');
        $json = <<<JSON
{
    "username": "Arathi",
    "memobooks": [
        {
            "bookname": "笔记本1",
            "amount": "10"
        },
        {
            "bookname": "笔记本2",
            "amount": "15"
        }
    ]
}
JSON;
        $this->load->view( 'json', array('json' => $json) );
    }

    public function addmb(){
        //只接受POST请求
        $mbname = $this->input->post('mbname');
        //$uid = //从session中取出uid
    }
}
