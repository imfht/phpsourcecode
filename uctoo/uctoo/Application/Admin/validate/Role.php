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
 * Class RoleModel
 * @author Patrick <contact@uctoo.com>
 */
class Role extends Validate
{

    protected $rule = [
        'name'  =>  'require|unique|checkName',
        'title' =>  'require|unique',
    ];

    protected $message  =   [
        'name.require' => '标识不能为空',
        'name.unique'     => '身份标识已经存在',
        'name.checkName' => '身份标识只能由字母和下滑线组成',
        'title.require' => '身份名不能为空',
        'title.unique'     => '身份名已经存在',
    ];

    /**
     * 验证身份名(只能有字母和下划线组成)
     * @param $name
     * @return bool
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function checkName($name){
        if(!preg_match('/^[_a-z]*$/i',$name)){
            return false;
        }
        return true;
    }
}
