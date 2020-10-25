<?php
/**
 * Created by PhpStorm.
 * User: Arathi
 * Date: 2015/7/28
 * Time: 23:43
 */

class Memo extends CI_Controller {
    public function __construct(){
        parent::__construct();
    }

    public function content($memo_id = 0){
        //只接受POST请求
        //$memoid = $this->input->get('memoid');
        //TODO 通过数据库查询到标题和内容
        $title = "总标题";
        $content = "#标题H1#\n##标题H2##\n内容体1\n###标题H3###\n内容体2";
        $jsonArray = array(
            'memoid' => $memo_id,
            'title' => $title,
            'content' => $content
        );
        $json = json_encode($jsonArray);
        //转换为单行文本
        $json = str_replace("\r\n", "\n", $json);
        $json = str_replace("\r", "\n", $json);
        $json = str_replace("\n", "\\n", $json);
        //$this->load->view( 'json', array('json' => $convertedJson) );
        $this->output
            ->set_content_type('application/json')
            ->set_output($json)
            ->_display();
        exit;
    }
    
    public function index(){
        $jsonArray = array(
            'memobooks' => array(
                array(
                    'id' => 1,
                    'name' => '笔记本1',
                    'amount' => 2,
                    'memos' => array(
                        array('id'=>1, 'title'=>'笔记1'),
                        array('id'=>2, 'title'=>'笔记2')
                    )
                ),
                array(
                    'id' => 1,
                    'name' => '笔记本2',
                    'amount' => 3,
                    'memos' => array(
                        array('id'=>11, 'title'=>'笔记11'),
                        array('id'=>12, 'title'=>'笔记12'),
                        array('id'=>13, 'title'=>'笔记13')
                    )
                )
            )
        );
        $json = json_encode($jsonArray);
        $json = str_replace("\r\n", "\n", $json);
        $json = str_replace("\r", "\n", $json);
        $json = str_replace("\n", "\\n", $json);
        //$this->load->view( 'json', array('json' => $json) );
        $this->output
            ->set_content_type('application/json')
            ->set_output($json)
            ->_display();
        exit;
    }

}