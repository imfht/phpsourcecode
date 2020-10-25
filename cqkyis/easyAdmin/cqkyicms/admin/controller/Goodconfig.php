<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/1 0001
 * Time: 18:05
 */

namespace app\admin\controller;


use app\admin\model\GoodconfigModel;

class Goodconfig extends Base
{
    protected $title="商城参数配置";
    public function index(){
        $name="商城参数";
        $goodconfig = new GoodconfigModel();
        if(request()->isPost()){
            $data = input('post.');

            $res=$goodconfig->add($data);
            if($res['code']==1){

                $this->ky_success($res['msg'],$res['data']);
            }else{

                $this->ky_error($res['msg']);
            }
        }else{
       $list = $goodconfig->findByone();
        $this->assign([
            'name'=>$name,
            'title'=>$this->title,
            'config'=>$list
        ]);
        }
        return $this->fetch();
    }

}