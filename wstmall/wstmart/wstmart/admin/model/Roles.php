<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\Roles as validate;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 角色志业务处理
 */
class Roles extends Base{
	/**
	 * 分页
	 */
	public function pageQuery(){
		return $this->where('dataFlag',1)->field('roleId,roleName,roleDesc')->paginate(input('limit/d'));
	}
	/**
	 * 列表
	 */
	public function listQuery(){
		return $this->where('dataFlag',1)->field('roleId,roleName')->select();
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
	    $result = $this->update($data,['roleId'=>$id]);
        if(false !== $result){
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	
	/**
	 * 获取角色权限
	 */
	public function getById($id){
		return $this->get(['dataFlag'=>1,'roleId'=>$id]);
	}
	
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		$data['createTime'] = date('Y-m-d H:i:s');
		$validate = new validate();
		if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
		$result = $this->allowField(true)->save($data);
        if(false !== $result){
        	return WSTReturn("新增成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$id = input('post.roleId/d');
		$data = input('post.');
		$validate = new validate();
		if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
	    $result = $this->allowField(true)->save($data,['roleId'=>$id]);
        if(false !== $result){
            $staffRoleId = (int)session('WST_STAFF.staffRoleId');
        	if($id==$staffRoleId){
        		$STAFF = session('WST_STAFF');
        		$STAFF['privileges'] = explode(',',input('post.privileges'));
        		$STAFF['roleName'] = Input('post.roleName');
        		session('WST_STAFF',$STAFF);
        	}
        	return WSTReturn("编辑成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	
}
