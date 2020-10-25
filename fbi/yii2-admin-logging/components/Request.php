<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace fbi\adminLogging\components;
use fbi\helpers\Tools;

/**
 * @inheritdoc
 */
class Request extends \yii\web\Request
{
    /**
     * Returns the user IP address.
     * @return string user IP address. Null is returned if the user IP address cannot be detected.
     */
    public function getUserIP()
    {
        return Tools::getUserIp();
    }
	public function getUserPort(){
		return Tools::getUserPort();
	}
}
