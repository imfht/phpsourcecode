<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\AdPositions as validate;
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
 * 广告位置业务处理
 */
class AdPositions extends Base{
	protected $pk = 'positionId';
	/**
	 * 分页
	 */
	public function pageQuery(){
		$positionType = (int)input('positionType');
		$key = input('key');
		$where[] = ['dataFlag','=',1];
        if($positionType>0)$where[] = ['positionType','=',$positionType];
        if($key !='')$where[] = ['positionCode','like','%'.$key.'%'];
		return $this->where($where)->field(true)->order('apSort asc,positionName asc')->paginate(input('limit/d'));
	}
	public function getById($id){
		return $this->get(['positionId'=>$id,'dataFlag'=>1]);
	}
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		WSTUnset($data,'positionId');
		$validate = new validate();
		if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
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
		$data = input('post.');
		$Id = (int)input('post.positionId');
		$validate = new validate();
		if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
	    $result = $this->allowField(true)->save($data,['positionId'=>$Id]);
        if(false !== $result){
        	return WSTReturn("编辑成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = (int)input('post.id/d');
	    $result = $this->setField(['positionId'=>$id,'dataFlag'=>-1]);
        if(false !== $result){
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	/**
	* 获取广告位置
	*/
	public function getPositon($typeId){
		return $this->where(['positionType'=>$typeId,'dataFlag'=>1])->order('apSort asc,positionId asc')->select();
	}
	
}
