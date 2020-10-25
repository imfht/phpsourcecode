<?php

namespace CigoAdminLib\Logic;

use CigoAdminLib\Lib\CigoCommon;
use CigoAdminLib\Lib\UserCtrl;

abstract class UserCtrlLogic implements UserCtrl
{
    const DATA_TAG_USERNAME = "username";
    const DATA_TAG_PASSWORD = "password";
    const DATA_TAG_ID = "id";
    const DATA_TAG_NICKNAME = "nickname";
    const DATA_TAG_USERINFO = "_userinfo";
    const DATA_TAG_LOGTIME = "_logtime";

    /**
     * API调用模型实例
     *
     * @access protected
     * @var object
     */
    protected $model;

    /**
     * 检查是否登陆
     */
    public function isLogIn()
    {
        if (!session(MODULE_NAME . UserCtrlLogic::DATA_TAG_USERINFO)) {
            return array(
                CigoCommon::DATA_TAG_STATUS => FALSE
            );
        } else {
            //判断是否过期
            if (!$this->checkIfTimeOut()) {
                return array(
                    CigoCommon::DATA_TAG_STATUS => FALSE
                );
            }
            //更新登陆时间
            session(MODULE_NAME . UserCtrlLogic::DATA_TAG_LOGTIME, time());

            //返回用户信息
            return $this->getSessionSavedUserInfo();
        }
    }

    protected abstract function checkIfTimeOut();

    protected function getSessionSavedUserInfo()
    {
        $logUserInfo = session(MODULE_NAME . UserCtrlLogic::DATA_TAG_USERINFO);
        return array(
            CigoCommon::DATA_TAG_STATUS => TRUE,
            CigoCommon::DATA_TAG_INFO => array(
                UserCtrlLogic::DATA_TAG_USERINFO => array(
                    UserCtrlLogic::DATA_TAG_ID => $logUserInfo[UserCtrlLogic::DATA_TAG_ID],
                    UserCtrlLogic::DATA_TAG_NICKNAME => $logUserInfo[UserCtrlLogic::DATA_TAG_NICKNAME]
                )
            )
        );
    }

    protected function clearUserInfo()
    {
        session(MODULE_NAME . UserCtrlLogic::DATA_TAG_USERINFO, NULL);
    }

    protected function tipLogOutSuccess()
    {
        return array(
            CigoCommon::DATA_TAG_STATUS => TRUE,
            CigoCommon::DATA_TAG_INFO => '退出成功！'
        );
    }

    /**
     * 执行登陆操作
     *
     * @param array $userInfo 用户信息
     *
     * @return array
     */
    public function doLogIn($userInfo)
    {
        return $this->model->doLogIn($userInfo);
    }

    /**
     * 执行退出登陆操作
     */
    public function doLogOut()
    {
        $this->clearUserInfo();
        return $this->tipLogOutSuccess();
    }
}
