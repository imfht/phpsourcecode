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

namespace app\common\model;
use think\Model;

/**
 * Class MemberPublic 公众号模型
 * @package common\model
 * @auth patrick
 */
class MemberPublic extends Model {
    //
    /* 用户模型自动完成 */
    protected $insert = ['uid','status'=>1];

    public function getMpType($key = null){
        $array = array(1 => '订阅号', 2 => '服务号', 3 => '企业号', 4 => '小程序');
        return !isset($key)?$array:$array[$key];
    }

}