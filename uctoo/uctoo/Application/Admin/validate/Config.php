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
 * 用户角色模型验证类
 * Class ConfigModel
 * @author Patrick <contact@uctoo.com>
 */
class Config extends Validate
{
    protected $rule = [
        'name'  =>  'require|unique',
        'title' =>  'require',
    ];

    protected $message  =   [
        'name.require' => '标识不能为空',
        'name.unique'     => '标识已经存在',
        'title.require' => '名称不能为空',
    ];
}
