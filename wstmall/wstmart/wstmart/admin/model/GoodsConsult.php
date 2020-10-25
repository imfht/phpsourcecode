<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\GoodsConsult as validate;
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
 * 商品咨询业务处理
 */
class GoodsConsult extends Base{
	/**
	 * 分页
	 */
	public function pageQuery(){
		$type = (int)input('type');
		$consultKey = input('consultKey');
		$where[] = ['gc.dataFlag','=',1];
		// 筛选类别
		if($type>0)$where[] = ['gc.consultType','=',$type];
		// 关键字搜索
		if($consultKey!='')$where[] = ['gc.consultContent','like',"%$consultKey%"];
        $rs = $this->alias('gc')
        		   ->join('__GOODS__ g','g.goodsId=gc.goodsId')
        		   ->join('__USERS__ u','u.userId=gc.userId','left')
        		   ->field('gc.*,u.loginName,g.goodsId,g.goodsImg,g.goodsName')
        		   ->where($where)
        		   ->order('gc.createTime desc')
        		   ->paginate(input('limit/d'))->toArray();
        if(!empty($rs['data'])){
        	foreach($rs['data'] as $k=>&$v){
        		// 解义
        		$v['consultContent'] = htmlspecialchars_decode($v['consultContent']);
        	}
        }
        return $rs;
	}
	public function getById($id){
		return $this->alias('gc')
					->join('__GOODS__ g','gc.goodsId=g.goodsId')
					->join('__USERS__ u','gc.userId=u.userId','left')
					->field('gc.*,g.goodsImg,g.goodsId,g.goodsName,u.loginName')
					->where(['id'=>$id])
					->find();
	}
    /**
	 * 编辑
	 */
	public function edit(){
		$Id = input('post.id/d',0);
		$data = input('post.');
		WSTUnset($data,'createTime');
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
	    $id = input('post.id/d',0);
		$data = [];
		$data['dataFlag'] = -1;
	    $result = $this->update($data,['id'=>$id]);
        if(false !== $result){
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	
}
