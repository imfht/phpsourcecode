<?php

namespace common\widgets;

/**
 * 扩展Widget组件基类
 * 
 * @property int $uid 当前用户uid
 * @property \yii\web\Controller $controller 当前控制器
 * 
 * @author ken <vb2005xu@qq.com>
 */
abstract class BaseWidget extends \yii\bootstrap\Widget
{

	/**
	 * 组件数据
	 */
	protected $_data = [];

	/**
	 * 获取当前用户的uid
	 * @return int
	 */
	public function getUid()
	{
		return $this->getView()->context->uid;
	}
	
	/**
	 * 当前控制器
	 * @return \yii\web\Controller
	 */
	public function getController()
	{
		return $this->getView()->context;
	}

}
