<?php
namespace addons\shuiguo\stores\controller;

use think\addons\Controller;

class Api extends Controller
{
    public function _initialize(){
        if (request()->isOptions()){
            abort(json(true,200));
        }
    }
    
    //门店列表
    public function storesList()
    {
        $storesList = x_model('AddonStores')->where('status',1)->select()->toArray();
        $data['stores'] = $storesList;
        
        if($storesList){
            $info = ['data' => $data, 'msg' => '门店列表', 'code' => 1];
        }else{
            $info = ['data' => $data, 'msg' => '门店列表', 'code' => 0];
        }
        return json($info);
    }
    
    
    
}