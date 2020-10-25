<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\UserRanks as validate;
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
 * 会员等级业务处理
 */
class UserRanks extends Base{
	protected $pk = 'rankId';
	/**
	 * 分页
	 */
	public function pageQuery(){
		return $this->where('dataFlag',1)->field(true)->order('rankId desc')->paginate(input('limit/d'));
	}
	public function getById($id){
		return $this->get(['rankId'=>$id,'dataFlag'=>1]);
	}
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		$data['createTime'] = date('Y-m-d H:i:s');
		$data['startScore'] = (int) $data['startScore'];
		$data['endScore'] = (int) $data['endScore'];
		WSTUnset($data,'rankId');
		Db::startTrans();
		try{
			$validate = new validate();
		    if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
			$result = $this->allowField(true)->save($data);
			$id = $this->rankId;
			//启用上传图片
			WSTUseResource(1, $id, $data['userrankImg']);
	        if(false !== $result){
	        	cache('WST_USER_RANK',null);
	        	Db::commit();
	        	return WSTReturn("新增成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('新增失败',-1);
        }
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$Id = (int)input('post.rankId');
		$data = input('post.');
		$data['startScore'] = (int) $data['startScore'];
		$data['endScore'] = (int) $data['endScore'];
		Db::startTrans();
		try{
			WSTUseResource(1, $Id, $data['userrankImg'], 'user_ranks', 'userrankImg');
			WSTUnset($data,'createTime');
			$validate = new validate();
		    if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
		    $result = $this->allowField(true)->save($data,['rankId'=>$Id]);
	        if(false !== $result){
	        	cache('WST_USER_RANK',null);
	        	Db::commit();
	        	return WSTReturn("编辑成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('编辑失败',-1);
        }	        
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = (int)input('post.id/d');
	    Db::startTrans();
		try{
			$data = [];
			$data['dataFlag'] = -1;
		    $result = $this->update($data,['rankId'=>$id]);
	        if(false !== $result){
	        	WSTUnuseResource('user_ranks','userrankImg',$id);
	        	cache('WST_USER_RANK',null);
	        	Db::commit();
	        	return WSTReturn("删除成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('编辑失败',-1);
        }	
	}
	
}
