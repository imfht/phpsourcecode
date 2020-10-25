<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2016 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\admin\validate;

use think\Validate;
/**
 * 用户组模型类
 * Class AuthGroupModel
 * @author Patrick <contact@uctoo.com>
 */
class AuthGroup extends Validate
{
    protected $rule = [
        'title'  =>  'require',
        'description' =>  'max:80',
    ];

    protected $message  =   [
        'title.require' => '名称必须',
        'description.max'     => '名称最多不能超过80个字符',
    ];

}
