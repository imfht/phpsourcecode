<?php
namespace app\admin\controller;

class Others extends Base
{

    public function iplocation($ip='')
    {
        if($ip==''){
            $this->error('缺少IP地址');
        }
        $location = new \app\lib\IpLocation();
        $info = $location->getlocation($ip);
        $str = '';
        if($info){
            if(isset($info['country'])){
                 $str = $info['country'];
            }
            if(isset($info['area'])){
                 $str .='('. $info['area'] .')';
             }
        }
        return $this->success($str);
    }
}
