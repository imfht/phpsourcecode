<?php
namespace app\geek\controller;
use think\Controller;
use app\Server\Geek;
class Project extends Controller
{
    public function _initialize()
    {
        $action = request()->action();
        if(!in_array($action,['save'])){
            geek_navBar($this->view,$this);
        }
        $this->geekSpecilNavPlus();
    }
    // 定制导航下拉框
    protected function geekSpecilNavPlus()
    {
        // 登录用户
        if($this->uLoginCkeck()){
            $dropdown = '
            <li role="presentation" class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
                设置 <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                <li role="presentation"><a role="menuitem" tabindex="-1" href="/conero/geek/project/edit.html">新增</a></li>      
                <li role="presentation"><a role="menuitem" tabindex="-1" href="/conero/center.html?project">我的项目</a></li>          
                </ul>
            </li>
            ';
            $this->assign('geekSpecilNavPlus',$dropdown);
        }
    }
    // 项目概略 - 描述
    public function index()
    {
        $geek = new Geek();
        // 请求地址检测
        $data = $geek->checkVisit();
        if($data){ // 当前项目详情
            return $this->prjDetail($data);
        }
        elseif($this->uLoginCkeck() && !isset($_GET['type'])){
            // die(urlBuild('!geek:project',['__get'=>['editor'=>uInfo('nick')]]));
            //urlBuild('geek:project',['__get'=>['editor'=>uInfo('nick')]]);
            $tmpWh = ['editor'=>uInfo('nick')];
            $pages = ['type'=>'self'];
        }
        else{
            $tmpWh = ['private_mk'=>'N'];
            $pages = [
                'type'=> $this->uLoginCkeck()? 'all':'none'
            ];
        }
        $this->loadScript([
            'title'=>'Conero-技术交流','css'=>['index/index'],'js'=>['index/index'],'bootstrap'=>true
        ]);
        // - 项目提取
        $btsp = $this->bootstrap($this->view);
        $wh = $btsp->getSearchWhere();        
        $wh = $wh? array_merge($tmpWh,$wh): $tmpWh;
        $count = $this->croDb('project_list')->where($wh)->count();
        $btsp->GridSearchForm(['__view__'=>'searchfrom','__cols__'=>['pro_code'=>'代码','pro_name'=>'名称','editor'=>'作者','edittm'=>'时间']]);
        $btsp->tableGrid(['__viewTr__'=>'trs'],['table'=>'project_list','cols'=>[function($record){return '<a href="/conero/geek/project.html?code='.$record['pro_code'].'">'.$record['pro_code'].'</a>';},'pro_name','editor','edittm']],function($db) use($wh,$btsp){
                $page = $btsp->page_decode();
                return $db->page($page,30)->where($wh)->select();
        });
        $btsp->pageBar($count);

        $this->assign('pages',$pages);
        return $this->fetch();
    }
    // 单项目节节点 - 描述
    protected function prjDetail($data){
        $this->loadScript([
            'require'=>["bootstrap","jstree"],'title'=>$data['pro_name'].'-Conero-技术交流','js'=>['Project/detail']
        ]);
        $page = [];
        $page['uLogin'] = $this->uLoginCkeck()? 'Y':'N';
        $map = ['pro_code'=>$data['pro_code']];
        $devs = $this->croDb('project_dev')->where($map)->select();
        // 开发者
        $devolper = '';
        foreach($devs as $v){
            $devolper .= '<li class="list-group-item"><a href="javascript:void(0);">'.$v['name'].'</a></li>';
        }
        if($devolper) $devolper = '<ul class="list-group">'.$devolper.'</ul>';
        // 删除按钮显示 - 创建本人可以删除
        $isOwner = 'N';
        if($page['uLogin'] == 'Y' && uInfo('code') == $data['user_code']){
            $page['deleteurl'] = '/conero/geek/project/save.html?uid='.bsjson(['dataid'=>'projectlist','datamode'=>'D','pro_code'=>$data['pro_code']]);
            $isOwner = 'Y';
        }
        $this->assign([
            'data' => $data,
            'page' => $page,
            'devolper' => $devolper
        ]);
        $this->tree($data['pro_code']);
        // 登录状态校验
        $this->_JsVar('uLogin',$isOwner);
        return $this->fetch('detail');
    }
    // 获取项目树- 遍历树 1
    protected function tree($code)
    {
        $data = $this->croDb('project_tree')->where('pro_code=\''.$code.'\' and parents_node is null')->select();
        $json = [];
        foreach($data as $v){            
            $node = ['text'=>$v['node_name'],'a_attr'=>['no'=>$v['no']]];
            $children = $this->_alltree($v['no']);
            if($children) $node['children'] = $children;
            $json[] = $node;
            
        }
        $this->_JsVar('ptree',$json);
    }
    // 获取项目树- 遍历树 2
    private function _alltree($no){        
        $ret = null;
        $data = $this->croDb('project_tree')->where('parents_node',$no)->select();
        if($data) $ret = [];
        foreach($data as $v){
            $node = ['text'=>$v['node_name'],'a_attr'=>['no'=>$v['no']]];
            $children = $this->_alltree($v['no']);
            if($children) $node['children'] = $children;
            $ret[] = $node;
        }
        return $ret;
    }
    // 编辑页面
    public function edit()
    {
        $this->loadScript([
            'auth'=>'','require'=>["tinymce","datetimepicker"],'title'=>'Conero-技术交流','js'=>['project/edit'],'bootstrap'=>true
        ]);
        $mode = 'A';
        if(isset($_GET['code'])){            
            $data = $this->croDb('project_list')->where(['pro_code'=>$_GET['code'],'user_code'=>uInfo('code')])->find();
            if($data){
                $this->assign('data',$data);
                $mode = 'M';
            }
        }
        $prjType = $this->croDb('sys_site')->where(['user_name'=>'CONST','gover_name'=>'project_type'])->field('plus_name,plus_desc')->select();
        $this->assign('prjtype',$prjType);
        $this->_JsVar('code',uInfo('code'));
        $this->_JsVar('mode',$mode);
        return $this->fetch();
    }
    // 保存页面 - 多表保存函数
    public function save()
    {
        $data = $_POST;
        if(empty($data)) $data = isset($_GET['uid'])? bsjson($_GET['uid']):$_GET;
        $mode = isset($data['datamode'])? $data['datamode']:null;
        $dataid = isset($data['dataid'])? $data['dataid']:null;
        switch($dataid){
            case 'projectlist': // 项目数据维护
                $tb = 'project_list';
                $uInfo = uInfo();
                if(empty($uInfo)){
                    $this->error('数据访问请求无效！',urlBuild('!geek'));
                }
                switch($mode){                    
                    case 'A':
                        unset($data['datamode']);unset($data['dataid']);                        
                        $code = $uInfo['code'];
                        $name = $uInfo['name'];
                        $nick = $uInfo['nick'];
                        $data['user_code'] = $code;
                        $data['editor'] = $nick;
                        if($this->croDb($tb)->insert($data)){
                            // 同步将作者写入到- 人员贡献表
                            $this->croDb('project_dev')->insert([
                                'pro_code' => $data['pro_code'],'user_code' => $code,'name' => $nick,'position'=>'成员，创始人','editor'=>$nick,'founder_mk'=>'Y'
                            ]);
                            $this->success('项目新增成功！');
                        }
                        else $this->success('【项目新增】-失败，数据无法正常提交！');
                        break;
                    case 'M':
                        unset($data['datamode']);unset($data['dataid']);
                        $map = ['pro_code'=>$data['pro_code'],'user_code'=>$uInfo['user_code']];unset($data['pro_code']);
                        if($this->croDb($tb)->where($map)->update($data)) $this->success('项目信息以及成功更新！');
                        else $this->success('【项目更新】-失败，数据无法正常提交！');
                        break;
                    case 'D': // 确保子项不存在数据
                        $map = ['pro_code'=>$data['pro_code']];
                        $this->pushRptBack('project_dev',$map,true);
                        $this->pushRptBack($tb,$map,true);
                        // 删除开发者表
                        $this->croDb('project_dev')->where($map)->delete(); 
                        if($this->croDb($tb)->where($map)->delete()) $this->success('项目信息已经被删除了！');
                        else $this->success('【项目删除】-失败，数据无法正常提交！');
                        break;
                }
                break;
        }        
        if($data) println($data);
        else println('无数据');
    }
}
