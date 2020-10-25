<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-06-27 14:06:58
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-21 10:11:41
 */
 

namespace common\helpers;

use Yii;
use yii\base\Model;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

class loggingHelper
{
     /**
     * 检测目录并循环创建目录
     *
     * @param $catalogue
     */
    public static function mkdirs($catalogue)
    {
        if (!file_exists($catalogue)) {
            self::mkdirs(dirname($catalogue));
            mkdir($catalogue, 0777);
        }

        return true;
    }

    /**
     * 写入日志
     *
     * @param $path
     * @param $content
     * @return bool|int
     */
    public static function writeLog($moduleName,$path,$mark, $content=[])
    {
        $basepath = Yii::getAlias('@common/addons/'.$moduleName.'/logs/'.date('Y/m/d/'));
        self::mkdirs(dirname($basepath.$path.'.log'));
        @chmod($path, 0777);
        $time = date('m/d H:i:s');
        return file_put_contents($basepath.$path.'.log', "\r\n" .$time.'-'.$mark.':'. json_encode($content), FILE_APPEND);
    }

}