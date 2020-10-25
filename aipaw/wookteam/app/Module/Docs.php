<?php

namespace App\Module;

use App\Tasks\PushTask;
use Cache;
use DB;
use Hhxsv5\LaravelS\Swoole\Task\Task;

/**
 * Class Docs
 * @package App\Module
 */
class Docs
{
    /**
     * 检验是否有阅读或修改权限
     * @param $bookid
     * @param string $checkType     edit|view
     * @return array|mixed
     */
    public static function checkRole($bookid, $checkType = 'edit')
    {
        $row = Base::DBC2A(DB::table('docs_book')->where('id', $bookid)->first());
        if (empty($row)) {
            return Base::retError('知识库不存在或已被删除！', -1000);
        }
        $userE = Users::authE();
        if (Base::isError($userE)) {
            $user = [];
        } else {
            $user = $userE['data'];
        }
        $checkType = $checkType == 'edit' ? 'edit' : 'view';
        if ($checkType == 'edit') {
            if (empty($user)) {
                return $userE;
            }
        } else {
            if ($row['role_view'] != 'all') {
                if (empty($user)) {
                    return Base::retError('知识库仅对会员开放，请登录后再试！', -1001);
                }
            }
        }
        if ($user['username'] == $row['username']) {
            return Base::retSuccess('success');
        }
        //
        if ($row['role_' . $checkType] == 'member') {
            if (!DB::table('docs_users')->where('bookid', $bookid)->where('username', $user['username'])->exists()) {
                return Base::retError('知识库仅对成员开放！', $checkType == 'edit' && $row['role_look'] == 'reg' ? 1002 : -1002);
            }
        } elseif ($row['role_' . $checkType] == 'private') {
            if ($row['username'] != $user['username']) {
                return Base::retError('知识库仅对作者开放！', $checkType == 'edit' && $row['role_look'] == 'reg' ? 1003 : -1003);
            }
        }
        //
        return Base::retSuccess('success');
    }

    /**
     * 通知正在编辑的成员
     *
     * @param integer $sid      章节ID
     * @param array $bodyArray  body参数
     */
    public static function notice($sid, $bodyArray = [])
    {
        $user = Users::auth();
        $array = Base::json2array(Cache::get("docs::" . $sid));
        if ($array) {
            foreach ($array as $uname => $vbody) {
                if (intval($vbody['indate']) + 20 < time()) {
                    unset($array[$uname]);
                }
            }
        }
        $pushLists = [];
        if ($array) {
            foreach ($array AS $tuser) {
                $uLists = Base::DBC2A(DB::table('ws')->select(['fd', 'username', 'channel'])->where('username', $tuser['username'])->get());
                foreach ($uLists AS $item) {
                    if ($item['username'] == $user['username']) {
                        continue;
                    }
                    $pushLists[] = [
                        'fd' => $item['fd'],
                        'msg' => [
                            'messageType' => 'docs',
                            'body' => array_merge([
                                'sid' => $sid,
                                'nickname' => $user['nickname'] ?: $user['username'],
                                'time' => time(),
                            ], $bodyArray)
                        ]
                    ];
                }
            }
        }
        $pushTask = new PushTask($pushLists);
        Task::deliver($pushTask);
    }
}
