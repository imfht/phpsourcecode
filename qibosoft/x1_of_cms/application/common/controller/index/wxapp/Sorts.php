<?php
namespace app\common\controller\index\wxapp;

use app\common\controller\IndexBase; 


//小程序获取栏目信息
abstract class Sorts extends IndexBase
{
    protected $model;                  //内容主题
    
    protected function _initialize()
    {
        parent::_initialize();
        preg_match_all('/([_a-z]+)/',get_called_class(),$array);
        $dirname = $array[0][1];
        $this->model = get_model_class($dirname,'sort');
    }
    
    /**
     * 获取栏目数据
     * @return \think\response\Json
     */
    public function index(){
        $_array = $this->model->getTitleList();
        $array = [];
        foreach ($_array AS $key=>$value){
            $array[] = [
                    'id'=>$key,
                    'name'=>$value
            ];
        }
        return $this->ok_js($array);
    }
    
    public function hot(){
        $array = [
                [
                        'id' => 'reply',
                        'name' => '最新回复',
                ],
                [
                        'id' => 'new',
                        'name' => '最新贴子',
                ],
                [
                        'id' => 'star',
                        'name' => '推荐贴子',
                ],
                [
                        'id' => 'hot',
                        'name' => '热门贴子',
                ],
        ];
        return $this->ok_js($array);
    }

}













