<?php
namespace app\admin\controller;
use app\common\controller\AdminBase; 
use app\admin\model\AdminMenu AS MenuModel;
use app\common\util\Tabel;
use app\common\util\Form;
use util\Tree;
use app\common\model\Group;

class AdminMenu extends AdminBase
{
    protected $validate;
    protected $grouplist;
    protected $group_nav;
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new MenuModel();
        $this->grouplist = Group::getTitleList(['allowadmin'=>1]);
//         $array = $this->model->group('groupid')->column('groupid');
//         $array = $array?array_merge($array,[3]):[3];
        $group_list = Group::where('allowadmin',1)->order('id asc')->column('id,title');
        foreach($group_list  AS $id=>$name){
            $this->group_nav[$id]=[
                    'title'=>$name,
                    'url'=>url('index',['gid'=>$id]),
            ];
        }
    }
    
    public function index($gid=0)
    {
        $gid || $gid=3;
	    $map = [
	            'groupid'=>$gid,
	            'type'=>0,
	    ];
	    $listdb = Tree::config(['title' => 'name'])->toList(
	            MenuModel::where($map)->order('list desc,id asc')->column(true)
	            );

	    $tab = [
	            //['id','ID','text'],
	            ['title_display','链接名称','text'],
	            ['groupid','所属用户组','select2',$this->grouplist],
	            ['list','排序值','text.edit'],
	            ['ifshow','是否显示','switch'],
	            ['target','新窗口打开','yesno'],
	            //['right_button', '操作', 'btn'],
// 	            ['right_button', '操作', 'callback',function($value,$rs){
// 	                if($rs['pid']>0)$value=str_replace('_tag="add"', 'style="display:none;"', $value);
// 	                return $value;
// 	            },'__data__'],
	    ];
	    
	    $table = Tabel::make($listdb,$tab)
	    ->addTopButton('add',['title'=>'添加菜单','url'=>url('add',['gid'=>$gid])])
	    ->addTopButton('delete')
	    ->addRightButton('add',['title'=>'添加下级菜单','href'=>url('add',['pid'=>'__id__','gid'=>'__groupid__'])])
	    ->addRightButton('delete')
	    ->addRightButton('edit')
	    //->addPageTips('省份管理')
	    //->addOrder('id,list')
	    ->addPageTitle('网站菜单管理')
	    ->addNav($this->group_nav,$gid) ;   

        return $table::fetchs();
	}
	
	public function add($pid=0,$gid=0){
	    if ($this->request->isPost()) {
	        $data = $this->request->post();
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        if($data['name']==''){
	            $this->error('名称不能为空!');
	        }
	        
	        if(MenuModel::create($data)){	                
	            $this->success('创建成功 ', url('index',['gid'=>$data['groupid']]) );
	        } else {
	            $this->error('创建失败');
	        }
	    }
	    $gid || $gid=3;

	    $array = MenuModel::where(['groupid'=>$gid,'pid'=>0])->column('id,name');
	    $form = Form::make()
	    ->addPageTips('父菜单为PC或WAP的话,子菜单设置通用无效')
	    ->addSelect('groupid','所属用户组','',$this->grouplist,$gid)
	    ->addSelect('pid','父级菜单','',$array,$pid)
	    ->addText('name','菜单名称')
	    ->addText('url','菜单链接')
	    ->addRadio('target','是否新窗口打开','',['本窗口打开','新窗口打开'],0)
	    ->addIcon('icon','小图标')
	    ->addPageTitle('添加菜单');
	    return $form::fetchs();
	}

	public function edit($id=0){
	    
	    if ($this->request->isPost()) {
	        $data = $this -> request -> post();
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        if (MenuModel::update($data)) {
	            $this->success('修改成功',url('index',['type'=>$data['type']]));
	        } else {
	            $this->error('修改失败');
	        }
	    }
	    $info = MenuModel::get($id);
	    $array = MenuModel::where('groupid',$info['groupid'])->where('pid','=',0)->column('id,name');
	    $form = Form::make([],$info)
	    ->addPageTitle('修改菜单')
	    ->addSelect('pid','父级菜单','',$array)
	    ->addText('name','名称')	    
	    ->addText('url','菜单链接')
	    ->addRadio('target','是否新窗口打开','',['本窗口打开','新窗口打开'])
	    ->addRadio('ifshow','是否隐藏','',['隐藏','显示(不隐藏)'])
	    ->addNumber('list','排序值')
	    ->addIcon('icon','小图标')
	    ->addHidden('id',$id);

	    return $form::fetchs();
	}
	
	public function delete($ids){
	    if (empty($ids)) {
	        $this -> error('ID有误');
	    }
	    $ids = is_array($ids)?$ids:[$ids];
	    if (MenuModel::destroy($ids)) {
	        $this->success('删除成功','index');
	    } else {
	        $this->error('删除失败');
	    }
	}
}
