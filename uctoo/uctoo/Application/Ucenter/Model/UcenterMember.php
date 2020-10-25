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
namespace app\ucenter\model;

use think\Model;

class UcenterMember extends Model
{
    //
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'reg_time';
    //
    /* 用户模型自动完成 */
    protected $auto = [];
    protected $insert = ['status' => 1,'reg_ip'];
    protected $update = [];

    //字段修改器
    public function setRegIpAttr($value)
    {
        return get_client_ip($value);
    }
    //修改器只有在用模型方式设置值时会触发
    protected function setPasswordAttr($value,$data)
    {
            $result = think_ucenter_md5($value, UC_AUTH_KEY);
            return $result;
    }

}
