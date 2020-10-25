<?php
namespace app\api\controller\apihelp;

use app\common\controller\BaseMember;
use app\common\model\ApiFields;
use app\common\model\ApiList as ApiLists;
use expand\ApiReturn;

//api接口列表
class Index extends BaseMember {

    public function initialize(){
        parent::initialize();
    }

	// API列表
    public function index() {
    	$apilist = ApiLists::all();
		$this->assign('apilist',$apilist);
		return $this->fetch();
    }

	// API接口详情
    public function apiinfo($hash) {
		$apiinfo = ApiLists::get(['hash'=>$hash]);
		if( empty($hash) || empty($apiinfo) ){
			return ApiReturn::r('-1');
		}
		$f_field = ApiFields::all( ['hash'=>$hash,'type'=>1] );	//返回字段
		$q_field = ApiFields::all( ['hash'=>$hash,'type'=>0] );	//请求字段
		$this->assign('f_field',$f_field);
		$this->assign('q_field',$q_field);
		$this->assign('data',$apiinfo);
		return $this->fetch();
    }

	// 错误码列表
    public function errorlist() {
		$errorlist = ApiReturn::$Code;
		$this->assign('errorlist',$errorlist);
		return $this->fetch();
    }

}
