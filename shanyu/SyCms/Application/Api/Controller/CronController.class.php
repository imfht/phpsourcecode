<?php
namespace Api\Controller;
use Think\Controller;
class CronController extends Controller {
    private $msg='';

    public function _initialize(){
        // if( PHP_SAPI != 'cli' ){
        //  header('HTTP/1.1 404 Not Found');
        //  header('Status:404 Not Found');
        //  include('./Common/View/404.html');
        //  exit();
        // }
    }

    public function index(){
        $this->noticeMe();
        echo 'Cron index done!';
    }

    private function noticeMe(){


        $config=D('Common/Config')->getConfig();
        C($config);

        $Email=new \Common\Event\EmailEvent();
        $status=$Email->send('wf9100@qq.com','自动提醒','自动提醒内容');

        if($status) \Think\Log::record('NoticeMe任务执行成功','INFO',true);
        else \Think\Log::record('NoticeMe任务执行失败:'.$Email->error,'INFO',true);
    }

}