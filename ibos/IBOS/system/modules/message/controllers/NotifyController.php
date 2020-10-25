<?php

/**
 * 消息模块通知中心控制器文件
 *
 * @author banyanCheung <banyan@ibos.com.cn>
 * @link http://www.ibos.com.cn/
 * @copyright Copyright &copy; 2012-2013 IBOS Inc
 */
/**
 * 消息模块通知中心控制器
 * @package application.modules.message.controllers
 * @author banyanCheung <banyan@ibos.com.cn>
 * @version $Id$
 */

namespace application\modules\message\controllers;

use application\core\model\Module;
use application\core\utils\Ibos;
use application\core\utils\Env;
use application\core\utils\Page;
use application\core\utils\StringUtil;
use application\modules\message\model\NotifyMessage;

class NotifyController extends BaseController
{

    /**
     * 列表页
     * @return void
     */
    public function actionIndex()
    {
        $uid = Ibos::app()->user->uid;
        $module = StringUtil::SQLfilter(StringUtil::xssFilter(Env::getRequest('module')));
        $isread = (int) Env::getRequest('isread');
        $search = StringUtil::xssFilter(StringUtil::SQLfilter(Env::getRequest('search')));
        $unreadCount = NotifyMessage::model()->fetchPageCountByUidAndModuleAndIsreadAndSearch($uid, $module, 0, $search);
        $readCount = NotifyMessage::model()->fetchPageCountByUidAndModuleAndIsreadAndSearch($uid, $module, 1, $search);
        $pageCount = $isread === 0 ? $unreadCount : $readCount;
        $pages = Page::create($pageCount);
        $list = NotifyMessage::model()->fetchAllNotifyListByUidAndModule($uid, 'ctime DESC', $pages->getLimit(), $pages->getOffset(), $module, $isread, $search);
        $data = array(
            'list' => $list,
            'pages' => $pages,
            'unreadCount' => $unreadCount,
            'readCount' => $readCount,
            'modulelist' => $this->getModuleList('notify'),
            'isread' => $isread,
            'module' => $module,
            'search' => $search,
            'allmodule'=> Module::model()->fetchAllEnabledModule(),
        );
        $this->setPageTitle(Ibos::lang('Notify'));
        $this->setPageState('breadCrumbs', array(
            array('name' => Ibos::lang('Message center'), 'url' => $this->createUrl('mention/index')),
            array('name' => Ibos::lang('Notify'))
        ));
        $this->render('index', $data);
    }

    /**
     * 删除操作
     * @return void
     */
    public function actionDelete()
    {
        $op = Env::getRequest('op');
        if (!in_array($op, array('id', 'module'))) {
            $op = 'id';
        }
        $res = NotifyMessage::model()->deleteNotify(Env::getRequest('id'), $op);
        $this->ajaxReturn(array('IsSuccess' => !!$res));
    }

    /**
     * 设置当前用户所有未读提醒为已读
     * @return void
     */
    public function actionSetAllRead()
    {
        if (Ibos::app()->request->isAjaxRequest) {
            $uid = Ibos::app()->user->uid;
            $res = NotifyMessage::model()->setRead($uid);
            $this->ajaxReturn(array('IsSuccess' => !!$res));
        }
    }

    /**
     * 设置列表提醒为已读
     * @return void
     */
    public function actionSetIsRead()
    {
        $ids = StringUtil::filterCleanHtml(Env::getRequest('id'));
        $res = NotifyMessage::model()->setReadByIdx(Ibos::app()->user->uid, $ids);
        $this->ajaxReturn(array('IsSuccess' => !!$res));
    }

    /**
     * 收到的赞
     * @return void
     */
    public function actionDigg()
    {
        $this->setPageTitle(Ibos::lang('My digg'));
        $this->setPageState('breadCrumbs', array(
            array('name' => Ibos::lang('Message center'), 'url' => $this->createUrl('mention/index')),
            array('name' => Ibos::lang('Notify'), 'url' => $this->createUrl('notify/index')),
            array('name' => Ibos::lang('My digg'))
        ));
        $this->render('digg');
    }

    /**
     * 提醒跳转统一连接
     */
    public function actionJump()
    {
        $url = $this->createUrl('notify/index');
        $id = intval(Env::getRequest('id'));
        $uid = Ibos::app()->user->uid;
        $messageData = NotifyMessage::model()->fetchByPk($id);
        if (!empty($messageData) && $uid == $messageData['uid']){
            NotifyMessage::model()->updateAll(array('isread' => 1), "id = :id", array(':id' => $id));
            $url = $messageData['url'];
        }
        $this->redirect($url);
    }

}
