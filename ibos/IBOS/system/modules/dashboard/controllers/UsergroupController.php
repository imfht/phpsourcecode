<?php

namespace application\modules\dashboard\controllers;

use application\core\utils\Cache;
use application\core\utils\Env;
use application\core\utils\Ibos;
use application\modules\dashboard\utils\Dashboard;
use application\modules\user\model\User;
use application\modules\user\model\UserGroup;

class UsergroupController extends BaseController
{

    /**
     * 用户组设置
     * @return void
     */
    public function actionIndex()
    {
        
        $groups = UserGroup::model()->fetchAll(array('order' => 'creditshigher'));
        $data = array('data' => $groups);
        $this->render('index', $data);
    }

    public function actionEdit()
    {
        // 更新与添加操作
        $groups = $_POST['groups'];
        $res = $this->checkCreditshigher($groups);

        if ($res['isSuccess'] === false) {
            $this->ajaxReturn($res);
        }

        $newGroups = isset($_POST['newgroups']) ? $_POST['newgroups'] : array();
        $oldGroupsKeys = array_keys($groups);

        $res = $this->handleGroupsData($groups, $newGroups);
        if ($res['isSuccess'] === false) {
            $this->ajaxReturn($res);
        }

        $groups = $res['data'];
        foreach ($groups as $id => $group) {
            if (in_array($id, $oldGroupsKeys)) {
                UserGroup::model()->modify($id, $group);
            } elseif ($group['title'] && $group['creditshigher'] != '') {
                UserGroup::model()->add($group);
            }
        }

        //更新所有用户的用户组
        $allUsers = User::model()->fetchAll();
        foreach ($allUsers as $user){
            $groupId = UserGroup::model()->fetchByCredits($user['credits']);
            if (!empty($groupId)){
                User::model()->modify($user['uid'], array(
                    'groupid' => $groupId['gid']
                ));
            }
        }

        // 删除操作
        $removeId = $_POST['removeId'];
        if (!empty($removeId)) {
            UserGroup::model()->deleteById($removeId);
        }
        Cache::update(array('UserGroup'));
        $this->ajaxReturn(array(
            'isSuccess' => true,
            'msg' => Ibos::lang('Save succeed', 'message')
        ));
    }

    private function checkCreditshigher($groups)
    {
        $msg = '';
        $pre = -9999999999;
        foreach ($_POST['groups'] as $k => $group) {
            if (intval($group['creditshigher']) < $pre) {
                $msg = Ibos::lang('Creditshigher must be bigger than pre', '', array(
                    '{title}' => $group['title'],
                    '{pre}' => $pre,
                ));
            } else {
                $pre = intval($group['creditshigher']);
            }
        }

        return empty($msg) ? array(
            'isSuccess' => true,
            'msg' => $msg,
        ) : array(
            'isSuccess' => false,
            'msg' => $msg,
        );
    }

    private function handleGroupsData($base, $newGroups)
    {
        $pre = -9999999999;
        $max = 999999999;
        $groupNewAdd = Dashboard::arrayFlipKeys($newGroups);
        $creditList = array();
        foreach ($groupNewAdd as $k => $v) {
            if ($v['title'] && $v['creditshigher']) {
                $base[] = array(
                    'title' => \CHtml::encode($v['title']),
                    'creditshigher' => $v['creditshigher'],
                );
            }
        }

        foreach ($base as $id => $group) {
            $lower = isset($base[$id + 1]) ? $base[$id + 1]['creditshigher'] : $max;

            $base[$id]['creditslower'] = $lower;
            $creditList[] = $group['creditshigher'];
        }

        return min($creditList) >= 0 ? array(
            'isSuccess' => false,
            'msg' => Ibos::lang('Usergroups update credits invalid'),
        ) : array(
            'isSuccess' => true,
            'msg' => '',
            'data' => $base,
        );
    }
}
