<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\Friendlinks as validate;
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
 * 友情链接业务处理
 */
use think\Db;
class friendlinks extends Base{
	protected $pk = 'friendlinkId';
	/**
	 * 分页
	 */
	public function pageQuery(){
		return $this->where('dataFlag',1)->field('friendlinkId,friendlinkName,friendlinkIco,friendlinkSort,friendlinkUrl')->order('friendlinkId desc')->paginate(input('limit/d'));
	}
	public function getById($id){
		return $this->get(['friendlinkId'=>$id,'dataFlag'=>1]);
	}
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		$data['createTime'] = date('Y-m-d H:i:s');
		$data['friendlinkSort'] = (int)$data['friendlinkSort'];
		WSTUnset($data,'friendlinkId');
		Db::startTrans();
		try{
			$validate = new validate();
		    if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
			$result = $this->allowField(true)->save($data);
			$id = $this->friendlinkId;
	        if(false !== $result){
	        	cache('TAG_FRIENDLINK',null);
	        	//启用上传图片
			    WSTUseResource(1, $id, $data['friendlinkIco']);
			    Db::commit();
	        	return WSTReturn("新增成功", 1);
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
		$id = (int)input('post.friendlinkId');
		$data = input('post.');
		$data['friendlinkSort'] = (int)$data['friendlinkSort'];
		WSTUnset($data,'createTime');
		Db::startTrans();
		try{
			WSTUseResource(1, $id, $data['friendlinkIco'], 'friendlinks', 'friendlinkIco');
			$validate = new validate();
		    if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
		    $result = $this->allowField(true)->save($data,['friendlinkId'=>$id]);
	        if(false !== $result){
	        	cache('TAG_FRIENDLINK',null);
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
	    $id = input('post.id/d');
	    Db::startTrans();
		try{
			$data = [];
			$data['dataFlag'] = -1;
		    $result = $this->update($data,['friendlinkId'=>$id]);
	        if(false !== $result){
	        	cache('TAG_FRIENDLINK',null);
	        	WSTUnuseResource('friendlinks','friendlinkIco',$id);
	        	Db::commit();
	        	return WSTReturn("删除成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('删除失败',-1);
        }
	}
	
}
