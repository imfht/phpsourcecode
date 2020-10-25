<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase;
use app\common\traits\ModuleContent;

//小程序 显示内容
abstract class Show extends IndexBase
{
    use ModuleContent;
    protected $model;                  //内容主题
    protected $mid;                    //模型ID
    
    
    public function add(){
        die('出错了!');
    }
    public function edit(){
        die('出错了!');
    }
    public function delete(){
        die('出错了!');
    }

    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'content');
        $this->mid = 1;
    }
    
    /**
     * 调取显示内容主题
     * @param number $id 内容ID
     * @return \think\response\Json
     */
    public function index($id=0){
        $info = $this->getInfoData($id,true);
        if(empty($info)){
            return $this->err_js('内容不存在');
        }
        
        $this->model->addView($id); //更新浏览量
        
        if($info['picurls']==''){
            $info['picurls'] = [];
        }
        
        $info['username'] = get_user_name($info['uid']);
        $info['create_time'] = date('Y-m-d H:i',$info['create_time']);
        $info['content'] = $info['full_content'] ;
        $info['content'] = str_replace('="/public/uploads', '="'.$this->request->domain().'/public/uploads', $info['content']);
        unset($info['full_content'],$info['sncode']);
        
        return $this->ok_js($info);
    }
}













