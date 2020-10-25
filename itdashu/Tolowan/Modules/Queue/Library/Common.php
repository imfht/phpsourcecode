<?php
namespace Modules\Queue\Library;

use Core\Config;
use Core\Fun;
use Core\Options;
use Modules\Queue\Models\Queue as Mqueue;

class Common
{
    /**
     * @param $type 任务类型
     * @param $data 任务需要传递的数据
     * @param $widget 任务优先级别
     */
    public static function put($type, $data, $time = null, $widget = 10, $start = true)
    {
        global $di;
        $state = false;
        $queueType = Config::cache('queueType');
        if (!isset($queueType[$type])) {
            return false;
        }
        if ($widget == null) {
            $widget = 10;
        }
        $queueModel = new Mqueue();
        $queueModel->type = $type;
        $queueModel->data = $data;
        $queueModel->widget = $widget;
        $queueModel->state = 0;
        $queueModel->queue_date = !is_null($time) ? $time : time();
        $queueModel->created = time();
        if ($queueModel->save()) {
            $di->getShared('flash')->success($queueType[$type]['name'] . '任务添加成功');
            $state = true;
        } else {
            $di->getShared('flash')->error($queueType[$type]['name'] . '任务添加失败');
            $state = false;
        }
        if ($start == true) {
            self::start();
        } else {
            $di->getShared('flash')->notice('任务没有立即运行');
        }
    }
    /**
     * 返回queue运行状态
     * 0 关闭状态
     * 1 开启运行状态
     * 2 开启状态，但是没有运行任务
     */
    public static function state()
    {
        $state = Options::get('queueState');
        $state = intval($state);
        return $state;
    }
    public static function start($id = null)
    {
        global $di;
        if ($id == null) {
            $queueState = Options::get('queueState', 0);
            //$di->getShared('flash')->success('执行队列中');
            if ($queueState == 0) {
                $di->getShared('flash')->success('http://' . $di->getShared('request')->getHttpHost() . $di->getShared('url')->get(array(
                    'for' => 'queue',
                )));
                Fun::ajaxGet('http://' . $di->getShared('request')->getHttpHost() . $di->getShared('url')->get(array(
                    'for' => 'queue',
                )));
            }
        }
    }
}
