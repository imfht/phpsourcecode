<?php
/**
 *错误收集
 * Created by PhpStorm.
 * User: root
 * Date: 7/13/16
 * Time: 7:41 PM
 */
class Error extends Frontend{
    public function index()
    {
        if($_POST){
            $data['info']=I('info');
            $data['file']=I('file');
            $data['time']=time();
            $this->model->add($data);
            $this->success('success');
        }else{
            $this->redirect('/404.html');
        }
    }

}