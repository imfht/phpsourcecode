<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\DataCats as validate;
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
 * 系统数据分类业务处理
 */
use think\Db;
class DataCats extends Base{
	protected $insert = ['dataFlag'=>1]; 
	/**
	 * 获取数据分类列表
	 */
	public function listQuery($catId = -1){
		if($catId==-1)return ['id'=>0,'name'=>'系统数据','isParent'=>true,'open'=>true];
		$rs = Db::name('data_cats')->where(['dataFlag'=>1])->field('catId id,catName name')->select();
		return $rs;
	}
	/**
	 * 获取数据分类
	 */
	public function getById($id){
		return $this->get(['dataFlag'=>1,'catId'=>$id]);
	}
	
	/**
	 * 新增数据分类
	 */
	public function add(){
		// 验证数据代码
		$catCode = input('catCode');
		$data = input('post.');
		$hasCode = $this->where(['catCode'=>$catCode,'dataFlag'=>1])->find();
		if(!empty($hasCode))return WSTReturn('数据分类代码已存在');
		// 执行新增
		$validate = new validate();
		if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
		$result = $this->save($data);
        if(false !== $result){
        	return WSTReturn("新增成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
    /**
	 * 编辑数据分类
	 */
	public function edit(){
		$id = input('post.catId/d');
        $data = input('post.');
		// 验证数据代码
		$catCode = input('catCode');
		$hasCode = $this->where([['catCode','=',$catCode],['dataFlag','=',1],['catId','<>',$id]])->find();
		if(!empty($hasCode))return WSTReturn('数据分类代码已存在');

        $validate = new validate();
		if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
	    $result = $this->allowField(['catName','catCode'])->save($data,['catId'=>$id]);
        if(false !== $result){
        	return WSTReturn("编辑成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	/**
	 * 删除数据分类
	 */
	public function del(){
	    $id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
		Db::startTrans();
		try{
			$result = $this->update($data,['catId'=>$id]);// 删除该数据分类
			if(false !== $result){
	        	// 删除该数据分类下所有子数据
	        	Db::name('datas')->where(['catId'=>$id])->setField('dataFlag',-1);
				Db::commit();
				return WSTReturn("删除成功", 1);
	        }
		}catch(\Exception $e){
			Db::rollback();
			return WSTReturn("删除失败",-1);
		}
        
	}
	
}
