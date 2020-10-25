<?php
namespace app\admin\controller;
use app\common\controller\AdminBase; 
use app\admin\model\AdminMenu AS MenuModel;
use app\common\util\Tabel;
use app\common\util\Form;
use util\Tree;
use app\common\util\Menu;

class MemberMenu extends AdminBase
{
    protected $validate;
    protected $group_nav;
    
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new MenuModel();
        $group_list = getGroupByid();
        
        $this->group_nav[0]=[
            'title'=>'系统默认菜单',
            'url'=>url('index',['gid'=>0]),
        ];
        
        foreach($group_list  AS $id=>$name){
            $this->group_nav[$id]=[
                    'title'=>$name,
                    'url'=>url('index',['gid'=>$id]),
            ];
        }        
    }
    
    /**
     * 会员菜单列表
     * @param unknown $gid
     * @return unknown
     */
    public function index($gid=null)
    {
        if ($gid==null) {
            $gid=0;
            $this->copy_menu($gid);
            Menu::member_sys_cache(true);
        }
	    $map = [
	            'groupid'=>$gid,
	            'type'=>1,
	    ];
	    $listdb = Tree::config(['title' => 'name'])->toList(
	            MenuModel::where($map)->order('list desc,id asc')->column(true)
	            );

	    $tab = [
	            //['id','ID','text'],
	            ['title_display','链接名称','text'],
	            ['groupid','所属用户组','select2',getGroupByid()],
	            ['list','排序值','text.edit'],
	            ['ifshow','是否显示','switch'],
	            ['target','新窗口打开','yesno'],
	            //['right_button', '操作', 'btn'],
// 	            ['right_button', '操作', 'callback',function($value,$rs){
// 	                if($rs['pid']>0)$value=str_replace('_tag="add"', 'style="display:none;"', $value);
// 	                return $value;
// 	            },'__data__'],
	    ];
	    
	    $script = "";
	    if ($gid<1) {
	        $script = "<script type='text/javascript'>
//$('.quick_edit,.fa-plus,.fa-times,.top_menu').hide();
$('.quick_edit,.fa-plus,.top_menu').hide();
$('._switch').click(function(){
    alert('这里设置不一定会生效,请选择右边菜单修改设置');
    return false;
});
$('.trA').each(function(){
    if($(this).html().indexOf('&nbsp;&nbsp;&nbsp;&nbsp;')==-1){
	   $(this).find('._switch,.glyphicon-ban-circle').hide();
	}
});
</script>";
	    }else{
	        $script = "<script type='text/javascript'>
$('.trA').each(function(){
    if($(this).html().indexOf('&nbsp;&nbsp;&nbsp;&nbsp;')>-1){
        $(this).find('.fa-plus').hide();
    }
});
</script>";
	    }
	    
	    $table = Tabel::make($listdb,$tab)
	    ->addTopButton('add',['title'=>'手工添加菜单','url'=>url('add',['gid'=>$gid])])
	    ->addTopButton('custom',['title'=>'快速导入会员所有菜单','url'=>url('copy',['gid'=>$gid]),'icon'=>'fa fa-copy'])
	    ->addTopButton('delete')
	    ->addRightButton('add',['title'=>'添加下级菜单','href'=>url('add',['pid'=>'__id__','gid'=>'__groupid__'])])
	    ->addRightButton('delete')
	    ->addRightButton('edit')
	    //->addPageTips('省份管理')
	    //->addOrder('id,list')
	    ->addPageTitle('会员个性菜单管理')
	    ->addPageTips("系统默认菜单请不要删除，不可恢复！这里展示出来，主要是方便重新定义图标或修改文字".$script)
	    ->addNav($this->group_nav,$gid) ;   

        return $table::fetchs();
	}
	
	protected function copy_menu($gid=0){
	    $num1 = $num2 = 0;
	    foreach(Menu::make('member') AS $m_name=>$array1){
	        foreach($array1['sons'] AS $key1=>$array2){
	            $title1 = $array2['title'];
	            $data1 = ['type'=>1,'groupid'=>$gid,'title'=>$title1];
	            $info1 = MenuModel::get($data1);
	            if (empty($info1)) {
	                $num1++;
	                $array2['icon'] && $data1['icon'] = $array2['icon'];
	                $data1['name'] = $title1;  //新的菜单名称
	                $result = MenuModel::create($data1) ;
	                $data2 = ['pid'=>$result->id];
	            }else{
	                $data2 = ['pid'=>$info1['id']];
	            }
	            $data2['groupid'] = $gid;
	            $data2['type'] = 1;
	            foreach($array2['sons'] AS $rs){
	                $data2['url'] = str_replace([ADMIN_FILENAME.'/admin/',ADMIN_FILENAME.'/'], ['member.php/member/','member.php/'], $rs['url']);
	                if (empty(MenuModel::get($data2))) {
	                    $num2++;
	                    $data2['name'] = $data2['title'] = $rs['title'];
	                    $rs['icon'] && $data2['icon'] = $rs['icon'];
	                    MenuModel::create($data2) ;
	                }
	            }
	        }
	    }
	    return [$num1,$num2];  //分别是一级菜单二级菜单生成的个数
	}
	
	/**
	 * 快速批量复制会员所有菜单
	 * @param number $gid
	 */
	public function copy($gid=0){
	    list($num1,$num2) = $this->copy_menu($gid);
	    if ($num1 || $num2) {
	        $this->success("本次共创建一级菜单 {$num1} 个,二级菜单 {$num2} 个");
	    }else{
	        $this->error('已经导入过了,重复导入无效');
	    }
	}
	
	/**
	 * 添加会员个性菜单
	 * @param number $pid
	 * @param number $gid
	 * @return unknown
	 */
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

	    $array = MenuModel::where(['groupid'=>$gid,'pid'=>0,'type'=>1])->column('id,name');
	    $form = Form::make()
	    ->addPageTips('父菜单为PC或WAP的话,子菜单设置通用无效')
	    ->addSelect('groupid','所属用户组','',getGroupByid(),$gid)
	    ->addSelect('pid','父级菜单','',$array,$pid)
	    ->addText('name','菜单名称')
	    ->addText('url','菜单链接')
	    ->addRadio('target','是否新窗口打开','',['本窗口打开','新窗口打开'],0)
	    ->addIcon('icon','小图标')
	    ->addColor('fontcolor','字体颜色')
	    ->addColor('bgcolor','背景颜色')
	    ->addHidden('type','1')
	    ->addPageTitle('添加会员个性菜单');
	    return $form::fetchs();
	}

	/**
	 * 修改会员个性菜单
	 * @param number $id
	 * @return unknown
	 */
	public function edit($id=0){	    
	    if ($this->request->isPost()) {
	        $data = $this -> request -> post();
	        $data['allowgroup'] = $data['allowgroup']?implode(',',$data['allowgroup']):'';
	        if (!empty($this -> validate)) {   //验证数据
	            $result = $this -> validate($data, $this -> validate);
	            if (true !== $result) $this -> error($result);
	        }
	        if(!isset($data['is_use']))$data['is_use'] = 1;   //会员个性菜单才用到
	        if (MenuModel::update($data)) {
	            Menu::member_sys_cache(true);
	            $this->success('修改成功',url('index',['gid'=>$data['groupid']]));
	        } else {
	            $this->error('修改失败');
	        }
	    }
	    $info = MenuModel::get($id);
	    $array = MenuModel::where('groupid',$info['groupid'])->where('pid','=',0)->column('id,name');
	    $form = Form::make([],$info)->addPageTitle('修改菜单');
	    if($info['groupid']>0 && $info['pid']>0){
	        $form->addSelect('pid','父级菜单','',$array)->addText('url','菜单链接');
	    }
	    $form->addText('name','名称');
	    if($info['pid']>0){
	        $form->addCheckbox('allowgroup','指定用户组使用','不设置则按默认为准,即有权限',getGroupByid())
	        ->addRadio('target','是否新窗口打开','',['本窗口打开','新窗口打开'])
	        ->addRadio('ifshow','是否隐藏','',['隐藏','显示(不隐藏)']);	        
	    }
	    $info['groupid']>0 && $form->addNumber('list','排序值');
	    $form->addIcon('icon','小图标')
	    ->addColor('fontcolor','字体颜色','是否有效果,需要模板配合做样式')
	    ->addColor('bgcolor','背景颜色','是否有效果,需要模板配合做样式')
	    ->addTextarea('script','脚本事件','要填写的话,需要补齐 &lt;script&gt;&lt;/script&gt;同理,也可以写css或html')
	    ->addHidden('groupid')
	    ->addPageTips("系统菜单不能修改链接")
	    ->addHidden('id',$id);
	    if($info['groupid']==0 && $info['is_use']==1){ //方便用户取消
	        $form->addRadio('is_use','个性设置','',[0=>'跟随系统',1=>'个性设置']);
	    }

	    return $form::fetchs();
	}
	
	/**
	 * 删除会员个性菜单.
	 * @param unknown $ids
	 */
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
