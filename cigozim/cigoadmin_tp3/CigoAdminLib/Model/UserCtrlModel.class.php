<?php
namespace CigoAdminLib\Model;

use CigoAdminLib\Lib\CigoCommon;
use Think\Model;

abstract class UserCtrlModel extends Model
{
    abstract function saveUserInfoToSession($userInfo = array());

    abstract function doLogIn($userInfo);

    protected function tipLogSuccess()
    {
        return array(
            CigoCommon::DATA_TAG_STATUS => TRUE,
	        CigoCommon::DATA_TAG_INFO => '登陆成功，稍后跳转...'
        );
    }

    protected function tipLogPwdError()
    {
        return array(
	        CigoCommon::DATA_TAG_STATUS => FALSE,
	        CigoCommon::DATA_TAG_INFO => '密码错误！'
        );
    }

	protected function tipLogUserNotExist()
	{
		return array(
			CigoCommon::DATA_TAG_STATUS => FALSE,
			CigoCommon::DATA_TAG_INFO => '用户不存在！'
		);
	}

	protected function tipLogUserForbidden()
	{
		return array(
			CigoCommon::DATA_TAG_STATUS => FALSE,
			CigoCommon::DATA_TAG_INFO => '用户被禁止登陆！'
		);
	}
}
