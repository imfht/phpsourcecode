<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\MobileBtns as validate;
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
 * 商家认证业务处理
 */
class MobileBtns extends Base{
	/**
	 * 分页
	 */
	public function pageQuery(){
		$btnSrc = (int)input('btnSrc1',-1);
		$btnName = input('btnName1');
		$where = [];
		if($btnSrc>-1){
			$where[] = ['btnSrc','=',$btnSrc];
		}
		if($btnName!=''){
			$where[] = ['btnName','like',"%$btnName%"];
		}
		return $this->field(true)
					->where($where)
					->order('btnSrc asc,btnSort asc')
					->paginate(input('limit/d'));
	}
	public function getById($id){
		return $this->get(['id'=>$id]);
	}
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		$data['btnSort'] = (int)$data['btnSort'];
		WSTUnset($data,'id');
		Db::startTrans();
		try{
			$validate = new validate();
			if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
			$result = $this->allowField(true)->save($data);
			if(false !==$result){
				cache('WST_MOBILE_BTN',null);
				$id = $this->id;
				//启用上传图片
				WSTUseResource(1, $id, $data['btnImg']);
		        if(false !== $result){
		        	Db::commit();
		        	return WSTReturn("新增成功", 1);
		        }
			}
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('新增失败',-1);	
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$data = input('post.');
		$data['btnSort'] = (int)$data['btnSort'];
		WSTUnset($data,'createTime');
		Db::startTrans();
		try{
			WSTUseResource(1, (int)$data['id'], $data['btnImg'], 'mobile_btns', 'btnImg');
		    $validate = new validate();
		    if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
		    $result = $this->allowField(true)->save($data,['id'=>(int)$data['id']]);
	        if(false !== $result){
	        	cache('WST_MOBILE_BTN',null);
	        	Db::commit();
	        	return WSTReturn("编辑成功", 1);
	        }
	    }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('编辑失败',-1);  
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = (int)input('post.id/d');
	    Db::startTrans();
		try{
		    WSTUnuseResource('mobile_btns','btnImg',$id);	
		    $result = $this->where(['id'=>$id])->delete();
	        if(false !== $result){
	        	cache('WST_MOBILE_BTN',null);
	        	Db::commit();
	        	return WSTReturn("删除成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1); 
	}
	
}
