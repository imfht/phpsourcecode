<?php
namespace app\admin\controller;

use app\common\controller\AdminBase;
use app\common\traits\AddEditList;
use app\common\model\Timedtask AS TaskModel;
use think\Db;

/**
 * 定时任务
 */
class Timedtask extends AdminBase
{
    use AddEditList;
    protected $validate = '';
    protected $model;
    protected $form_items;
    protected $list_items;
    protected $tab_ext = [
        'page_title'=>'定时任务',
        'help_msg'=>'注意:定时任务必须要配置好才能生效,<a href="http://help.php168.com/1092543" style="color:blue;" target="_blank">点击查看详细配置说明</a>',
    ];
    protected $types = [
        't_day'=>'每天一次',
        't_week'=>'每周一次',
        't_month'=>'每月一次',
        't_days'=>'每隔几天一次',
        't_hours'=>'每隔几小时一次',
        't_minutes'=>'每隔几分钟一次',
        't_once'=>'只执行一次',
    ];
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new TaskModel();
        $this->tab_ext['top_button'] = [
            ['type'=>'add','title'=>'创建新的任务'],
            ['type'=>'delete','title'=>'批量删除任务'],
            [
                'title'=>'启动任务(后端)',
                'icon'=>'fa fa-windows',
                'url'=>"javascript:$.get('".iurl('index/task/dos')."',function(res){if(res.code==1){layer.alert(res.msg)}});layer.msg('已执行...');",
            ],
        ];
        
        
        $this->form_items = [
            ['text','title','功能描述'],
            ['radio','type','执行周期','',$this->types,'t_day'],
            ['number','week','每周星期几执行','星期天就写7'],
            ['number','day','每月哪天执行'],
            ['date','ymd','具体哪年哪月哪天执行','比如2019-12-12'],
            ['number','days','每隔几天执行'],
            ['number','hours','每隔几小时执行'],
            ['number','minutes','每隔几分钟执行'],
            ['time','his','几时几分开始执行','比如：10:30:00'],
            ['text','class_file','脚本程序类的路径','比如:“app\common\task\Qsend”'],
            ['text','class_method','类的方法名','留空则默认用run'],
        ];
        
