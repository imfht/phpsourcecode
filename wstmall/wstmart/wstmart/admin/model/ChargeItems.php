<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\ChargeItems as validate;
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
 * 充值项业务处理
 */
class ChargeItems extends Base{
	/**
	 * 分页
	 */
	public function pageQuery(){
		$where = [];
		$where['dataFlag'] = 1;
		return $this->where($where)->field(true)->order('itemSort asc,id asc')->paginate(input('limit/d'));
	}
	public function getById($id){
		return $this->get(['id'=>$id,'dataFlag'=>1]);
	}
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		$data["createTime"] = date("Y-m-d H:i:s");
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
		$Id = (int)input('post.id');
		$data = input('post.');
		$validate = new validate();
		if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
	    $result = $this->allowField(true)->save($data,['id'=>$Id]);
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
	    $result = $this->setField(['id'=>$id,'dataFlag'=>-1]);
        if(false !== $result){
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	
	
}
