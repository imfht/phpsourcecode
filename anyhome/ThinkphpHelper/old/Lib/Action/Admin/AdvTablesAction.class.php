<?php
class AdvTablesAction extends CommonAction {

    public $appid ,$app,$connection;
    public function _initialize()
    {
        parent::_initialize();
        if (I('appid')) {
            session('appid',I('appid'));
        }
        $this->appid = session('appid');
        
    }

    public function ckApp()
    {
        if (!$this->appid) {
            afterNote('请选择应用');
            redirect(U('Admin/Index/index'));
        }
        $Apps = M('Apps');
        $app = $Apps->find($this->appid);
        if (!$app) {
            afterNote('请选择应用');
            redirect(U('Admin/Index/index'));
        }
    }

    public function index()
    {
        $AdvTables = D('AdvTables');
        $volist = $AdvTables->getTables();
        $this->assign('volist', $volist);
        $this->display();
    }


    //初始化所有
    public function init()
    {
        $AdvTables = D('AdvTables');
        $volist = $AdvTables->getTables();
        foreach ($volist as $k) {
            $this->iniTable($k['tableName'],1);
        }
        $this->success('操作成功');
    }

    //初始化菜单
    public function initMenu()
    {
        $volist = $AdvTables->getTables();
        foreach ($volist as $k) {
            $this->iniTable($k['tableName'],1);
        }
        # code...
    }



    //初始化表
    public function iniTable($tbname = 'Common',$dy = 0)
    {
        if (!$tbname) 
            $this->error('控制器名称不对');
        $this->createA($tbname,1);
        $this->createM($tbname,1);
        $this->createIndexTpl($tbname,1);
        $this->createAddTpl($tbname,1);
        $this->createEditTpl($tbname,1);
        $this->createFormField($tbname,1);
        // $this->createMemu($tbname,$tbname,1);
        $this->createPermission($tbname,1);
        if (!$dy) {
            $this->success('操作成功');
        }
    }

    //创建权限
    public function createPermission($tbname = '',$dy = 0)
    {
        $Group = M('Group');
        $map['code'] = $this->group;
        $g = $Group->where($map)->find();
        if (!$g) {
            $Group->add($map);
            $g = $Group->where($map)->find();
        }
        unset($map);
        $Module = M('Module');
        $modname = parse_name($tbname,1);
        $map['tbname'] = $tbname;
        $map['gid'] = $g['id'];
        $m = $Module->where($map)->find();
        if (!$m) {
            $map['name'] = $modname;
            $Module->add($map);
            $m = $Module->where($map)->find();
        }
        $acs = array('index','list','add','insert','edit','update','delete','search','view');
        unset($map);
        $Actions = M('Actions');
        $map['group'] = $this->group;
        $map['mod'] = $modname;
        $map['moid'] = $m['id'];
        foreach ($acs as $k) {
            $map['ac'] = $k;
            $map['name'] = $modname;
            $mac = $Actions->where($map)->find();
            if (!$mac) {
                $Actions->add($map);
            }
        }
        if (!$dy) {
            $this->success('操作成功');
        }
    }


    public function addMenu($tbname = '')
    {
        $Menu = M('Menu');
        $map['pid'] = 0;
        $menus = $Menu->where($map)->select();
        $this->assign('menus', $menus);
        $this->display();
    }

    //创建菜单
    public function createMemu($tbname = '',$name = "",$dy = 0)
    {
        if (!$tbname) 
            $this->error('控制器名称不对');
        $tbname = parse_name($tbname,1);
        if (!$name) $name = $tbname;
        $pid = I('pid');

        $Menu = M('Menu');
        $data['pid'] = $pid;
        $data['group'] = $this->group;
        $data['mod'] = $tbname;
        $data['name'] = $name;

        $map['mod'] = $tbname;
        $map['group'] = $this->group;
        $m = $Menu->where($map)->find();
        if ($m) {
            $Menu->where($map)->save($data);
        }else{
            $Menu->add($data);
        }

        if (!$dy) {
            $this->success('操作成功');
        }

    }

    //生成控制器
    public function createA($tbname = 'Common',$dy = 0)
    {
        if (!$tbname) 
            $this->error('控制器名称不对');
        $tbname = parse_name($tbname,1);
        $this->assign('tbname', $tbname);
        $content = $this->fetch('tpl_Action');
        // $acPath = dirname(__FILE__);
        $appConfig = $this->app;;
        $acPath = $appConfig['sitePath'].'Lib/Action/'.$appConfig['DEFAULT_GROUP'];
        $acFile = $acPath.'/'.$tbname.'Action.class.php';
        if (!file_exists($acFile)) {
            if (!is_dir($acPath))  mkdir($acPath,0777,true);
            file_put_contents($acFile,html_entity_decode($content));
        }
        if (!$dy) {
            $this->success('操作成功');
        }
    }

    //生成模型
    public function createM($tbname = 'Common',$dy = 0)
    {
        if (!$tbname) 
            $this->error('名称不对');
        $tbname = parse_name($tbname,1);
        $this->assign('tbname', $tbname);
        $content = $this->fetch('tpl_Model');
        // $acPath = LIB_PATH.'Model/';
        $appConfig = $this->app;;
        $acPath = $appConfig['sitePath'].'Lib/Model/';
        $acFile = $acPath.$tbname.'Model.class.php';
        if (!file_exists($acFile)) {
            if (!is_dir($acPath))  mkdir($acPath,0777,true);
            file_put_contents($acFile,html_entity_decode($content));
        }
        if (!$dy) {
            $this->success('操作成功');
        }
    }