        $this->tab_ext['trigger'] = [
            ['type', 't_once', 'ymd,his'],
            ['type', 't_week', 'week,his'],
            ['type', 't_month', 'day,his'],
            ['type', 't_day', 'his'],
            ['type', 't_days', 'days,his'],
            ['type', 't_hours', 'hours'],
            ['type', 't_minutes', 'minutes'],
        ];        
    }
    
    /**
     * 核对脚本是否正确
     * @param array $info
     * @return void|boolean
     */
    private function check_class($info=[]){
        if (!class_exists($info['class_file'])) {
            return ;
        }
        $class_method = $info['class_method']?:'run';
        $obj = new $info['class_file'];
        if (!method_exists($obj,$class_method)) {
            return ;
        }
        return true;
    }
    
    
    /**
     * 所有任务
     * @return mixed|string
     */
    public function index() {
        $map = [];
        $this->list_items = [
            //['uid', '用户UID', 'text'],
            ['title', '任务功能描述', 'text'],
            ['type', '运行频率', 'callback',function($type,$rs){
                if ($type=='t_day') {
                    $msg = '每天1次,'.$rs['his'].'后执行';
                }elseif($type=='t_week') {
                    $msg = '每周1次,星期'.$rs['week'].' '.$rs['his'].'后执行';
                }elseif($type=='t_month') {
                    $msg = '每月1次,'.$rs['day'].'号 '.$rs['his'].'后执行';
                }elseif($type=='t_once') {
                    $msg = '仅1次,于'.$rs['ymd'].' '.$rs['his'].'后执行';
                }elseif($type=='t_days') {
                    $msg = '每隔'.$rs['days'].'天执行1次, '.$rs['his'].'后执行';
                }elseif($type=='t_hours') {
                    $msg = '每隔'.$rs['hours'].'小时执行';
                }elseif($type=='t_minutes') {
                    $msg = '每隔'.$rs['minutes'].'分钟执行';
                }
                return $msg;
            }],
            
            ['ifopen', '启用与否', 'switch'],
            ['num', '执行次数', 'callback',function($num,$rs){
                $url = url('log',['taskid'=>$rs['id']]);
                return $num?"<a href=\"{$url}\">详情</a>({$num})":0;
            }],
            ['last_time', '上次执行日期', 'datetime'],
            ['use_time', '耗时(秒)', 'text'],
            ['id', '测试', 'callback',function($id,$rs){
                $url = iurl('index/task/test',['id'=>$id]);
                return "<a class='fa fa-retweet' href=\"javascript:;\" onclick=\"$.get('{$url}',function(res){if(res.code==0){layer.alert(res.msg)}else{layer.alert(res.msg);}});layer.msg('请稍候...')\">执行</a>";
            }],
            ['create_time', '创建日期', 'datetime'],
                
        ];
        $this->tab_ext['page_title'] = "定时任务";
        if (is_file(RUNTIME_PATH.'Task.txt')) {
            $time = date('Y-m-d H:i:s',filemtime(RUNTIME_PATH.'Task.txt'));
            $this->tab_ext['page_title'] .="，后端最近执行时间:<font color='red'>".$time.'</font>';
            if (time()-filemtime(RUNTIME_PATH.'Task.txt')>600 && time()-filemtime(RUNTIME_PATH.'Task_web.txt')>600 ) {
                $this->tab_ext['page_title'] .="，已超过10分钟没反应，可能停止了,请重新启动";
            }
        }
        if (is_file(RUNTIME_PATH.'Task_web.txt')) {
            $this->tab_ext['page_title'] .="，前台最近执行时间:<font color='blue'>".date('Y-m-d H:i:s',filemtime(RUNTIME_PATH.'Task_web.txt')).'</font>';
        }
        return $this -> getAdminTable(self::getListData($map, 'list desc,id desc' ));
    }
    
    /**
     * 生成标志,给后台任务好核对是否变化过.
     */
    public static function make_cfg(){
        //write_file(RUNTIME_PATH.'Task_config.txt', date('Y-m-d H:i:s'));
        task_config(true);
    }
    
    /**
     * 新建任务
     * @return mixed|string
     */
    public function add(){
        if ($this -> request -> isPost()) {
            if ($this->check_class($this->request->post())!==true) {
                $this -> error('脚本程序不存在,或有误');
            }
            if ($this -> saveAddContent()) {
                $this->make_cfg();
                $this -> success('添加成功', 'index');
            } else {
                $this -> error('添加失败');
            }
        }
        return $this -> addContent();
    }
    
    /**
     * 修改任务
     * @param number $id
     * @return mixed|string
     */
    public function edit($id = 0) {
        if (empty($id)) $this -> error('缺少参数');
        if ($this -> request -> isPost()) {
            if ($this->check_class($this->request->post())!==true) {
                $this -> error('脚本程序不存在,或有误');
            }
            if ($this -> saveEditContent()) {
                $this->make_cfg();
                $this -> success('修改成功', 'index');
            } else {
                $this -> error('修改失败');
            }
        }
        $info = $this -> getInfoData($id);
        return $this -> editContent($info);
    }
    
    
    /**
     * 执行的日志
     * @param number $taskid
     * @return mixed|string
     */
    public function log($taskid=0){
        $map = [];
        if ($taskid) {
            $map = [
                'taskid'=>$taskid,
            ];
        }
        $listdb = Db::name('timed_log')->where($map)->order('id desc')->paginate();
        $this->list_items = [
            ['taskid','任务名称','callback',function($taskid,$rs){
                $info = $this->model->get($taskid);
                return $info['title'];
            }],
            ['create_time','执行时间','datetime'],
            ['times','耗时(秒)','text'],
            ];
        return $this -> getAdminTable($listdb);
    }
    
    /**
     * 删除任务
     * @param unknown $ids
     * @return unknown
     */
    public function delete($ids = null) {
        $ids = is_array($ids)?$ids:[$ids];
        foreach ($ids AS $id){
            Db::name('timed_log')->where('taskid',$id)->delete();
        }
        if ($this -> deleteContent($ids)) {
            $this->make_cfg();
            $this -> success('删除成功');
        } else {
            $this -> error('删除失败');
        }
    }
    
 
}
