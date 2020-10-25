<?php
namespace Framework;

/**
 * 控制器派发器
 *
 * @autheor kelezyb
 * @version 0.9.0.1
 */
class SZDispatcher {
    /**
     * @var array
     */
    private $tasks;

    public function __construct() {
        $this->tasks = array();
    }

    /**
     * 执行控制器
     * @param $datas
     * @return string
     * @throws \Exception
     */
    public function executeController($fd, $datas) {
        if (3 === count($datas)) {
            list($requestid, $execute, $params) = $datas;

            $execute_info = explode('.', $execute);
            $info_count = count($execute_info);
            if (2 === $info_count) {
                list($controller, $method) = $execute_info;
                $controller .= 'Controller';
                SZLogger::debug(sprintf("Call controller %s:%s", $controller, $method));
                return $this->call($fd, $controller, $method, $params);
            } else {
                throw new \Exception("controller param error.");
            }
        } else {
            throw new \Exception("pack struct error.");
        }
    }

    /**
     * @param $fd
     * @param $controller
     * @param $method
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    private function call($fd, $controller, $method, $params) {

        $className = "App\\Controllers\\$controller";
        $controllerObject = new $className($fd);

        if (method_exists($controllerObject, $method)) {
            $result = call_user_func_array(array($controllerObject, $method), $params);
        } else {
            throw new \Exception('method[{$method}] no found.');
        }

        return $result;
    }

    public function executeTask($taskId, $taskname, $pararms) {
        $taskname .= 'Task';
        $method = 'run';
        $className = "App\\Tasks\\$taskname";
        $taskObject = new $className(SZServer::Instance());
        $this->tasks[$taskId] = $taskObject;

        if (method_exists($taskObject, $method)) {
            $result = call_user_func_array(array($taskObject, $method), $pararms);
        } else {
            throw new \Exception('method[{$method}] no found.');
        }

        return $result;
    }

    public function executeTaskFinish($taskId, $data) {
        if (isset($this->tasks[$taskId])) {
            $this->tasks[$taskId]->end($data);
        }
    }
}