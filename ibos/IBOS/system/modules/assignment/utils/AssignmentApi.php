<?php

namespace application\modules\assignment\utils;

use application\core\utils\Ibos;
use application\modules\assignment\model\Assignment;
use application\modules\assignment\utils\Assignment as AssignmentUtil;
use application\modules\message\utils\MessageApi;

class AssignmentApi extends MessageApi
{

    private $_indexTab = array('today', 'tomorrow');

    /**
     * 提供给接口的模块首页配置方法
     * @return array
     */
    public function loadSetting()
    {
        return array(
            'name' => 'assignment/assignment',
            'title' => '任务指派',
            'style' => 'in-assignment',
            'tab' => array(
                array(
                    'name' => 'today',
                    'title' => '今天',
                    'icon' => 'o-ol-am-today'
                ),
                array(
                    'name' => 'tomorrow',
                    'title' => '明天',
                    'icon' => 'o-ol-am-tomorrow'
                )
            )
        );
    }

    /**
     * 渲染首页视图
     * @return array
     */
    public function renderIndex()
    {
        $viewAlias = 'application.modules.assignment.views.indexapi.assignment';
        $uid = Ibos::app()->user->uid;

        $todayData = Assignment::model()->fetchTodayAssignmentData();
        $tomorrowData = Assignment::model()->fetchTomorrowAssignmentData();
        $data = array(
            'todayData' => AssignmentUtil::handleListData($todayData, $uid),
            'tomorrowData' => AssignmentUtil::handleListData($tomorrowData, $uid),
            'lang' => Ibos::getLangSource('assignment.default'),
            'assetUrl' => Ibos::app()->assetManager->getAssetsUrl('assignment')
        );
        foreach ($this->_indexTab as $tab) {
            $data['tab'] = $tab;
            $data[$tab] = Ibos::app()->getController()->renderPartial($viewAlias, $data, true);
        }
        return $data;
    }

    /**
     * 获取最新任务数
     * @return integer
     */
    public function loadNew()
    {
        $uid = Ibos::app()->user->uid;
        return Assignment::model()->getUnfinishCountByUid($uid);
    }

}
