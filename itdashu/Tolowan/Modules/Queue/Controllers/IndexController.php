<?php
namespace Modules\Queue\Controllers;

use Core\Config;
use Modules\Queue\Library\Queue;
use Core\Mvc\Controller;
use Modules\Queue\Models\Queue as QueueModel;
use Modules\Queue\Models\QueueLog;

class IndexController extends Controller
{

    public function indexAction()
    {
        extract($this->variables['router_params']);
        ini_set('disable_functions', '');//开启所有函数
        ignore_user_abort(true); // 后台运行
        set_time_limit(0); // 取消脚本运行时间的超时上限
        sleep(1);
        $this->view->disable();
        if($id == 0){
            echo '运行全部任务';
            Queue::runAllCron();
            exit(0);
        }else{
            $queryType = Config::cache('queryType');
            $cron = QueueModel::findFirst($id);
            $output = false;
            $data = @unserialize($cron->data);
            if ($data) {
                if (isset($data['callable']) && isset($queryType[$data['callable']])) {
                    if (isset($data['params'])) {
                        $output = call_user_func($queryType[$data['callable']]['callable'], $data['data']);
                    }else{
                        $output = call_user_func($data['callable']);
                    }
                }
            }
            if($output === true){
                $cron->delete();
            }else{
                $cron->error = is_string($output) ? $output : serialize($output);
                $cron->state = 2;
                $cron->save();
            }
            @unlink(CACHE_DIR . 'queue/lock'.$id);
        }
    }
    public function progressAction(){
        extract($this->variables['router_params']);
        $queueLog = QueueLog::findFirstByQid($id);
        $this->response->setHeader("Content-Type", "application/json");
        $this->variables['#templates'] = 'json';
        $this->variables['data'] = json_encode($queueLog->toArray());
    }
}
