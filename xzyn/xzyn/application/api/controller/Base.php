<?php
namespace app\api\controller;

use think\Controller;

// API基础控制器

class Base extends Controller {

	public $uid;
	public $fname;	// 返回字段名称 数组
	public $qname;	// 请求字段名称 默认值

    public function initialize() {
		$this->uid = session('userId');
		$hash = input('hash');
		$this->getFields($hash);
	}

    public function iniApi($hash) {
    	$apiInfo = cache('apiInfo_'.$hash);	//接口信息
		return action($apiInfo['apiName'],$hash);
    }

    public function getFields($hash) {
		$fieldsData = \app\common\model\ApiFields::all(['hash'=>$hash]);
		$fieldsData = $fieldsData->visible(['fieldName','type','default'])->toArray();	// 返回字段名称
		$f_arr = [];
		$q_arr = [];
		foreach ($fieldsData as $k => $v) {
			if( $v['type'] == 1 ){
				$f_arr[] = $v['fieldName'];	// 返回字段
			}else{
				$q_arr[$v['fieldName']] = $v['default'];	// 请求字段的默认值
			}
		}
		$this->fname = $f_arr;
		$this->qname = $q_arr;
	}

}
