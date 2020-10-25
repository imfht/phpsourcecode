<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\model;

/**
 * 会员模型
 */
class User extends ModelBase
{

    /**
     * 上级获取器
     */
    public function getLeaderNicknameAttr($leader_id)
    {

        return $this->setname('User')->getDataValue(['id' => $leader_id], 'nickname');
    }

    public function getStatutextAttr($status)
    {
        $arr = [DATA_DELETE => '删除', DATA_DISABLE => '禁用', DATA_NORMAL => '启用', 2 => '邮箱认证', 3 => '手机认证', 4 => '认证', 5 => '邮箱手机认证'];

        return $arr[$status];

    }
}
