<?php

namespace App\Model;

/**
 * 微信用户模型.
 */
class WxUser extends \App\Component\BaseModel
{
    public $primary = 'userId';
    /**
     * 表名.
     *
     * @var string
     */
    public $table = 'wx_user';
    /**
     * 查询用户列表
     * @return mixed
     */
    public function getUserList($params = [])
    {
        $params['leftjoin'] = ['wx_user_group', "{$this->table}.groupId = wx_user_group.wxGroupId"];

        $list = $this->gets($params);
        return $list;
    }
}
