<?php

namespace app\api\controller;

use expand\ApiReturn;

class Arctype extends Base {
    public function initialize(){
        parent::initialize();
//		p('Index');
    }

	// 获取分类列表接口
    public function index($hash) {
		$data = cache('input_'.$hash);	//请求字段
		$usertoken = request()->header('usertoken');
    	$Arctype = new \app\common\model\Arctype;
		$arctype_data = $Arctype->where( ['status'=>1,'mid'=>21] )->order('pid asc')->select();	// 列表数据
		$newdata = $arctype_data->visible($this->fname)->toArray();	// 根据返回字段来返回
    	return ApiReturn::r(1,$newdata);
	}


}
