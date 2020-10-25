<?php
/**
 * Description...
 * Created by PhpStorm.
 * User: zsw
 * Date: 2018/3/28
 * Time: 13:12
 */

namespace Bjask\Queue;


interface QueueHandlerInterface
{
    public function createConnection(string $topic_name);

    public function push(string $messgae);

    public function pop();

    public function len();

    public function isConnected();

    public function close();
}