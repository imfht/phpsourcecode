<?php

/**
 * message模块的默认控制器
 *
 * @version $Id$
 * @package application.modules.main.controllers
 */

namespace application\modules\message\controllers;

use application\core\controllers\Controller;
use application\core\model\Module;
use application\core\utils\Ibos;
use application\modules\message\model\UserData;

class BaseController extends Controller
{

    /**
     * 通用获取sidebar函数
     * @param array $data 视图赋值
     * @return string 视图html
     */
    public function getSidebar($data = array())
    {
        $data['unreadMap'] = $this->getUnreadCount();
        $sidebarAlias = 'application.modules.message.views.sidebar';
        $sidebarView = $this->renderPartial($sidebarAlias, $data, true);
        return $sidebarView;
    }

    /**
     * 获取sidebar栏目的气泡提示
     * @return array
     */
    private function getUnreadCount()
    {
        $unreadCount = UserData::model()->getUnreadCount(Ibos::app()->user->uid);
        $sidebarUnreadMap['mention'] = $unreadCount['unread_atme'];
        $sidebarUnreadMap['comment'] = $unreadCount['unread_comment'];
        $sidebarUnreadMap['notify'] = $unreadCount['unread_notify'];
        $sidebarUnreadMap['pm'] = $unreadCount['unread_message'];
        return $sidebarUnreadMap;
    }

    /**
     * 通用获取所属模块函数
     * @param array $data 视图赋值
     * @return string 视图html
     */
    public function getModulebar($data = array())
    {
        $tag = empty($data['tag']) ? '' : $data['tag'];
        $data['modulelist'] = $this->getModuleList($tag);
        $sidebarAlias = 'application.modules.message.views.modulebar';
        $sidebarView = $this->renderPartial($sidebarAlias, $data, true);
        return $sidebarView;
    }

    /*
     * 获得modulelist的数据，用于导航栏
     */
    public function getModuleList($tag)
    {
        switch ($tag)
        {
            case 'notify':
                $moduleList = $this->getNotifyModuleList();
                break;
            case 'notifyManage':
                $moduleList = $this->getNotifyManageModuleList();
                break;
            default:
                $moduleList = array();
                break;
        }
        return $moduleList;
    }

    /**
     * 提醒管理的模块筛选
     */
    private function getNotifyManageModuleList()
    {
        $modules = array(
                'message',
                'crm',
                'workflow',
                'activity',
                'thread',
                'meeting',
                'assets',
                'vote',
                'assignment');
        $modulelist = array();
        $notCoreModule = Module::model()->fetchAllNotCoreModule();
        foreach ($notCoreModule as $key => $val) {
            if(in_array($key, $modules)){
                $modulelist[$key] = $val['name'];
            }
        }
        $modulelist['message'] = Ibos::lang("Message Module List another name");

        return $modulelist;
    }

    /**
     * 获得提醒列表模块分类
     * @return mixed
     */
    private function getNotifyModuleList()
    {
        $removerModule = array('app');
        $orderList = array('workflow','crm','article','diary','email'); // 需要调整顺讯的模块,最后的排最前
        $notCoreModule = Module::model()->fetchAllNotCoreModule();
        $sortList = array();
        foreach ($notCoreModule as $key => $val) {
            if(in_array($key, $removerModule)){
                continue;
            }
            $modulelist[$key] = $val['name'];
        }

        foreach ($orderList as $val) {
            if(!empty($modulelist[$val])){
                $sortList[$val] = $modulelist[$val];
                unset($modulelist[$val]);
            }
        }

        $modulelist['message'] = Ibos::lang("Message Module List another name");
        $modulelist = array_merge($sortList, $modulelist);
        return $modulelist;
    }
}
