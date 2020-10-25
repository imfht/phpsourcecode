<?php
namespace WxSDK\core\common;

use WxSDK\core\model\AppModel;

interface IApp
{
    /**
     * @return Ret
     */
    function getAccessToken();
    function saveAccessToken();

    /**
     * @return AppModel
     */
    function getModel();
    /**
     * @return string IApp中getApp的参数
     */
    function getId();

}