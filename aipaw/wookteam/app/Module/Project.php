<?php

namespace App\Module;

use DB;

/**
 * Class Project
 * @package App\Module
 */
class Project
{
    /**
     * 是否在项目里
     * @param int $projectid
     * @param string $username
     * @param bool $isowner
     * @return array
     */
    public static function inThe($projectid, $username, $isowner = false)
    {
        $whereArray = [
            'type' => '成员',
            'projectid' => $projectid,
            'username' => $username,
        ];
        if ($isowner) {
            $whereArray['isowner'] = 1;
        }
        $row = Base::DBC2A(DB::table('project_users')->select(['isowner', 'indate'])->where($whereArray)->first());
        if (empty($row)) {
            return Base::retError('你不在项目成员内！');
        } else {
            return Base::retSuccess('你在项目内', $row);
        }
    }

    /**
     * 更新项目（complete、unfinished）
     * @param int $projectid
     */
    public static function updateNum($projectid)
    {
        if ($projectid > 0) {
            DB::table('project_lists')->where('id', $projectid)->update([
                'unfinished' => DB::table('project_task')->where('projectid', $projectid)->where('complete', 0)->where('delete', 0)->count(),
                'complete' => DB::table('project_task')->where('projectid', $projectid)->where('complete', 1)->where('delete', 0)->count(),
            ]);
        }
    }

    /**
     * 任务负责人组
     * @param $task
     * @return array
     */
    public static function taskPersons($task)
    {
        $array = [];
        $array[] = Users::username2basic($task['username']);
        $persons = [$task['username']];
        $subtask = Base::string2array($task['subtask']);
        foreach ($subtask AS $item) {
            if ($item['uname'] && !in_array($item['uname'], $persons)) {
                $persons[] = $item['uname'];
                $basic = Users::username2basic($item['uname']);
                if ($basic) {
                    $array[] = $basic;
                }
            }
        }
        return $array;
    }

    /**
     * 是否负责人（任务负责人、子任务负责人）
     * @param $task
     * @param $username
     * @return bool
     */
    public static function isPersons($task, $username)
    {
        $persons = [$task['username']];
        $subtask = Base::string2array($task['subtask']);
        foreach ($subtask AS $item) {
            if ($item['uname'] && !in_array($item['uname'], $persons)) {
                $persons[] = $item['uname'];
            }
        }
        return in_array($username, $persons) ? true : false;
    }

    /**
     * 任务是否过期
     * @param array $task
     * @return int
     */
    public static function taskIsOverdue($task)
    {
        return $task['complete'] == 0 && $task['enddate'] > 0 && $task['enddate'] <= Base::time() ? 1 : 0;
    }

    /**
     * 过期的排在前
     * @param array $taskLists
     * @return mixed
     */
    public static function sortTask($taskLists)
    {
        $inOrder = [];
        foreach ($taskLists as $key => $oitem) {
            $inOrder[$key] = $oitem['overdue'] ? -1 : $key;
        }
        array_multisort($inOrder, SORT_ASC, $taskLists);
        return $taskLists;
    }

    /**
     * 获取与任务有关系的用户（关注的、在项目里的、负责人、创建者）
     * @param $taskId
     * @return array
     */
    public static function taskSomeUsers($taskId)
    {
        $taskDeatil = Base::DBC2A(DB::table('project_task')->select(['follower', 'subtask', 'createuser', 'username', 'projectid'])->where('id', $taskId)->first());
        if (empty($taskDeatil)) {
            return [];
        }
        //关注的用户
        $userArray = Base::string2array($taskDeatil['follower']);
        //子任务负责人
        $subtask = Base::string2array($taskDeatil['subtask']);
        foreach ($subtask AS $item) {
            $userArray[] = $item['uname'];
        }
        //创建者
        $userArray[] = $taskDeatil['createuser'];
        //负责人
        $userArray[] = $taskDeatil['username'];
        //在项目里的用户
        if ($taskDeatil['projectid'] > 0) {
            $tempLists = Base::DBC2A(DB::table('project_users')->select(['username'])->where(['projectid' => $taskDeatil['projectid'], 'type' => '成员' ])->get());
            foreach ($tempLists AS $item) {
                $userArray[] = $item['username'];
            }
        }
        //
        return array_values(array_filter(array_unique($userArray)));
    }

    /**
     * 项目（任务）权限
     * @param $type
     * @param $projectid
     * @param int $taskid
     * @param string $username
     * @return array|mixed
     */
    public static function role($type, $projectid, $taskid = 0, $username = '')
    {
        if (empty($username)) {
            $user = Users::authE();
            if (Base::isError($user)) {
                return $user;
            } else {
                $user = $user['data'];
            }
            $username = $user['username'];
        }
        //
        $project = Base::DBC2A(DB::table('project_lists')->select(['username', 'title', 'setting'])->where('id', $projectid)->where('delete', 0)->first());
        if (empty($project)) {
            return Base::retError('项目不存在或已被删除！');
        }
        // 项目负责人最高权限
        if ($project['username'] == $username) {
            unset($project['setting']);
            return Base::retSuccess('success', $project);
        }
        //
        $setting = Base::string2array($project['setting']);
        foreach (['edit_role', 'complete_role', 'archived_role', 'del_role'] AS $key) {
            $setting[$key] = is_array($setting[$key]) ? $setting[$key] : ['__', 'owner'];
        }
        $setting['add_role'] = is_array($setting['add_role']) ? $setting['add_role'] : ['__', 'member'];
        //
        $role = $setting[$type];
        if (empty($role) || !is_array($role)) {
            return Base::retError('操作权限不足！');
        }
        if (in_array('member', $role)) {
            $inRes = Project::inThe($projectid, $username);
            if (Base::isError($inRes)) {
                return $inRes;
            }
        } elseif (in_array('owner', $role)) {
            if (empty($taskid)) {
                return Base::retError('任务不存在！');
            }
            $task = Base::DBC2A(DB::table('project_task')
                ->select(['username'])
                ->where([
                    ['delete', '=', 0],
                    ['id', '=', $taskid],
                ])
                ->first());
            if (empty($task)) {
                return Base::retError('任务不存在！');
            }
            if ($task['username'] != $username) {
                return Base::retError('此操作只允许项目管理员或者任务负责人！');
            }
        } else {
            return Base::retError('此操作仅限项目负责人！');
        }
        //
        unset($project['setting']);
        return Base::retSuccess('success', $project);
    }
}
