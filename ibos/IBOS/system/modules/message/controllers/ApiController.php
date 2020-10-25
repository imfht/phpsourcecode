<?php

namespace application\modules\message\controllers;

use application\core\utils\Env;
use application\core\utils\StringUtil;
use application\core\utils\Convert;
use application\core\utils\Ibos;
use application\modules\message\model\UserData;
use application\modules\message\model\FeedDigg;
use application\modules\weibo\model\Follow;

class ApiController extends BaseController
{

    /**
     * 该控制器的action
     * @return array
     */
    public function actions()
    {
        $actions = array(
            'alarmadd' => 'application\modules\message\actions\api\AlarmAdd', // 添加
            'alarmedit' => 'application\modules\message\actions\api\AlarmEdit',// 修改
            'alarmdel' => 'application\modules\message\actions\api\AlarmDel', // 删除
            'alarmlist' => 'application\modules\message\actions\api\AlarmList', // 多个条件组合查询列表
            'alarmdetail' => 'application\modules\message\actions\api\AlarmDetail', // 单条详情
            'alarmeventtime' => 'application\modules\message\actions\api\AlarmEventTime', // 获取关联事件时间
            'alarmsetlist' => 'application\modules\message\actions\api\AlarmSetList', // 设置提醒下拉列表
        );
        return $actions;
    }

    /**
     * 轮询查询未读提醒
     * @return void
     */
    public function actionGetUnreadCount()
    {
        $this->checkLogin();
        $count = UserData::model()->getUnreadCount(Ibos::app()->user->uid);
        $data['status'] = 1;
        $data['data'] = $count;
        $this->ajaxReturn($data);
    }

    /**
     * 查找最近@ 的人
     * @return void
     */
    public function actionSearchAt()
    {
        $users = UserData::model()->fetchRecentAt(Ibos::app()->user->uid);
        $this->ajaxReturn(!empty($users) ? $users : array());
    }

    /**
     * 加载更多赞过的人列表
     */
    public function actionLoadMoreDiggUser()
    {
        $feedId = intval(Env::getRequest('feedid'));
        $offset = intval(Env::getRequest('offset'));
        $result = FeedDigg::model()->fetchUserList($feedId, 5, $offset);
        $uids = Convert::getSubByKey($result, 'uid');
        $followStates = Follow::model()->getFollowStateByFids(Ibos::app()->user->uid, $uids);
        $data['data'] = $this->renderPartial('application.modules.message.views.feed.digglistmore', array('list' => $result, 'followstates' => $followStates), true);
        $data['isSuccess'] = true;
        $this->ajaxReturn($data);
    }

    /**
     * 关注操作
     */
    public function actionDoFollow()
    {
        if (Env::submitCheck('formhash')) {
            // 安全过滤
            $fid = StringUtil::filterCleanHtml($_POST['fid']);
            $res = Follow::model()->doFollow(Ibos::app()->user->uid, intval($fid));
            // 是否互相关注
            $isFriend = $res['following'] && $res['follower'];
            $this->ajaxReturn(array('isSuccess' => !!$res, 'both' => $isFriend, 'msg' => Follow::model()->getError('doFollow')));
        }
    }

    /**
     * 取消关注
     */
    public function actionUnFollow()
    {
        if (Env::submitCheck('formhash')) {
            // 安全过滤
            $fid = StringUtil::filterCleanHtml($_POST['fid']);
            $res = Follow::model()->unFollow(Ibos::app()->user->uid, intval($fid));
            $this->ajaxReturn(array('isSuccess' => !!$res, 'msg' => Follow::model()->getError('unFollow')));
        }
    }

}
