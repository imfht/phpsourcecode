<?php
namespace app\index\controller;

use app\common\controller\IndexBase;
use think\Db;
use app\common\model\Timedtask AS TaskModel;


/**
 * 定时任务
 */
class Task extends IndexBase
{
    private static $task_file;           //后端执行记录
    private static $task_web_file;       //前台执行记录
    private static $task_cfg_file;     //记录任务是否有调整
    private static $task_cfg_time;     //记录任务是否有调整
    private static $client;
    
    protected function _initialize(){
        parent::_initialize();
        self::$task_file = RUNTIME_PATH.'Task.txt';
        self::$task_web_file = RUNTIME_PATH.'Task_web.txt';
        self::$task_cfg_file = RUNTIME_PATH.'Task_config.txt';
        if (!defined('IN_TASK')) {
            define('IN_TASK',true);
        }
    }
    

    /**
     * 前端AJAX调用
     * 非管理员只执行一次,管理员的话可以无限循环下去
     * @return void|\think\response\Json
     */
    public function index(){
        if( time()-filemtime(self::$task_file)<600 ){
            return $this->err_js('后端在执行当中，前端不重复执行！');
        }elseif( time()-filemtime(self::$task_web_file)<120 ){
            return $this->err_js('2分钟内不重复执行！');
        }
        if ($this->admin) {
            return $this->dos();    //管理员身份就执行无限循环的操作
        }
        self::$client = 'web';
        write_file(self::$task_web_file, date('Y-m-d H:i:s'));
        set_time_limit(0);
        $this->run_task();
        $this->ok_js('定时任务，执行完毕！');
    }
    
    /**
     * 命令行调用
     * 反复执行,永不停止,直到 self::$task_file 被重写才终止
     * @return string
     */
    public function dos(){
        if (php_sapi_name()!='cli' && !$this->admin) {
            return $this->err_js('无权限访问！');
        }
        chdir(ROOT_PATH);//getcwd();
        ini_set("max_execution_time", 0);
        set_time_limit(0);
        ignore_user_abort(true);
        $ck_time = date('Y-m-d H:i:s');
        write_file(self::$task_file, $ck_time);
        self::$client = 'dos';
        self::$task_cfg_time = filemtime(self::$task_cfg_file); //检查定时任务是否有调整过

        $ck = true;
        while($ck==true){
            $this->run_task();
            if (file_get_contents(self::$task_file)!=$ck_time) {
                $ck = false;
            }
            sleep(5);   //休息5秒
        }
        return '执行完毕！';
    }
    
    /**
     * 测试运行某条日志
     * @param number $id
     * @return void|unknown|\think\response\Json
     */
    public function test($id=0){
        $rs = TaskModel::where('id',$id)->find();
        if (empty($rs)) {
            return $this->err_js('ID有误');
        }
        $speed_time = explode(' ',microtime());
        $headtime = $speed_time[0] + $speed_time[1];
        
        $this->run_class($rs);
        
        $speed_time = explode(' ',microtime());
        $endtime = $speed_time[0] + $speed_time[1];
        $times = $endtime - $headtime;
        
//         TaskModel::update([
//             'id'=>$rs['id'],
//             'num'=>$rs['num']+1,
//             'last_time'=>time(),
//             'use_time'=>$times,
//         ]);
        
        return $this->ok_js([],'执行完毕,耗时'.number_format($times,4).'秒');
    }
    
