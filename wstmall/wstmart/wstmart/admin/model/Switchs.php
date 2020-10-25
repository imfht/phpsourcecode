<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\Banks as validate;
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
 * 页面转换业务处理
 */
class Switchs extends Base{
	/**
	 * 分页
	 */
	public function pageQuery(){
		return $this->order('id desc')->paginate(input('limit/d'));
	}
	public function getById($id){
		return $this->get(['id'=>$id]);
	}
	/**
	 * 列表
	 */
	public function listQuery(){
		return $this->select();
	}
	/**
	 * 新增
	 */
	public function add(){
		$data = ['homeURL'=>input('post.homeURL'),'mobileURL'=>input('post.mobileURL'),'wechatURL'=>input('post.wechatURL')];
		if(($data['homeURL'] == '' && $data['mobileURL'] == '') || ($data['homeURL'] == '' && $data['wechatURL'] == '') || ($data['mobileURL'] == '' && $data['wechatURL'] == ''))return WSTReturn('请至少输入两个要转换的网址');
		$result = $this->save($data);
        if(false !== $result){
        	cache('WST_SWITCHS',null);
        	return WSTReturn("新增成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$id = input('post.id/d',0);
		$data = ['homeURL'=>input('post.homeURL'),'mobileURL'=>input('post.mobileURL'),'wechatURL'=>input('post.wechatURL')];
		if(($data['homeURL'] == '' && $data['mobileURL'] == '') || ($data['homeURL'] == '' && $data['wechatURL'] == '') || ($data['mobileURL'] == '' && $data['wechatURL'] == ''))return WSTReturn('请至少输入两个要转换的网址');
		$result = $this->save($data,['id'=>$id]);
        if(false !== $result){
        	cache('WST_SWITCHS',null);
        	return WSTReturn("编辑成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = input('post.id/d',0);
		$data = [];
	    $result = $this->where('id',$id)->delete();
        if(false !== $result){
        	cache('WST_SWITCHS',null);
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	
}
