<?php
namespace app\common\model;

//第三方接口秘钥管理

use think\Model;

class ApiStoreAuth extends Model {
	//只读字段,一旦写入，就无法更改。
	protected $readonly = [];
	// 新增自动完成列表
    protected $insert  = ['status'];
	//更新自动完成列表
    protected $update = [];
	//新增和更新自动完成列表
    protected $auto = [];

	//关联模型
//  public function User() {
//      return $this->hasOne('User', 'id', 'writer');
//  }

    protected function setStatusAttr($value) {	//状态(是否审核) [修改器]
    	if ($value){
            return $value;
        }else{
            return 1;
        }
    }

    public function getStatusTurnAttr($value, $data) {	//状态(是否审核) [获取器]
        $turnArr = [0=>'停用', 1=>'在用'];
        return $turnArr[$data['status']];
    }







}