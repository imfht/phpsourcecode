<?php
namespace app\member\controller;

use app\common\controller\MemberBase;


class Friend extends MemberBase
{
    /**
     * 粉丝好友
     * @return mixed|string
     */
    public function index()
    {
        return $this->fetch();
    }
}
