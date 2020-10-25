<?php
namespace app\green\controller\home;
use app\common\controller\Home;
use app\green\model\GreenDevice;

class Iot extends Home{

    public function index(){
       $list = GreenDevice::where(['state' => 0])->whereTime('update_time','<',time()-35)->select();
       foreach ($list as $device){
           GreenDevice::where(['id' => $device->id])->update(['state' => 1]);
       }
    }
}