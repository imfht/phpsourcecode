<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 14-12-11
 * Time: 下午2:41
 */

namespace Libraries\ServiceProvider;

use App;
use Event;
use Illuminate\Support\ServiceProvider;
use Libraries\EventHandler\ProjectEventHandler;
use Libraries\EventHandler\TaskEventHandler;
use Libraries\EventHandler\ProjectDiscussionEventHandler;
use Libraries\EventHandler\ProjectSharingEventHandler;

/**
 * 事件服务提供者，用于注册事件订阅者
 */
class EventServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->bindEventHandlers();
        $this->subscribeHandlers();
    }

    /**
     * 注册事件订阅者
     */
    private function subscribeHandlers()
    {
        foreach ( $this->eventHandlers as $currentHandler ) {
            Event::subscribe($currentHandler);
        }
    }

    /**
     * 将事件订阅者注入到Laravel的IoC容器
     */
    private function bindEventHandlers()
    {
        App::bind('ProjectEventHandler', function(){
            return new ProjectEventHandler();
        });

        App::bind('TaskEventHandler', function(){
            return new TaskEventHandler();
        });

        App::bind('ProjectDiscussionEventHandler' ,function(){
            return new ProjectDiscussionEventHandler();
        });

        App::bind('ProjectSharingEventHandler' ,function(){
            return new ProjectSharingEventHandler();
        });


    }

    //存储事件订阅者的名称
    private $eventHandlers = [
        'ProjectEventHandler',
        'TaskEventHandler',
        'ProjectDiscussionEventHandler',
        'ProjectSharingEventHandler'
    ];
}
