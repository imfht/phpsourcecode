<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {
    public $ac,$md,$con;
    public $app,$apps,$code;
    public $menu;
    public function _initialize (){

        $this->ac = ACTION_NAME;
        $this->con = CONTROLLER_NAME;
        $this->md = MODULE_NAME;
        

        $this->uid = session('admin_id');
        if (!$this->uid) {
            layout(false);
            $apps = F('apps');
            $this->assign('apps', $apps);
            $this->display('Public/login');
            exit();
        }
        if (I('code')) session('code',I('code'));

        if (session('code')) $this->code = session('code');

        if ($this->code) {
            $apps = F('apps');  
            $this->app = $apps[$this->code];

            $app_cfg = F($this->code.'/cfg');
            $models = $app_cfg['model'];
            $menus = array();
            foreach ($models as $k) {
                // print_r($k);exit();
                if ($k['is_nav'] == 1) {
                    $k['name'] = ucfirst($k['name']);
                    $menus[strtolower($k['name'])] = $k;
                }
            }
            // print_r($menus);exit();
            $this->assign('Menus', $menus);
            $con = parse_name($this->con);
            $menu = $menus[$con];
            $this->menu = $menu;
            $this->assign('menu', $menu);
            // print_r($menu);exit();

        }

        if ($this->app) {
            session('app_id',$this->app['app_id']);
            session('app_key',$this->app['app_key']);
        }



        $this->assign('md', $this->md);
        $this->assign('ac', $this->ac);
        $this->assign('con', $this->con);
        $this->assign('admin_id', $this->uid);

        $this->assign('code', $this->code);
        $this->assign('app', $this->app);

        $this->assign('addUrl', U($this->con.'/add'));
        $this->assign('editUrl', U($this->con.'/edit?id='.I('id')));
        $this->assign('viewUrl', U($this->con.'/view?id='.I('id')));
        $this->assign('viewAttrUrl', U($this->con.'/viewAttr?id='.I('id')));
        $this->assign('uploadAttrUrl', U($this->con.'/uploadAttr?id='.I('id')));
        $this->assign('deleteAttrUrl', U($this->con.'/deleteAttr'));
        $this->assign('delUrl', U($this->con.'/delete?id='.I('id')));
        $this->assign('backUrl', U($this->con.'/index'));

        $this->assign('insertUrl', U($this->con.'/insert'));
        $this->assign('updateUrl', U($this->con.'/update'));
        $this->assign('updateField', U($this->con.'/updateField'));
        
        $this->assign('auditUrl', U($this->con.'/audit'));
        $this->assign('updateAuditUrl', U($this->con.'/updateAudit'));


        unset($map);

        layout(true);
        if (array_key_exists('HTTP_X_PJAX', $_SERVER) && $_SERVER['HTTP_X_PJAX'])
        {
            layout(false);
        }else{
            
        }
    }

    public function index($key = '',$curPage =1,$pageSize = 10){
        if (IS_POST) {
            $m = D('ApiCloud');
            $map['class'] = parse_name($this->con);

            if ($key) {
                foreach ($this->menu['field'] as $k) {
                    if($k['search'])
                        $map[$k['name']]['like'] = $key;
                }
            }
            // print_r($map);exit();

            $ret = $m->getPage($map,$curPage,$pageSize);
            if (!$ret){
                $ret['volist'] = '';
                $ret['count'] = 0;
            } 
            $data['success'] = true;
            $data['data'] = $ret['volist'];
            $data['totalRows'] = $ret['count'];
            $data['curPage'] = $curPage;
            $this->ajaxReturn($data);
        }

        $tpl = T($this->con.'/index');
        $tpl = str_replace("./", "", $tpl);

        if (!file_exists($tpl)) {
            $this->display('Common/index');
        }else{
            $this->display();
        }
    }


    public function delete($id ='')
    {
        $m = D('ApiCloud');
        $map['class'] = parse_name($this->con);
        $ret = $m->where($map)->delete($id);
        if ($ret !== FALSE) {
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
    }

    public function insert()
    {
        $m = D('ApiCloud');
        $map['class'] = parse_name($this->con);
        $data = I('post.');
        unset($map['id']);
        $ret = $m->where($map)->add($data);
        if ($ret !== FALSE) {
            $this->success('操作成功',U($this->con.'/index'));
        }else{
            $this->error('操作失败');
        }
    }

    public function edit($id ='')
    {
        $m = D('ApiCloud');
        $map['class'] = parse_name($this->con);
        $map['id'] = $id;
        $vo = $m->where($map)->find();
        // print_r($vo);exit();
        $this->assign('vo',$vo);
        $tpl = T($this->con.'/edit');
        $tpl = str_replace("./", "", $tpl);
        if (!file_exists($tpl)) {
            $this->display('Common/edit');
        }else{
            $this->display();
        }

    }


    public function add()
    {
        // print_r($this->menu['field']);exit();
        $tpl = T($this->con.'/add');
        $tpl = str_replace("./", "", $tpl);

        if (!file_exists($tpl)) {
            $this->display('Common/add');
        }else{
            $this->display();
        }
    }

    public function update($id='',$model ='')
    {
        $m = D('ApiCloud');
        $map['class'] = parse_name($this->con);
        $map['id'] = $id;
        $data = I('post.');
        unset($map['id']);
        $ret = $m->where($map)->save($data);
        if ($ret !== FALSE) {
            $this->success('操作成功',U($this->con.'/index'));
        }else{
            $this->error('操作失败');
        }
    }


    public function pointer($id='')
    {
        $m = D('ApiCloud');
        $map['class'] = parse_name($this->con);

        $ret = $m->getPage($map,$curPage,100);
        $this->assign('id', $id);
        $this->assign('data', $ret['volist']);
        $tpl = T($this->con.'/pointer');
        $tpl = str_replace("./", "", $tpl);

        if (!file_exists($tpl)) {
            $this->display('Common/pointer');
        }else{
            $this->display();
        }
    }


    

    
}