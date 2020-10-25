<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-03 07:24:21
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-11 00:07:09
 */

namespace common\rbac;

use yii\rbac\Rule;

class AddonsRule extends Rule
{
    public $name = 'AddonsRule';

    public function execute($user, $item, $params)
    {
        return true;
        return isset($params['post']) ? $params['post']->createdBy == $user : false;
    }
}
?>
 