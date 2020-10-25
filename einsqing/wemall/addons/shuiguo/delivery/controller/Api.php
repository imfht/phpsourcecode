<?php
namespace addons\shuiguo\delivery\controller;

use think\addons\Controller;

class Api extends Controller
{
    public function _initialize(){
        
        if (request()->isOptions()){
            
            abort(json(true,200));
        }
    }
    
    //配送时间
    public function deliveryTime()
    {

        $delivery_time = x_model('AddonDeliveryConfig')->value('delivery_time');
        $data['delivery_time'] = explode(',',$delivery_time);
        
        return json(['data' => $data, 'msg' => '配送时间', 'code' => 1]);
    }
    
    
    
}