    //首页模板
    public function createIndexTpl($tbname = 'Common',$dy = 0)
    {
        if (!$tbname) 
            $this->error('名称不对');
        $tbname = parse_name($tbname,1);
        $model = D($tbname);
        $fields = $model->getDbFields();
        $this->assign('fields', $fields);
        // $this->display('tpl_index');
        // exit();
        $content = $this->fetch('tpl_index');
        $content = str_replace("[extend]", "<extend name=\"Public:base\" />", $content);
        $content = str_replace("[block]", "<block name=\"box-content\">", $content);
        $content = str_replace("[/block]", "</block>", $content);
        $content = str_replace("[group]", $this->group, $content);
        $content = str_replace("[mod]", $tbname, $content);


        // $acPath = TMPL_PATH.'Admin/'.$tbname;
        // $appConfig = $this->app;;
        $acPath = './Tpl/'.$appConfig['DEFAULT_GROUP'].'/'.$tbname;

        $acFile = $acPath.'/index.html';

        if (!file_exists($acFile)) {
            if (!is_dir($acPath))  mkdir($acPath,0777,true);
            file_put_contents($acFile,$content);
        }
        if (!$dy) {
            $this->success('操作成功');
        }
    }


    //新增模板
    public function createAddTpl($tbname = 'Common',$dy = 0)
    {
        if (!$tbname) 
            $this->error('名称不对');
        $tbname = parse_name($tbname,1);
        $model = D($tbname);
        $fields = $model->getDbFields();
        $this->assign('fields', $fields);
        // $this->display('tpl_index');
        // exit();
        $content = $this->fetch('tpl_add');
        $content = str_replace("[extend]", "<extend name=\"Public:base\" />", $content);
        $content = str_replace("[block]", "<block name=\"box-content\">", $content);
        $content = str_replace("[/block]", "</block>", $content);


        // $acPath = TMPL_PATH.'Admin/'.$tbname;
        $appConfig = $this->app;;
        $acPath = $appConfig['sitePath'].'Tpl/'.$appConfig['DEFAULT_GROUP'].'/'.$tbname;
        $acFile = $acPath.'/add.html';

        if (!file_exists($acFile)) {
            if (!is_dir($acPath))  mkdir($acPath,0777,true);
            file_put_contents($acFile,$content);
        }
        if (!$dy) {
            $this->success('操作成功');
        }
    }


    //修改模板
    public function createEditTpl($tbname = 'Common',$dy = 0)
    {
        if (!$tbname) 
            $this->error('名称不对');
        $tbname = parse_name($tbname,1);
        $model = D($tbname);
        $fields = $model->getDbFields();
        $this->assign('fields', $fields);
        // $this->display('tpl_index');
        // exit();
        $content = $this->fetch('tpl_edit');
        $content = str_replace("[extend]", "<extend name=\"Public:base\" />", $content);
        $content = str_replace("[block]", "<block name=\"box-content\">", $content);
        $content = str_replace("[/block]", "</block>", $content);


        // $acPath = TMPL_PATH.'Admin/'.$tbname;
        $appConfig = $this->app;;
        $acPath = $appConfig['sitePath'].'Tpl/'.$appConfig['DEFAULT_GROUP'].'/'.$tbname;
        $acFile = $acPath.'/edit.html';

        if (!file_exists($acFile)) {
            if (!is_dir($acPath))  mkdir($acPath,0777,true);
            file_put_contents($acFile,$content);
        }
        if (!$dy) {
            $this->success('操作成功');
        }
    }

    //表单字段
    public function createFormField($tbname = 'Common',$dy = 0)
    {
        if (!$tbname) 
            $this->error('名称不对');
        $tbname = parse_name($tbname,1);
        $model = D($tbname);
        $fields = $model->getDbFields();
        $TableFiled = M('TableFiled');
        foreach ($fields as $k) {
            if ($k == 'id' || $k == 'mid') continue;
            $data['title'] = $k;
            $data['fname'] = $k;
            $data['mod'] = $tbname;
            $map['mod'] =  $tbname;
            $map['fname'] =  $k;
            $f = $TableFiled->where($map)->find();
            if (!$f) 
                $TableFiled->add($data);
        }
        if (!$dy) {
            $this->success('操作成功');
        }
    }

    //公共控制器
    public function createCommonAction($dy = 0)
    {
        $content = $this->fetch('tpl_CommonAction');
        $appConfig = $this->app;;
        $acPath = $appConfig['sitePath'].'Lib/Action/'.$appConfig['DEFAULT_GROUP'];
        $acFile = $acPath.'/'.$tbname.'CommonAction.class.php';
        if (!file_exists($acFile)) {
            if (!is_dir($acPath))  mkdir($acPath,0777,true);
            file_put_contents($acFile,html_entity_decode($content));
        }
        if (!$dy) {
            $this->success('操作成功');
        }
    }

    //公共模型
    public function createCommonModel($dy = 0)
    {
        $content = $this->fetch('tpl_CommonModel');
        $appConfig = $this->app;;
        $acPath = $appConfig['sitePath'].'Lib/Model/';
        $acFile = $acPath.'CommonModel.class.php';
        if (!file_exists($acFile)) {
            if (!is_dir($acPath))  mkdir($acPath,0777,true);
            file_put_contents($acFile,html_entity_decode($content));
        }
        if (!$dy) {
            $this->success('操作成功');
        }
    }


}