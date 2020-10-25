<?php
class EmptyAction extends Action {
    Public function _initialize(){
         if(strtolower($Think.MODULE_NAME)=='admin')
         {
            $this->success('正在跳转到后台....','../Admin/index.php');
            exit();
         }
         else
         {
            header("HTTP/1.0 404 Not Found");//使HTTP返回404状态码 
            $this->display("error/404"); 
            die;
         }
    }
    
}
?>