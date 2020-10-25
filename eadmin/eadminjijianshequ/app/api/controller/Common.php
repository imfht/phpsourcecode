<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller;

use app\api\logic\Common as logicCommon;

/**
 * 公共基础接口控制器
 */
class Common extends ApiBase
{

    /**
     * 登录接口
     */
    public function login()
    {

        return $this->apiReturn(logicCommon::login($this->param));
    }

    /**
     * 修改密码接口
     */
    public function changePassword()
    {

        return $this->apiReturn(logicCommon::changePassword($this->param));
    }

    /**
     * 上传用户信息
     */
    public function upuserinfo()
    {

        return $this->apiReturn(logicCommon::upuserinfo($this->param));
    }

    /**
     * 上传用户信息
     */
    public function uploadfile()
    {

        return $this->apiReturn(logicCommon::uploadfile($this->param));
    }

    public function getipstr()
    {

        return $this->apiReturn(logicCommon::getipstr($this->param));
    }

    public function checkweburl()
    {

        return $this->apiReturn(logicCommon::checkweburl($this->param));
    }

    public function wenjuanlist()
    {//获取问卷列表

        return $this->apiReturn(logicCommon::wenjuanlist($this->param));
    }

    public function tjwenjuan()
    {//问卷答题

        return $this->apiReturn(logicCommon::tjwenjuan($this->param));
    }

    public function wjcontent()
    {//进入问卷

        return $this->apiReturn(logicCommon::wjcontent($this->param));
    }

    public function getarticle()
    {//进入问卷

        return $this->apiReturn(logicCommon::getarticle($this->param));
    }

    public function getuserinfo()
    {//进入问卷

        return $this->apiReturn(logicCommon::getuserinfo($this->param));
    }

    public function mywenjuanlist()
    {//进入问卷

        return $this->apiReturn(logicCommon::mywenjuanlist($this->param));
    }

}
