<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
        $apps = F('apps');
        $this->assign('apps', $apps);
        $this->display();
    }

    public function delete($code='')
    {
        if (!$code) {
            $this->error('参数错误');
        }
        $apps = F('apps');        
        unset($apps[$code]);
        F('apps',$apps);
        $this->success('删除成功','reload');
    }

    public function edit($code='')
    {
        if (!$code) {
            $this->error('参数错误');
        }
        $apps = F('apps');
        $app = $apps[$code];
        $this->assign('vo', $app);      
        $this->display();
    }

    public function update()
    {
        foreach ($_POST as $k=>$v) {
            if (!$_POST[$k]) {
                $this->error('所有项目必须填写');
                return;
            }
        }
        extract($_POST);
        if(!preg_match("/^[a-z\s]+$/",$code)){
            $this->error('应用标志必须为小写字母');
        }
        $apps = F('apps');

        $apps[$code] = $_POST;
        F('apps',$apps);
        F($code.'/cfg',$_POST);
        $this->success('应用修改成功','reload');
    }


    public function insert()
    {
        foreach ($_POST as $k=>$v) {
            if (!$_POST[$k]) {
                $this->error('所有项目必须填写');
                return;
            }
        }
        extract($_POST);
        if(!preg_match("/^[a-z\s]+$/",$code)){
            $this->error('应用标志必须为小写字母');
        }
        $apps = F('apps');

        if ($apps[$code]) {
            $this->error('该应用标志已存在');
        }

        $apps[$code] = $_POST;
        F('apps',$apps);
        F($code.'/cfg',$_POST);
        $this->success('应用新增成功');
    }
}