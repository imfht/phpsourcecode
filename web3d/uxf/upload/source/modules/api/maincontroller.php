<?php
/**
 * 测试rest控制器
 */
class MainController extends Rest_Controller {
    
    protected function ActStudents_get(){
        $data = array('name' => 'Jimmy', 'age' => '30', 'email' => 'web3d@live.com');
        
        $this->response($data, 'json');
    }
}

