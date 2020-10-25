<?php
// +-------------------------------------------------------------+
// | Author: 战神~~巴蒂 <378020023@qq.com> <http://www.jyuu.cn>  |
// +-------------------------------------------------------------+
namespace Admin\Controller;
use Think\Controller;
class ProfileController extends AdminController {
    public function index(){
    	$map  = array('status' => array('gt', -1));
        $list = $this->lists(M('MemberFieldGroup'),$map,'sort asc,id asc',true);
        $this->assign('list', $list);
		$this->meta_title = '资料分组';
        $this->display();
	}
	
	public function group(){
    	$map  = array('status' => array('gt', -1));
        $list = $this->lists(M('MemberFieldGroup'),$map,'sort asc,id asc',true);
        $this->assign('list', $list);
		$this->meta_title = '资料分组';
        $this->display();
	}
	
	
    /**添加分组信息
     * @author 战神~~巴蒂
     */
	public function addgroup($id = 0, $name = ''){
		if(IS_POST){
			$G = D('MemberFieldGroup');
			$data['name'] = $name;
            if ($data['name'] == '') {
                $this->error('分组名称不能为空！');
            }
            $map['name'] = $name;
        	if ($G->where($map)->count() > 0) {
                $this->error('已经有同名分组，请使用其他分组名称！');
            }		
        	$data['status'] = 1;
            $data['create_time'] = NOW_TIME;
            $id = $G->add($data);  
            if($id){
                $this->success('新增成功');
            } else {
               $this->error('新增失败');
            }
        } else {
			$this->meta_title = '添加资料分组';
			$this->display();
        }

	}
	
	/**修改分组信息
     * @author 战神~~巴蒂
     */
	
	public function modgroup ($id = 0,$name = ''){
        if(IS_POST){
			$G = D('MemberFieldGroup');
			$data['name'] = $name;
			$data['id'] = $id;
            if ($data['name'] == '') {
                $this->error('分组名称不能为空！');
            }
            $map['name'] = $name;
        	if ($G->where($map)->count() > 0) {
                $this->error('已经有同名分组，请使用其他分组名称！');
            }	
            if($G->where('id=' . $id)->save($data)!== false){
                $this->success('更新成功',Cookie('__forward__'));
            } else {
                $this->error('更新失败');
            }
        } else {
            /* 获取数据 */
            $data = M('MemberFieldGroup')->field(true)->find($id);
            if(false === $data){
                $this->error('获取后台数据信息错误');
            }
            $this->assign('data', $data);
			$this->meta_title = '修改资料组';
			$this->display('addgroup');
        }
	}
	
	/**
     * 删除资料组
     */
    public function delgroup(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('MemberFieldGroup')->where($map)->delete()){
            //记录行为
            //action_log('update_channel', 'channel', $id, UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
    
    
    /**
    * 管理字段
    */
    
    public function memberfield($group_id){
    	//$group = D('MemberFieldGroup')->where('id=' . $group_id)->find();
    	$group = D('MemberFieldGroup')->getFieldById($group_id,'name');
    	$map['group_id'] = $group_id;
    	$list = $this->lists(D('memberField'),$map);
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    	$this->assign('group',$group);
    	$this->assign('list', $list);
    	$this->meta_title = $group.'-字段管理';
    	$this->display();
    	
    }
    
    public function addfield(){    
		if(IS_POST){
            $Field= D('MemberField');
            $data = $Field->create();
            if($data){
                $id = $Field->add();
                if($id){
                    $this->success('新增成功',Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Field->getError());
            }
        } else {
			$this->meta_title = '添加字段';
			$this->display();
        }
    	
    }
    
    public function modfield($id = 0){    
		if(IS_POST){
            $Field= D('MemberField');
            $data = $Field->create();
            if($data){
                if($Field->save()!== false){
                    $this->success('更新成功',Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Field->getError());
            }
        } else {
        	$data = M('MemberField')->field(true)->find($id);
            if(false === $data){
                $this->error('获取后台数据信息错误');
            }
            $this->assign('data', $data);
			$this->meta_title = '修改字段';
			$this->display('addfield');
        }
    	
    }
}