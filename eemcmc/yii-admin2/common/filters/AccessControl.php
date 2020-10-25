<?php

namespace common\filters;

/**
 * Description of AccessControl
 *
 * @author ken <vb2005xu@qq.com>
 */
class AccessControl extends \yii\filters\AccessControl
{

	/**
	 * @inheritdoc
	 * @var \common\filters\AccessRule
	 */
	public $ruleConfig = ['class' => 'common\filters\AccessRule'];

}
