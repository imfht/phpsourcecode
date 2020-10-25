<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\Menus as validate;
use think\Db;
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
 * 菜单业务处理
 */
class Menus extends Base{
	protected $insert = ['dataFlag'=>1]; 
	/**
	 * 获取菜单列表
	 */
	public function listQuery($parentId = -1){
		if($parentId==-1)return ['id'=>0,'name'=>WSTConf('CONF.mallName'),'isParent'=>true,'open'=>true];
		$rs = $this->where([['parentId','=',$parentId],['dataFlag','=',1]])->field('menuId id,menuName name')->order('menuSort', 'asc')->select();
		if(count($rs)>0){
			foreach ($rs as $key =>$v){
				$rs[$key]['isParent'] = true;
			}
		};
		return $rs;
	}
	/**
	 * 获取菜单
	 */
	public function getById($id){
		return $this->where([['dataFlag','=',1],['menuId','=',$id]])->find();
	}
	
	/**
	 * 新增菜单
	 */
	public function add(){
		$data = input('post.');
		WSTUnset($data,'menuId');
		$validate = new validate();
		if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
		$result = $this->save($data);
        if(false !== $result){
        	WSTClearAllCache();
        	return WSTReturn("新增成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
    /**
	 * 编辑菜单
	 */
	public function edit(){
		$menuId = input('post.menuId/d');
		$data = input('post.');
		$validate = new validate();
		if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
	    $result = $this->allowField(['menuName','menuIcon','menuSort'])->save($data,['menuId'=>$menuId]);
        if(false !== $result){
        	WSTClearAllCache();
        	return WSTReturn("编辑成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	/**
	 * 删除菜单
	 */
	public function del(){
	    $menuId = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
	    $result = $this->update($data,['menuId'=>$menuId]);
        if(false !== $result){
        	WSTClearAllCache();
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	public function getChildMenus($parentId,$data){
		$rdata = [];
        foreach($data as $v){
            if($v['parentId']==$parentId){
            	$v['child'] =  $this->getChildMenus($v['menuId'],$data);
            	$rdata[] = $v;
            }
        }
        return $rdata;
	}
	/**
	 * 获取用户菜单
	 */
	public function getMenus(){
		//用户权限判断
		$STAFF = session('WST_STAFF');
		$datas  = [];
		$dbo = $this->alias('m')->join('__PRIVILEGES__ p','m.menuId= p.menuId and isMenuPrivilege=1 and p.dataFlag=1','left')
			->where('m.dataFlag','=',1)->where('m.isShow','=',1);
		if((int)$STAFF['staffId']!=1){
			$dbo->where([['m.menuId','in',$STAFF['menuIds']]]);
		}	
		$menus = $dbo->field('m.menuId,m.menuName,privilegeUrl,m.parentId,m.menuIcon')
			->order('menuSort', 'asc')
			->select();
		if(!empty($menus)){
	    	foreach($menus as $key =>$v0){
                if($v0['parentId']==0){
                	$v0['child'] = $this->getChildMenus($v0['menuId'],$menus);
                	$datas[] = $v0;
                }
	    	}
	    }
	    return $datas;
	}
}