    /**
     * 执行所有定时任务
     * @return number
     */
    public function run_task(){
        $taskdb = task_config();
        if (self::$client=='dos'){
            $cache_time = filemtime(self::$task_cfg_file);    //计划任务被调整过
            if (self::$task_cfg_time != $cache_time) {
                self::$task_cfg_time = $cache_time;
                $taskdb = '';
            }
        }        
        if (empty($taskdb)&&!is_array($taskdb)) {
            $taskdb = TaskModel::where('ifopen',1)->order('list desc,id desc')->column(true);
            cache('timed_task',$taskdb);
        }
        $ck = 0;
        $time_d = date('d');
        $time_w = date('w')?:7;
        foreach($taskdb AS $rs){
            if ($rs['type']=='t_once') {    //仅执行一次的情况
                if ($rs['num']>0) {
                    TaskModel::where('id',$rs['id'])->update(['ifopen'=>0]);    //直接关闭,避免再写入缓存
                    continue;
                }
                if (date('Y-m-d')!=$rs['ymd']) {
                    continue;
                }
            }elseif($rs['type']=='t_month'){    //每月哪天执行
                if ($time_d!=$rs['day']) {
                    continue;
                }
            }elseif($rs['type']=='t_week'){     //每周星期几执行
                if ($time_w!=$rs['week']) {
                    continue;
                }
            }
            if ($rs['his']!='') {
                list($t_h,$t_i,$t_s) = explode(':',$rs['his']);
                $t_s || $t_s='00';
                if(intval("{$t_h}{$t_i}{$t_s}")>date('His')){     //还没到某时某分，不执行
                    continue;
                }
            }
            
            
            //做个最后执行的标志
            if( is_file(self::$task_file) && self::$client=='dos' ){
                touch(self::$task_file, time());
            }
            if( is_file(self::$task_web_file) && self::$client=='web' ){
                touch(self::$task_web_file, time());
            }
            
            $log = Db::name('timed_log')->where('taskid',$rs['id'])->order('id desc')->find();  //查找当前任务上一次执行过的时间
            if($log){
                if($rs['type']=='t_hours'){     //每隔几小时执行
                    $num = date('H')-date('H',$log['create_time']);
                    if ($num<0) {
                        $num += 24;
                    }
                    if ($num<$rs['hours']){
                        continue ;
                    }
                }elseif($rs['type']=='t_minutes'){      //每隔几分钟执行
                    $num = date('H')-date('H',$log['create_time']);
                    if ($num<0) {
                        $num += 24;
                    }
                    $num = $num*60 + date('i')-date('i',$log['create_time']);
                    if ($num<$rs['minutes']) {
                        continue ;
                    }
                }elseif( date('Y-m-d',$log['create_time'])==date('Y-m-d') ){   //一天内最多执行一次的情况
                    if( in_array($rs['type'], ['t_once','t_day','t_month','t_week','t_days']) ){
                        continue ;
                    }                 
                }
                if ($rs['type']=='t_days') {
                    if( (time()-$log['create_time'])<3600*24*$rs['days'] ){ //隔几天才能执行的
                        continue ;
                    }
                }
            }
            $data = [
                'taskid'=>$rs['id'],
                'create_time'=>time(),
            ];
            $id = Db::name('timed_log')->insertGetId($data);   //记录当前执行过的任务日志 
            
            $speed_time = explode(' ',microtime());
            $headtime = $speed_time[0] + $speed_time[1];
            
            try {
                $this->run_class($rs);
            } catch(\Exception $e) {                
            }
            
            
            $speed_time = explode(' ',microtime());
            $endtime = $speed_time[0] + $speed_time[1];
            $times = $endtime - $headtime;
            Db::name('timed_log')->update(['id'=>$id,'times'=>$times]);     //当前记录执行消耗的时间
            
            TaskModel::update([
                'id'=>$rs['id'],
                'last_time'=>time(),
                'use_time'=>$times,
            ]);
            TaskModel::where(['id'=>$rs['id']])->setInc('num',1);
            $ck++;
            sleep(5);   //休息一下,让服务器缓解一下压力
        }
        return $ck;
    }
    
    /**
     * 仅做测试用
     * @param string $ext
     */
//     public function runs($ext=''){
//         file_put_contents(ROOT_PATH.'110000000.TXT', date('Y-m-d H:i:s')." $ext\r\n",FILE_APPEND );
//     }
    
    /**
     * 运行定时任务脚本程序
     * @param array $info
     */
    private function run_class($info=[]){
        //file_put_contents(ROOT_PATH.'111.TXT', date('Y-m-d H:i:s')."  {$info['id']} {$info['class_file']}\r\n",FILE_APPEND );
        if (!class_exists($info['class_file'])) {
            return ;
        }
        $class_method = $info['class_method']?:'run';
        $obj = new $info['class_file'];
        if (!method_exists($obj,$class_method)) {
            return ;
        }
        $obj->$class_method($info['ext']);
    }
    
    /**
     * 前台调用的钩子
     */
    public function LayoutBodyFoot(){
        $url = urls('index/task/index');
        print<<<EOT
<script type="text/javascript">
$.get("{$url}",function(res){
	if(res.code==0){
		layer.msg('你成功执行了一条定时任务!');
	}
});
</script>
EOT;
    }
}