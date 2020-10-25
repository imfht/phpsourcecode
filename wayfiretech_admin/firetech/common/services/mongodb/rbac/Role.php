<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-08-12 22:32:51
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-12 22:32:51
 */
 
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\services\mongodb\rbac;
 

/**
 * Role is a special version of [[\yii\rbac\Role]] dedicated to MongoDB RBAC implementation.
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0.5
 */
class Role extends \yii\rbac\Role
{
    /**
     * @var array|null list of parent item names.
     */
    public $parents;
}