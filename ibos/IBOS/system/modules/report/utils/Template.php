<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/1
 * Time: 9:53
 */

namespace application\modules\report\utils;


use application\modules\department\model\Department;
use application\modules\user\model\User;

class Template
{

    /**
     * 用户可用的模板条件
     * @param integer $uid 用户ID
     * @return  string
     */
    public static function getTemplateCondition($uid)
    {
        $scopeCondition = "(`deptid` = 'alldept' OR FIND_IN_SET('{$uid}',uid)) AND `isnew` = 1 ";
        return $scopeCondition;
    }

    /**
     * @param string $picName 图片名称
     * 读取图片配置中每一个图片名称，以后还是要有个后台来管理这些图标比较好
     * 或者通过文件名得到对应图片颜色
     */
    public static function getPictureName($picName = '')
    {
        $path = PATH_ROOT . str_replace('\\', DIRECTORY_SEPARATOR, '\system\modules\report\static\image\tmpl_icon\PictureConfig.json');
        if (file_exists($path)){
            $picNames = file_get_contents($path);
            $picArray = json_decode($picNames, true);
            if (empty($picName)){
                return $picArray;
            }else{
                return isset($picArray[$picName]) ? $picArray[$picName] : '';
            }
        }else{
            return null;
        }
    }
}