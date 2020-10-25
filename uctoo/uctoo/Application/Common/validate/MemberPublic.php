<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2017 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------

namespace app\common\validate;

use think\Validate;
/**
 * 公众号模型类验证器
 * Class MemberPublic
 * @author Patrick <contact@uctoo.com>
 */
class MemberPublic extends Validate
{
    protected $rule = [
        'public_id'  =>'unique:member_public',
        'wechat'  => 'unique:member_public',
        'appid'  => 'unique:member_public|require',
    ];

    protected $message  =   [
        'public_id.unique' => '公众号原始id不能重复',
        'wechat.unique'   => '微信号不能重复',
        'appid.unique'   => 'AppID不能重复',
        'appid.require'   => 'AppID不能为空',
    ];

}
