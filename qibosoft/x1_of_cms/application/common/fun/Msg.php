<?php
namespace app\common\fun;

use plugins\msgtask\model\Task AS TaskModel;
use plugins\msgtask\model\Log as LogModel;

/**
 * 群发消息
 */
class Msg{
    
    /**
     * 定时群发消息
     * @param number $uid 接收者的UID,也可以是多个,比如[55,225.65]
     * @param string $title 信息标题
     * @param string $content 信息内容
     * @param array $array 扩展信息数据,
     * time指定多少秒后发布,也可以指定未来的时间,
     * msgtype 发送消息类型,同时发送多种消息有逗号隔开,比如msg,wxmsg,phone,mail
     * ext_id ext_sys指定主题的ID及模型,可避免重复插入任务.可忽略,但不建议
     * @return string|boolean
     */
    public static function send($uid=0,$title='',$content='',$array=[]){
        $task_file = RUNTIME_PATH.'Task.txt';
        $task_web_file = RUNTIME_PATH.'Task_web.txt';
        if (!plugins_config('msgtask')) {
            return '系统没有安装 定时群发消息 插件';
        }elseif( time()-filemtime($task_file)>1800 && time()-filemtime($task_web_file)>1800  ){
            return '定时任务没有启动';
        }
        $time = $array['time']?:0;
        $sncode = $array['sncode']?:'';
        $msgtype = $array['msgtype']?:'msg,wxmsg';
        $ext_id = $array['ext_id']?:0;
        $ext_sys = $array['ext_sys']?:0;
        
        if ($time && $time<time()) {
            $time = time()+$time;
        }
        
        $msgtype = str_replace(['wxmsg','msg','phone','email','mail','wx','sms'], [1,0,2,3,3,1,2], $msgtype);
        
        if ($ext_id && empty($ext_sys)) {
            $ext_sys = M('id');
        }
        
        $info = [];
        if ($ext_id) {
            $map = [
                'ext_id'=>$ext_id,
                'ext_sys'=>intval($ext_sys),
            ];
            $info = TaskModel::where($map)->find();
        }
        if (empty($info)) {
            $data = [
                'title'=>$title,
                'content'=>$content,
                'sncode'=>$sncode,
                'begin_time'=>$time,
                'type'=>$msgtype,
                'ext_id'=>$ext_id,
                'ext_sys'=>$ext_sys,
            ];
            $result = TaskModel::create($data);
            if(!$result){
                return '入库失败1';
            }
            $tid = $result->id;
        }else{
            $tid = $info['id'];
        }
        
        if(is_array($uid)){
            $uid_array = $uid;
        }else{
            $uid_array = [$uid];
        }
        $_array = [];
        foreach ($uid_array AS $u){
            $_array[] = [
                'touid'=>$u,
                'tid'=>$tid,
            ];
        }
        $obj = new LogModel;
        if ($obj->saveAll($_array)) {
            return true;
        }else{
            return '入库失败2';   
        }        
    }
    
}