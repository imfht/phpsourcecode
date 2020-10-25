<?php
namespace app\common\model;

//用于保存各个API的字段规则

use think\Model;

class ApiFields extends Model {
	//只读字段,一旦写入，就无法更改。
	protected $readonly = ['hash'];
	// 新增自动完成列表
    protected $insert  = [];
	//更新自动完成列表
    protected $update = [];
	//新增和更新自动完成列表
    protected $auto = [];

	//字段类型
    public $dataType = array(
        1 	=> 'Integer[整数]',
        2  	=> 'String[字符串]',
        3 	=> 'Boolean[布尔]',
        4   => 'Enum[枚举]',
        5   => 'Float[浮点数]',
        6   => 'File[文件]',
        7  	=> 'Mobile[手机号]',
        8  	=> 'Object[对象]',
        9   => 'Array[数组]',
        10   => 'Email[邮箱]',
        11   => 'Date[日期]',
        12   => 'Url',
        13   => 'IP',
    );

	//关联模型
    public function apiList() {
        return $this->hasOne('ApiList', 'hash', 'hash');
    }

    public function getIsMustTurnAttr($value, $data) {	//是否必须 isMust 字段 [获取器]
        $turnArr = [0=>'选填', 1=>'必填'];
        return $turnArr[$data['isMust']];
    }

    public function getDataTypeTurnAttr($value, $data) {	//字段类型 dataType 字段 [获取器]
        $turnArr = [
        	1 	=> 'Integer[整数]',
	        2  	=> 'String[字符串]',
	        3 	=> 'Boolean[布尔]',
	        4   => 'Enum[枚举]',
	        5   => 'Float[浮点数]',
	        6   => 'File[文件]',
	        7  	=> 'Mobile[手机号]',
	        8  	=> 'Object[对象]',
	        9   => 'Array[数组]',
        	10   => 'Email[邮箱]',
        	11   => 'Date[日期]',
        	12   => 'Url',
        	13   => 'IP',
        ];
        return $turnArr[$data['dataType']];
    }





}