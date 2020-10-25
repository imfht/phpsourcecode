<?php
namespace App\Tasks;

use App\Module\Chat;
use Hhxsv5\LaravelS\Swoole\Task\Task;

class PushTask extends Task
{
    private $lists;

    /**
     * PushTask constructor.
     * @param array $lists      [fd, msg]
     */
    public function __construct($lists)
    {
        $this->lists = $lists;
    }

    public function handle()
    {
        $swoole = app('swoole');
        foreach ($this->lists AS $item) {
            $swoole->push($item['fd'], Chat::formatMsgSend($item['msg']));
        }
    }
}
