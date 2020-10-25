<?php
/**
     * RestfulApi 接口
*/
namespace Restful\Controller;

use Think\Controller\RestController;


class IndexController extends BaseController {
    protected $allowMethod    = array('get','post','put'); // REST允许的请求类型列表
    protected $allowType      = array('html','xml','json'); // REST允许请求的资源类型列表
	//APP首页
    public function index()
    {
        switch ($this->_method){
            case 'get': //get请求处理代码
  
                $data['title'] = 'title';
                $data['description'] = 'description';
                
                $result['info'] = 'succee';
                $result['data'] = $data;
                $result['code'] = 200;

                $this->response($result,$this->type);
            break;
            case 'put':
            
            break;
            case 'post':
                
                $data['title'] = 'title';
                $data['description'] = 'description';
                
                $result['info'] = 'succee';
                $result['data'] = $data;
                $result['code'] = 200;
                $this->response($result,'json');
            break;
        
        
        }
       
    }
	
}