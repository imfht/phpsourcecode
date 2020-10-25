<?php
namespace app\view\validate;

use think\Validate;

class Index extends Validate {
	protected $rule = [ 
			'id|素材id'=>'require',
			'type|素材类型'=>'require',
	];
	protected $message = [ 

	];
	public function __construct() {
		// 开启批量验证
		$this->batch ();
	}
}