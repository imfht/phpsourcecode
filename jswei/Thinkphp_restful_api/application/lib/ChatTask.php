<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2019/3/3
 * Time: 12:13
 */
namespace app\lib;

use think\swoole\template\Task;

class ChatTask extends Task {

    public function __construct(array $args){
        parent::__construct($args);
    }

    public function initialize($arg){ }

    public function run($server, $task_id, $fromWorkerId){
        // TODO: Implement run() method.
    }
}