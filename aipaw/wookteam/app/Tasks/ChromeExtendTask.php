<?php
namespace App\Tasks;

@error_reporting(E_ALL & ~E_NOTICE);

use App\Module\Base;
use App\Module\Chat;
use DB;
use Hhxsv5\LaravelS\Swoole\Task\Task;

class ChromeExtendTask extends Task
{
    private $username;

    /**
     * ChromeExtendTask constructor.
     * @param $username
     */
    public function __construct($username)
    {
        $this->username = $username;
    }

    public function handle()
    {
        $lists = Base::DBC2A(DB::table('ws')->select(['fd', 'username', 'channel'])->where([
            'username' => $this->username,
            'channel' => 'chromeExtend',
        ])->where([
            ['update', '>', time() - 600],
        ])->get());
        if (count($lists) > 0) {
            $unread = intval(DB::table('chat_dialog')->where('user1', $this->username)->sum('unread1'));
            $unread+= intval(DB::table('chat_dialog')->where('user2', $this->username)->sum('unread2'));
            //
            $swoole = app('swoole');
            foreach ($lists AS $item) {
                $swoole->push($item['fd'], Chat::formatMsgSend([
                    'messageType' => 'unread',
                    'body' => [
                        'unread' => $unread
                    ],
                ]));
            }
        }
    }
}
