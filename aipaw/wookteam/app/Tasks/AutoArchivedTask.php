<?php
namespace App\Tasks;

@error_reporting(E_ALL & ~E_NOTICE);

use App\Module\Base;
use App\Module\Chat;
use DB;
use Hhxsv5\LaravelS\Swoole\Task\Task;

/**
 * 完成的任务自动归档
 * Class AutoArchivedTask
 * @package App\Tasks
 */
class AutoArchivedTask extends Task
{

    public function __construct()
    {
        //
    }

    public function handle()
    {
        $setting = Base::setting('system');
        if ($setting['autoArchived'] === 'open') {
            $archivedDay = intval($setting['archivedDay']);
            if ($archivedDay > 0) {
                $time = time();
                $archivedDay = min(100, $archivedDay);
                $archivedTime = $time - ($archivedDay * 86400);
                //获取已完成未归档的任务
                DB::transaction(function () use ($time, $archivedTime) {
                    $taskLists = Base::DBC2A(DB::table('project_task')->where([
                        ['delete', '=', 0],
                        ['archiveddate', '=', 0],
                        ['complete', '=', 1],
                        ['completedate', '<=', $archivedTime],
                    ])->take(100)->get());
                    if ($taskLists) {
                        $idArray = [];
                        $logArray = [];
                        $pushLists = [];
                        $upArray = [
                            'archived' => 1,
                            'archiveddate' => $time,
                        ];
                        foreach ($taskLists AS $taskDetail) {
                            $idArray[] = $taskDetail['id'];
                            $logArray[] = [
                                'type' => '日志',
                                'projectid' => $taskDetail['projectid'],
                                'taskid' => $taskDetail['id'],
                                'username' => $taskDetail['username'],
                                'detail' => '任务归档【自动】',
                                'indate' => $time,
                                'other' => Base::array2string([
                                    'type' => 'task',
                                    'id' => $taskDetail['id'],
                                    'title' => $taskDetail['title'],
                                ])
                            ];
                            $userLists = Chat::getTaskUsers($taskDetail['id']);
                            if ($userLists) {
                                foreach ($userLists as $user) {
                                    $pushLists[] = [
                                        'fd' => $user['fd'],
                                        'msg' => [
                                            'messageType' => 'user',
                                            'username' => '::system',
                                            'target' => $user['username'],
                                            'time' => $time,
                                            'body' => [
                                                'act' => 'archived',
                                                'type' => 'taskA',
                                                'taskDetail' => array_merge($taskDetail, $upArray),
                                            ]
                                        ]
                                    ];
                                }
                            }
                        }
                        if ($idArray) {
                            DB::table('project_task')->whereIn('id', $idArray)->where([
                                ['archiveddate', '=', 0],
                                ['complete', '=', 1],
                            ])->update($upArray);
                        }
                        if ($logArray) {
                            DB::table('project_log')->insert($logArray);
                        }
                        if ($pushLists) {
                            $pushTask = new PushTask($pushLists);
                            Task::deliver($pushTask);
                        }
                    }
                });
            }
        }
    }
}
