<?php
namespace Home\Controller;

use Home\Controller\HomeController;

class DistrictController extends HomeController
{
    //获取中国省份信息
    public function getProvince(){
        if (IS_AJAX){
            $pid = I('pid');  //默认的省份id
    
           
            $map['level'] = 1;
            $map['pid'] = 0;
            $list = D('District')->_list($map);
    
            $data = "<option value =''>-省份-</option>";
            foreach ($list as $k => $vo) {
                $data .= "<option ";
                if( $pid == $vo['id'] ){
                    $data .= " selected ";
                }
                $data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
            }
            $this->ajaxReturn($data);
        }
    }
    
    //获取城市信息
    public function getCity(){
        if (IS_AJAX){
            $cid = I('cid');  //默认的城市id
            $pid = I('pid');  //传过来的省份id
    
           
            $map['level'] = 2;
            $map['pid'] = $pid;
    
            $list = D('District')->_list($map);
    
            $data = "<option value =''>-城市-</option>";
            foreach ($list as $k => $vo) {
                $data .= "<option ";
                if( $cid == $vo['id'] ){
                    $data .= " selected ";
                }
                $data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
            }
            $this->ajaxReturn($data);
        }
    }
    
    //获取区县市信息
    public function getDistrict(){
        if (IS_AJAX){
            $did = I('did');  //默认的城市id
            $cid = I('cid');  //传过来的城市id
    
          
            $map['level'] = 3;
            $map['pid'] = $cid;
    
            $list = D('District')->_list($map);
    
            $data = "<option value =''>-区县-</option>";
            foreach ($list as $k => $vo) {
                $data .= "<option ";
                if( $did == $vo['id'] ){
                    $data .= " selected ";
                }
                $data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
            }
            $this->ajaxReturn($data);
        }
    }
    
    //获取乡镇信息
    public function getCommunity(){
        if (IS_AJAX){
            $coid = I('coid');  //默认的乡镇id
            $did = I('did');  //传过来的区县市id
    
            $where['city_id'] = $cid;
    
           
            $map['level'] = 4;
            $map['pid'] = $did;
    
            $list = D('District')->_list($map);
    
            $data = "<option value =''>-街道-</option>";
            foreach ($list as $k => $vo) {
                $data .= "<option ";
                if( $coid == $vo['id'] ){
                    $data .= " selected ";
                }
                $data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
            }
            $this->ajaxReturn($data);
        }
    }
}

?>