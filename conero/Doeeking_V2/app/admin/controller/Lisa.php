<?php
// 系统自身检测 - 2017年1月3日 星期二
namespace app\admin\controller;
use think\Controller;
use hyang\Fs;
use hyang\Util;
class Lisa extends controller{
    private $ieadfile;
    private $_getLisaVarJson;   // 静态模拟量
    public function _initialize(){
        // $this->ieadfile = config('lisa_ieads_file').md5('lisa.ieads').'.bsj';        
        // $this->ieadfile = config('lisa_ieads_file').md5('lisa.ieads');
        $this->ieadfile = uLogic('Admin')->getLisaConfig();
    }
    // 本地检测文件
    public function index()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero-系统模块','bootstrap'=>true
        ]);
        $md = isset($_GET['md'])?$_GET['md']:null;
        if($md) $this->assign('md',$md);
        $this->assign('trs',$this->localData($md));
        return $this->fetch();
    }
    // 本地模板文件映射
    // $module -> string/array
    private function localData($module=null){
        if($module) $module = is_array($module)? $module:[$module];
        // iead生成器
        $file = $this->ieadfile;        
        if(!is_file($file)){
            urlBuild('.lisa/iead',['__get'=>['fname'=>base64_encode($file)]]);
        }        
        $data = bsjson(file_get_contents($file));  
        $data = array_merge([
            'module'=>'','php'=>'','js'=>'','css'=>''
        ],$data);         
        $php = $data['php'];
        $module = $module? $module:explode(',',strtolower($data['module']));
        $js = $data['js'];
        $css = $data['css'];
        $xhtml = '';
        $no = 0;
        foreach(scandir(ROOT_PATH.$php) as $v){
            if(!in_array($v,$module)) continue;
            $tmpData = scandir(ROOT_PATH.$php.$v.'/controller');
            $first = true;
            foreach($tmpData as $vv){
                if(in_array($vv,['.','..'])) continue;
                $phpName = str_replace('.php','',$vv);
                $jsPath = ROOT_PATH.$js.$v.'/'.$phpName.' ';
                $cssPath = ROOT_PATH.$css.$v.'/'.$phpName.' ';
                $viewPath = ROOT_PATH.$php.$v.'/view/'.$phpName.' ';
                $no++;
                $jsPath = Util::runClosure(function() use($jsPath){
                    Fs::dir($jsPath);
                    return Fs::toString();
                });
                $viewPath = Util::runClosure(function() use($viewPath){
                    Fs::dir($viewPath);
                    return Fs::toString();
                });
                $cssPath = Util::runClosure(function() use($cssPath){
                    Fs::dir($cssPath);
                    return Fs::toString();
                });
                $xhtml .= '
                    <tr class="'.$v.'"><td>'.$no.'</td>'.($first? '<td rowspan="'.(count($tmpData)-2).'"><a href="'.urlBuild('!.lisa','?md='.$v).'">'.$v.'</a></td>':'').'<td>'.$phpName.'</td><td>'.$viewPath.'</td><td>'.$jsPath.'</td><td>'.$cssPath.'</td></tr>
                ';
                $first = false;
            }
        }
        return $xhtml;
    }
    // 本地检测数据写入到数据库中
    public function local2dber()
    {
        $file = $this->ieadfile;        
        if(!is_file($file)){
            urlBuild('.lisa/iead',['__get'=>['fname'=>base64_encode($file)]]);
        }        
        $data = bsjson(file_get_contents($file));  
        $module = explode(',',strtolower($data['module']));
        $php = $data['php'];
        $nick = uInfo('nick');
        $proCode = $this->_getLisaVar('code');
        $deveNo = $this->croDb('project_dev')->where(['name'=>$nick,'pro_code'=>$proCode])->value('no');
        $insertCount = 0;$count = 0;
        if($deveNo){
            $controller = '';
            foreach($module as $v){
                $dir = ROOT_PATH.$php.$v.'/controller/';
                $pNo = $this->croDb('project_tree')->where(['pro_code'=>$proCode,'parents_node'=>$this->_getLisaVar('parents_node'),'node_code'=>$v])->value('no');     
                if(empty($pNo)) continue;
                foreach(scandir($dir) as $vv){
                    if(in_array($vv,['.','..'])) continue;
                    $controller = strtolower(str_replace('.php','',$vv));
                    $count += 1;
                    $map = [
                        'pro_code'=>$proCode,
                        'parents_node' => $pNo,
                        'node_code' => $controller,
                    ];
                    // 数据存在时不覆盖-跳过
                    if($this->croDb('project_tree')->where($map)->count() >0) continue;                    
                    $treeData = array_merge($map,[
                        'node_name' => $controller,
                        'keyword'   => 'Controller',
                        'node_type'   => 'Controller',
                        'node_desc'   => '系统根据服务器文件扫描自动生成数据',
                        'url'   => url('/'.$v.'/'.$controller),
                        'dever_no'  => $deveNo,
                        'dever_name'  => $nick
                    ]);
                    if($this->croDb('project_tree')->insert($treeData)) $insertCount += 1;
                    // echo $pNo.'=>'.$controller.' ';
                    // println(print_r($treeData,true));
                }
            }
            if($insertCount>0) $this->success('本地新增【'.$insertCount.'/'.$count.'】条数据');
            println($data);
        }
    }
    // 数据库加载文件
    public function dber()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero-系统模块','js'=>['Lisa/dber'],'bootstrap'=>true
        ]);
        // 当前父类模块
        $no = request()->param('no');
        $proCode = $this->_getLisaVar('code');
        // 系统模块全列
        $map = [
            'pro_code' => $proCode,
            'parents_node' => $this->_getLisaVar('parents_node')
        ];
        $data = $this->croDb('project_tree')->where($map)->select();
        $xhtml = '';$ctt = 1;
        foreach($data as $v){
            $xhtml .= '<li class="list-group-item'.($no && $no == $v['no']? ' list-group-item-success':'' ).'">'.$ctt.'. <a href="'.url('dber','no='.$v['no']).'">'.$v['node_code'].' '.$v['node_name'].'</a><span style="float: right;">'.$v['create_dt'].'</span></li>'; 
            $ctt++;
        }
        if($xhtml) $xhtml = '<h4>系统模块列表</h4><ul class="list-group">'.$xhtml.'</ul>';
        $module = [
            'group' => $xhtml
        ];
        // 当前选择子列
        if($no){
            $map= [
                'pro_code' => $proCode,
                'parents_node' => $no
            ];
            $data = $this->croDb('project_tree')->where($map)->select();
            $ctt = 1;
            $xhtml = '';
            foreach($data as $v){
                $xhtml .= '<tr dataid="'.$v['no'].'"><td>'.$ctt.'</td><td>'.$v['node_code'].' '.$v['node_name'].'</td><td>'.$v['create_dt'].'<td><td><a href="javascript:void(0);" class="del_link" dataid="'.urlBuild('!.lisa/save','?uid='.bsjson(['mode'=>'D','no'=>$v['no']])).'">删除</a> <a href="javascript:void(0);" class="edit_link">修改</a> <a href="'.url('dber','no='.$v['no']).'">子方法</a>'.(empty($v['url'])? '':' <a href="'.$v['url'].'" target="_blank">跳转</a>').'<td></tr>';
                $ctt++;
            }
            $xhtml = $xhtml? '<table class="table">'.$xhtml.'<table>':'<p><a href="javascript:history.back(-1);" class="text-danger">未查找到数据，点击返回上一级！</a></p>';
            $module['childGroup'] = $xhtml;
            // println($data);
        }
        $this->assign('module',$module);
        // println($data);
        return $this->fetch();
    }
    // 服务器本地文档目录
    public function docs()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero-系统模块','bootstrap'=>true
        ]);
        $descFname = null;
        if(isset($_GET['name'])){
            $descFname = base64_decode($_GET['name']);
            $doc = dirname($descFname);
            
        }
        else $doc = isset($_GET['dir'])? base64_decode($_GET['dir']):$this->_getLisaVar('doc');
        // print_r($doc);
        $xhtml = '';
        $pathname = ROOT_PATH.$doc;
        Fs::$pathname = $pathname;
        $data = Fs::dir(function($v) use(&$xhtml,$pathname,$doc){
            static $ctt = 1;
            $isDir = is_dir($pathname.'/'.$v)? true:false;
            $name = $isDir? '<a href="'.urlBuild('!.lisa/docs','?dir='.base64_encode($doc.'/'.$v)).'">'.$v.'</a>':'<a href="'.urlBuild('!.lisa/docs','?name='.base64_encode($doc.'/'.$v)).'">'.$v.'</a>';
            $xhtml .= '<tr><td>'.$ctt.'</td><td>'.$name.'</td><td>'.($isDir? '<span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> 目录':'<span class="glyphicon glyphicon-file" aria-hidden="true"></span> 文件').'</td></tr>';
            $ctt++;
        });
        // println($data);
        // println($xhtml);
        $this->assign('trs',$xhtml);
        if($descFname){
            $this->assign('dfname',str_replace(ROOT_PATH,'',$descFname));
            $content = $descFname;
            $finfo = pathinfo($descFname);
            if(!isset($finfo['extension'])) $content = '';
            elseif(in_array(strtolower($finfo['extension']),['php','js','json','tpl','gitignore'])) $content = '<textarea class="form-control" rows="50">'.file_get_contents(ROOT_PATH.$descFname).'</textarea>';
            elseif(in_array(strtolower($finfo['extension']),['png'])) $content = '<img src="/conero/'.$descFname.'">';
            elseif(in_array(strtolower($finfo['extension']),['md'])) $content = '<div class="panel panel-default"><div class="panel-body">'.(\auto\Load::object('Parsedown')->text(file_get_contents($descFname))).'</div></div>';
            
            $content = ($content? $content:'').'<div class="list-group">
                <p class="list-group-item active">文件其他说明</p>
                <p class="list-group-item">1. <a href="/conero/index/common/download.html?file='.$descFname.'">点击下载'.$descFname.'</a></p>
                <p class="list-group-item">2. 文件大小 '.filesize($descFname).' 字节</p>
                <p class="list-group-item">3. 修改日期 '.date('Y-m-d H:i:s',filemtime($descFname)).' 字节</p>
                <div>';
            $this->assign('content',$content);
        }
        return $this->fetch();
    }
    // idea生成器
    public function iead()
    {
        $this->loadScript([
            'auth'=>'DEV','title'=>'Conero-Lisa','js'=>'Lisa/iead','bootstrap'=>true
        ]);
        $file = isset($_GET['fname'])? base64_decode($_GET['fname']):null;
        $file = $file? $file:$this->ieadfile;
        // println($file);
        $data = [];
        if(is_file($file)){
            $content = trim(file_get_contents($file));
            if(substr_count($file,'.bsj')>0){
                $data = bsjson($content);
            }
            if(!is_array($data)) $data = json_decode($content,true);  
            $data = $data? $data:$content;
            if(is_array($data)) $data['sys_page_fname'] = $file;     
        }
        if($data && is_array($data)){
            $this->_JsVar('loaddata','Y');
            $this->assign('data',$data);
        }
        // println($data,$file);
        return $this->fetch();
    }
    // iead 数据维护
    /*  sys_page_fname => 文件保护类型 sys_page_fbsjson => bsjson 加密法
     *  否则下载生成的文件!!
     *
     *
     */
    public function ieadSave()
    {
        $data = $_POST;
        if(isset($data['sys_page_fname'])){
            // 保存文件
            $fname = $data['sys_page_fname'];
            unset($data['sys_page_fname']);
            $bsjson = isset($data['sys_page_fbsjson'])? $data['sys_page_fbsjson']:null;
            if(substr_count($fname,'.bsj')>0) $json = bsjson($data);
            else $json = count($data)>0? json_encode($data):null;  
            $ret = 'json为空！';
            if($json){
                // $fname = substr_count($fname,'.json')>0? $fname:$fname.'.json';
                if(is_dir(ROOT_PATH.dirname($fname))){
                    file_put_contents(ROOT_PATH.$fname,$json);
                    $ret = $fname.'文件已经生成';
                }
                else $ret = $fname.'文件名称无效';
            }
            $this->success($ret);
        }
        else{
            $json = count($data)>0? json_encode($data):'json数据为空或者已经损坏！'; 
            $fname = uInfo('nick').'.json';
            // \hyang\Download::filename($fname); 
            // \hyang\Download::setContent($json); 
            \hyang\Download::setConfig([
                'content' => $json,
                'name' => $fname
            ]);
            \hyang\Download::load(); 
        }
        println($data);
    }
    public function save()
    {
        /*
        $data = count($_POST)>0? $_POST:$_GET;
        $data = isset($data['uid'])? bsjson($data['uid']):$data;
        $mode = isset($data['mode'])? $data['mode']:'';
        if($mode) unset($data['mode']);
        */
        list($data,$mode) = $this->_getSaveData();
        $ret = '操作失败！！';
        switch($mode){
            case 'D':
                $map = ['no'=>$data['no']];
                $ret = $this->pushRptBack('project_tree',$map,'auto')? '数据已经成功删除！':'数据删除时失败！';
                break;
            case 'M':
                $map = ['no'=>$data['no']];unset($data['no']);
                $ret = $this->croDb('project_tree')->where($map)->update($data)? '数据已经成功更新！':'数据更新时失败！';
                break;
        }
        // println($data,$mode); die;
        $this->success($ret);
    }
    // 字段获取
    private function _getLisaVar($key=null){
        $data = empty($this->_getLisaVarJson)? bsjson(file_get_contents($this->ieadfile)):$this->_getLisaVarJson;
        $data = is_array($data)? $data:[];
        if($data && empty($this->_getLisaVarJson)) $this->_getLisaVarJson = $data;
        if($key){
            return array_key_exists($key,$data)? $data[$key]:'';
        }
        return $data;
    }
}