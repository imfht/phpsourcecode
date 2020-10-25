<?php
namespace addons\Chinacity\controller;

use muucmf\addons\Controller;

class Chinacity extends Controller{
	
	//获取中国省份信息
	public function getProvince(){

		if (request()->isPost()){
			
			$pid = input('pid');  //默认的省份id

			if( !empty($pid) ){
				//$map['id'] = $pid;
			}
			$map['level'] = 1;
			$map['upid'] = 0;
			$list = model('addons\chinacity\model\District')->_list($map);

			$data = "<option value =''>-省份-</option>";
			foreach ($list as $k => $vo) {
				$data .= "<option ";
				if( $pid == $vo['id'] ){
					$data .= " selected ";
				}
				$data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
			}
			return $data;
		}
	}

	//获取城市信息
	public function getCity(){
		if (request()->isAjax()){
			$cid = input('cid');  //默认的城市id
			$pid = input('pid');  //传过来的省份id

			if( !empty($cid) ){
				//$map['id'] = $cid;
			}
			$map['level'] = 2;
			$map['upid'] = $pid;

			$list = model('addons\chinacity\model\District')->_list($map);

			$data = "<option value =''>-城市-</option>";
			foreach ($list as $k => $vo) {
				$data .= "<option ";
				if( $cid == $vo['id'] ){
					$data .= " selected ";
				}
				$data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
			}
			return $data;
		}
	}

	//获取区县市信息
	public function getDistrict(){
		if (request()->isAjax()){
			$did = input('did');  //默认的城市id
			$cid = input('cid');  //传过来的城市id

			if( !empty($did) ){
				//$map['id'] = $did;
			}
			$map['level'] = 3;
			$map['upid'] = $cid;

			$list = model('addons\chinacity\model\District')->_list($map);

			$data = "<option value =''>-州县-</option>";
			foreach ($list as $k => $vo) {
				$data .= "<option ";
				if( $did == $vo['id'] ){
					$data .= " selected ";
				}
				$data .= " value ='" . $vo['id'] . "'>" . $vo['name'] . "</option>";
			}
			return $data;
		}
	}

}