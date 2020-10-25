<?php
namespace Modules\Queue\Controllers;

use Core\Config;
use Core\Mvc\Controller;
use Core\Mvc\ModelQuery;
use Modules\Queue\Models\Queue;
use Modules\Queue\Library\Queue as QueueLibrary;

class AdminController extends Controller
{

    public function indexAction()
    {
        extract($this->variables['router_params']);
        QueueLibrary::start();
        $nodeQuery = array(
            'match' => array(
                array(
                    'conditions' => 'MATCH(%body%) AGAINST(:word:)',
                    'bind' => array('word' => 'Ajax \'')
                )
            )
        );
        $this->variables = array_merge($this->variables, array(
            'title' => '任务列表',
            'description' => '',
            'params' => $this->variables['router_params'],
            'breadcrumb' => array(
                'admin' => array(
                    'href' => array(
                        'for' => 'adminIndex',
                    ),
                    'name' => '控制台',
                ),
                'queueList' => array(
                    'name' => '任务列表',
                ),
            ),
            'content' => array(),
        ));
        $query = array(
            'from' => array('id' => 'queue'),
            'limit' => 30,
            'page' => $page,
            'paginator' => true,
        );
        $data = ModelQuery::find($query);
        $content['menuGroup'] = array(
            '#templates' => 'queueMenuGroup',
            'title' => '菜单',
            'data' => array(
                'runCron' => array(
                    'href' => array(
                        'for' => 'queue',
                        'id' => 0,
                    ),
                    'name' => '运行Cron'
                )
            ),
        );
        $content['queueList'] = array(
            '#templates' => 'box',
            'wrapper' => true,
            'title' => '队列列表',
            'max' => false,
            'color' => 'success',
            'size' => '12',
            'content' => array(
                'menuGroup' => array(),
                'list' => array(
                    '#templates' => array(
                        'adminQueueList',
                    ),
                    'stateInfo' => array(1 => '待执行', 2 => '执行错误'),
                    'typeInfo' => array(1 => '普通任务', 2 => '日常循环任务'),
                    'data' => $data,
                ),
            ),
        );
        $this->variables['content'] += $content;
    }

    protected function _filterForm($query, $form)
    {
        $data = $form->getData();
        foreach ($data as $key => $value) {
            $query['andWhere'][] = array(
                'conditions' => "$key = :$key:",
                'bind' => array($key => $value)
            );
        }
        return $query;
    }

    public function deleteAction()
    {
        extract($this->variables['router_params']);
        $queue = Queue::findFirst($id);
        if ($queue) {
            if ($queue->delete()) {
                $this->flash->success('删除成功');
            } else {
                $this->flash->error('删除失败');
            }
        } else {
            $this->flash->error('删除失败，数据不存在');
        }
        return $this->temMoved(array('for' => 'adminQueue', 'page' => 1));
    }

    public function progressAction()
    {
        extract($this->variables['router_params']);
        $queue = new Queue();
        if ($name == 'all') {
            $progress = $queue->progress();
        } else {
            $progress = $queue->progress($name);
        }
        $this->variables += array(
            'title' => '进度列表',
            '#templates' => 'progress',
            'data' => $progress,
        );
    }
}
