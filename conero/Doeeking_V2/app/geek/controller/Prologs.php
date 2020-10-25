<?php
namespace app\geek\controller;
use think\Controller;
class Prologs extends Controller
{
    public function index()
    {
    }
    public function edit()
    {
        geek_navBar($this->view,$this);
        $this->loadScript([
            'require'=>['tinymce','datetimepicker'],'auth'=>'','title'=>'Conero-技术交流','js'=>['Prologs/edit'],'bootstrap'=>true
        ]);
        $uid = isset($_GET['uid'])? bsjson($_GET['uid']):null;
        // println($uid);
        if($uid){
            $mode = $uid['mode'];
            if('A' == $mode){
                $qData = $this->croDb('project_tree')->where('no',$uid['pnode'])->find();
                $pages = [
                    'navBar'=> '<strong>'.$_GET['code'].'/'.$qData['node_name'].'</strong> 日志新增'
                ];
            }else{
                $data = $this->croDb('project_logs')->where('log_no',$uid['log'])->find();
                $qData = $this->croDb('project_tree')->where('no',$data['node_no'])->find();
                $this->assign('data',$data);
                $pages = [
                    'navBar'  => '<strong>'.$_GET['code'].'/'.$qData['node_name'].'</strong> 日志'.($mode == 'M'? '修改':'删除'),
                    'pkparam' => '<input type="hidden" name="log_no" value="'.$data['log_no'].'">'
                ];
            }
            // println($pages,$qData);
            $pages = array_merge($pages,$qData);
            if($mode) $pages['mode'] = $mode;
            // println($pages);
            $this->assign('pages',$pages);
        }
        return $this->fetch();
    }
    public function save(){
        $data = $_POST;
        $mode = isset($data['mode'])? $data['mode']:'';
        if($mode) unset($data['mode']);
        $url = urlBuild('!geek:prologs',['__get'=>['code'=>$data['pro_code']]]);     
        // 新增
        if($data && 'A' == $mode){ 
            $setask = isset($data['task'])? true:null;               
            $uData = $this->croDb('project_dev')->where('pro_code',$data['pro_code'])->field(['no'=>'dever_no','name'=>'dever_name'])->find();
            if($uData){
                if(empty($data['act_date'])) unset($data['act_date']);
                if(empty($data['type'])) unset($data['type']);
                if(empty($data['spend_hours'])) unset($data['spend_hours']);                
                $data = array_merge($uData,$data);       
                $id = $this->croDb('project_logs')->insertGetId($data);   
                if($id){
                    // 任务推送表
                    if($setask) uLogic('Conero')->setTaskEvent([
                        'task'      => $data['node_desc'].'/'.$data['title'],
                        'taskid'    => 'project_logs_'.$id,
                        'task_url'  => '/conero/geek/prologs/edit.html?code='.$data['pro_code'].'&uid='.bsjson(['mode'=>'M','log'=>$id,'task'=>'Y'])
                    ]);
                    $this->success($data['node_desc'].'成功新增一条日志！');
                }
            }
        }
        elseif($mode){
            $map = ['log_no'=>$data['log_no']];            
            if('M' == $mode){
                unset($data['log_no']);
                if($this->croDb('project_logs')->where($map)->update($data)) $this->success($data['node_desc'].'日志记录已更新！');
            }
            elseif('D' == $mode){
                $this->pushRptBack('project_logs',$map,true);
                $mapTask = ['taskid'=>'project_logs_'.$data['log_no']];
                if($this->pushRptBack('sys_taskrpt',$mapTask,true)) uLogic('Conero')->setTaskEvent($mapTask,true);           
                if($this->croDb('project_logs')->where($map)->delete()) $this->success($data['node_desc'].'已经被删除一天日志记录！',$url);
            }
        }
        $this->success('[-_-] 数据操作失败！',$url);
        println($data);
    }
}