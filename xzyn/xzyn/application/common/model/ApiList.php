<?php
namespace app\common\model;

//用于保存各个API的字段规则

use think\Model;

class ApiList extends Model {
	//只读字段,一旦写入，就无法更改。
	protected $readonly = ['hash'];
	// 新增自动完成列表
    protected $insert  = [];
	//更新自动完成列表
    protected $update = [];
	//新增和更新自动完成列表
    protected $auto = ['status'];

	//关联模型
    public function api_fields() {
        return $this->hasOne('ApiFields', 'hash', 'hash');
    }

    public function getStatusTurnAttr($value, $data) {	//状态(是否审核) status 字段 [获取器]
        $turnArr = [0=>'禁用', 1=>'在用'];
        return $turnArr[$data['status']];
    }
    public function getMethodTurnAttr($value, $data) {	//请求方式 method 字段 [获取器]
        $turnArr = [0=>'不限', 1=>'POST',2=>'GET'];
        return $turnArr[$data['method']];
    }
    public function getAccessTokenTurnAttr($value, $data) {	//是否需要认证AccessToken accessToken 字段 [获取器]
        $turnArr = [0=>'不验证Token', 1=>'验证Token'];
        return $turnArr[$data['accessToken']];
    }
    public function getNeedLoginTurnAttr($value, $data) {	//是否需要认证用户token needLogin 字段 [获取器]
        $turnArr = [0=>'不验证登录', 1=>'验证登录'];
        return $turnArr[$data['needLogin']];
    }
    public function getIsTestTurnAttr($value, $data) {	//是否是测试模式 isTest 字段 [获取器]
        $turnArr = [0=>'生产模式', 1=>'测试模式'];
        return $turnArr[$data['isTest']];
    }
    public function setStatusAttr($value, $data) {	// status 字段 [修改器]
        return $value === 0 ? 0 : 1;
    }

}