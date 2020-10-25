<?php
namespace app\geek\controller;
use think\Controller;
class Protree extends Controller
{
    public function index()
    {
        geek_navBar($this->view,$this);
        $this->loadScript([
            'title'=>'Conero-技术交流','css'=>['index/index'],'js'=>['protree/index'],'bootstrap'=>true
        ]);
        $data = isset($_GET['no'])? bsjson($_GET['no']):null;
        if(!empty($data)){
            $no = $data['no'];
            $data = $this->croDb('project_tree')->where('no',$no)->find();
            $data['breadcrumb'] = implode('',$this->getTreePath($no));
            $this->assign('pages',$data);
            $logs = $this->croDb('project_logs')->where('node_no',$no)->order('act_date desc')->select();
            if($logs) $this->assign('logs',$logs);
            // 项目创建者或者-开发者
            $editRight = $this->croDb('project_list')->where(['pro_code'=>$data['pro_code'],'user_code'=>uInfo('code')])->count() > 0? true:false;
            if($editRight == false && $data['dever_name'] == uInfo('nick')) $editRight = true;
            $this->assign('editRight',($editRight? 'Y':'N'));
            if($editRight){
                $helper = [
                    'editurl' => '/conero/geek/protree/edit.html?uid='.bsjson(['mode'=>'M','no'=>$no]),
                    'delurl' => '/conero/geek/protree/edit.html?uid='.bsjson(['mode'=>'D','no'=>$no])
                ];
                $this->assign('helper',$helper);
            }
        }        
        // println($data);
        return $this->fetch();
    }
    // 获取项目路径
    private function getTreePath($no){
        $tmpArr = [];
        $data = $this->croDb('project_tree')->where('no',$no)->field('node_name,node_code,no,parents_node')->find();
        // $data = $this->croDb('project_tree')->where('no',$no)->find();
        $tmpArr[] = '<li><a href="/conero/geek/protree.html?no='.bsjson(['no'=>$data['no']]).'" title="'.$data['node_code'].'">'.$data['node_name'].'</a></li>';
        if(!empty($data['parents_node'])){
            $pData = $this->getTreePath($data['parents_node']);
            if(!empty($pData)){
                foreach($pData as $v){
                    array_unshift($tmpArr,$v);
                }
            }
        }
        return $tmpArr;
    }
    public function edit()
    {
        geek_navBar($this->view,$this);
        $this->loadScript([
            'title'=>'Conero-技术交流','css'=>['index/index'],'js'=>['protree/edit'],'bootstrap'=>true
        ]);
        // url 传入传递参数
        $pages = isset($_GET['uid'])? bsjson($_GET['uid']):'';
        if($pages){
            $mode = $pages['mode'];
            if($mode == 'A'){
                $query = $this->croDb('project_tree')->where('no',$pages['pnode'])->field('node_name')->find();
                if($query) $pages = array_merge($query,$pages);
                $pages['code'] = $_GET['code'];
            }
            else{
                $pages = $this->croDb('project_tree')->where('no',$pages['no'])->find();
                $this->assign('data',$pages);
                $pages = [
                    'code' => $pages['pro_code'],
                    'mode' => $mode
                ];
            }
            // println($pages);            
        }
        else $pages = ['code'=>$_GET['code'],'mode'=>'A'];
        $this->assign('pages',$pages);                        
        return $this->fetch();
    }
    public function ajax()
    {
        $item = isset($_POST['item'])? $_POST['item']:'';
        $data = $_POST;
        if($item) unset($data['item']);
        if($item == 'logs_detail'){
            $data = $this->croDb('project_logs')->where('log_no',$data['dataid'])->find();
            $html = '
                <div class="panel-heading">'.$data['title'].'</div>
                <div class="panel-body">'.$data['content'].'</div>
            ';
            echo $html;die;
        }
    }
    public function save()
    {
        $data = $_POST;
        $dataid = '';
        if(isset($data['dataid'])){$dataid = $data['dataid'];unset($data['dataid']);}        
        $mode = '';
        if(isset($data['formmode'])){$mode = $data['formmode'];unset($data['formmode']);}
        switch($dataid){
            case 'projectree':
                $code = $data['pro_code'];
                // $mode = null;         
                if($mode == 'A'){
                    if(empty($data['parents_node'])) unset($data['parents_node']);
                    $getUser = $this->croDb('project_dev')->where(['pro_code'=>$code,'user_code'=>uInfo('code')])->field(['no'=>'dever_no','name'=>'dever_name'])->find();
                    if($getUser) $data = array_merge($data,$getUser);
                    else{
                        $this->success('节点新增失败，操作人无效,可能时你不具备该操作权限',urlBuild('!geek:project',['__get'=>['code'=>$code]]));
                    }
                    if($this->croDb('project_tree')->insert($data)){
                        $this->success('项目节点创建成功！',urlBuild('!geek:project',['__get'=>['code'=>$code]]));
                    }
                }      
                elseif($mode == 'M' || $mode == 'D'){
                    // println($mode);die;
                    $map = ['no'=>$data['no']];unset($data['no']);
                    if($mode == 'M'){
                        unset($data['parents_node']);
                        $ret = $this->croDb('project_tree')->where($map)->update($data);
                        $ret = $ret? '项目节点成功修改！':'项目修改失败，您可能未做任何修改';
                    }
                    else{
                        // println($mode,$map);die;                        
                        $this->pushRptBack('project_tree',$map,true);
                        $ret = $this->croDb('project_tree')->where($map)->delete();
                        $ret = $ret? '项目节点已经被删除！':'项目修删除失败';
                    }
                    $this->success($ret,urlBuild('!geek:project',['__get'=>['code'=>$code]]));  
                }          
                break;
        }
        println($_POST,$data);
    }
}