<?php
namespace Framework;

/**
 * 异步任务执行抽象类
 *
 * @package Framework
 * @author kelezyb
 * @version 0.9.0.1
 */
abstract class SZTask {
    /**
     * 任务执行
     * @param mixed $params
     */
    public function run($params) {
        $result = $this->execute($params);

        SZServer::Instance()->finishTask($result);
    }

    /**
     * 任务结束
     * @param mixed $data
     */
    public function end($data) {
        $this->finish($data);
    }

    /**
     * 任务执行重载部分
     * @param mixed $params
     * @return mixed
     */
    abstract public function execute($params);

    /**
     * 任务执行完成回调
     * @param mixed $params
     * @return mixed
     */
    abstract public function finish($params);
}