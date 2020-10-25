<?php
namespace app\common\model;

//应用appId和appSecret表

use think\Model;

class ApiApp extends Model {
	//只读字段,一旦写入，就无法更改。
	protected $readonly = ['app_id', 'app_secret'];
	// 新增自动完成列表
    protected $insert  = ['app_status'=>1];
	//更新自动完成列表
    protected $update = [];
	//新增和更新自动完成列表
    protected $auto = [];

	//关联模型
    public function User() {
        return $this->hasOne('User', 'id', 'uid');
    }
	//关联模型
    public function ApiApptoken() {
        return $this->hasOne('ApiApptoken', 'app_id', 'app_id');
    }

    public function getAppStatusTurnAttr($value, $data) {	//状态 app_status (是否审核) [获取器]
        $turnArr = [0=>'停用', 1=>'在用'];
        return $turnArr[$data['app_status']];
    }


}