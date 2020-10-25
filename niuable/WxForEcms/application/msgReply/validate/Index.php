<?php
namespace app\reply\validate;

use think\Validate;

/**
 * Index 类
 * @author WangWei
 * @version 1.0.0.0
 */
class Index extends Validate
{
	 //规则
	protected $rule = [

	];
	//自定义错误提示
	protected $message = [
	];
	/**
	 * 构造函数
	 * 初始化设置：开启批量验证
	 */
	public function __construct()
	{
		// 开启批量验证
		$this->batch();
	}
	
}