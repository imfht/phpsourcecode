<?php
namespace wstmart\common\model;
use think\Db;
use think\Log;
use wstmart\common\validate\Invoices as Validate;
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
 * 发票信息类
 */
class Invoices extends Base{
	/**
	 * 获取单条记录
	 */
	public function getById($id, $uId=0){
		$userId = ($uId>0)?$uId:(int)session('WST_USER.userId');
		return $this->where(['id'=>$id, 'userId'=>$userId])->find();
	}
	/**
	 * 获取发票列表
	 */
	public function listQuery($userId = 0){
		$userId = ($userId>0)?$userId:(int)session('WST_USER.userId');
		return $this->where(['userId'=>$userId,'dataFlag'=>1])->select();
	}


	/**
	* 列表查询
	*/
	public function pageQuery($limit=0,$uId=0){
		$userId = $uId==0?(int)session('WST_USER.userId'):$uId;
		$where = [];
		$type = input('invoiceType',-1);
		if($type>-1)$where['invoiceType'] = $type;
		return $this->where(['userId'=>$userId,'dataFlag'=>1])->where($where)->limit($limit)->select();
	}
	/**
	* 新增
	*/
	public function add($uId=0){
		$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
		$count = $this->where('userId='.$userId.' and dataFlag=1')->count();
		if($count>20)return WSTReturn('发票信息不能超过20条');
		$data = input('param.');
		$data['userId'] = $userId;
		$data['createTime'] = date('Y-m-d H:i:s');
		$validate = new Validate;
		if (!$validate->scene('add')->check($data)) {
			return WSTReturn($validate->getError());
		}else{
            $rs = $this->allowField(true)->save($data);
            if($rs!==false) return WSTReturn('新增成功',1,['id'=>$this->id]);
		}
		
		return WSTReturn($this->getError(),-1);
	}
	/**
	* 修改
	*/
	public function edit($uId=0){
		$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
		$data = input('param.');
		$id = input('param.id');
        unset($data['id']);
		$validate = new Validate;

		//数据验证
		if (!$validate->scene('edit')->check($data)) {
			return WSTReturn($validate->getError());
		}else{
			$rs = $this->field(true)->where(['id'=>$id,'userId'=>$userId])->update($data);
			if($rs!==false){
				return WSTReturn('修改成功',1);
			}else{
				WSTReturn($this->getError());
			}
		}
		return WSTReturn('编辑失败',-1);
	}
	/**
	* 删除
	*/
	public function del($uId=0){
		$id = (int)input('id');
		$userId = ($uId>0)?$uId:(int)session('WST_USER.userId');
		$rs = $this->where(['id'=>$id,'userId'=>$userId])->setField(['dataFlag'=>-1]);
		if($rs!==false)return WSTReturn('删除成功',1);
		return WSTReturn('删除失败');
	}
	/**
	* 获取发票信息【存入订单表字段】
	*/
	public function getInviceInfo($id,$uId=0){
		if($id==0)return json_encode(['invoiceHead'=>'个人']);// 所需发票为个人时
		$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
		$rs = $this->where(['id'=>$id,'userId'=>$userId,'dataFlag'=>1])->find();
		if(empty($rs))return [];
		$jsonArr = [];
		$jsonArr['type'] = 0;//0:纸质发票 1:电子发票【后续扩展】
		$jsonArr['invoiceHead'] = $rs['invoiceHead'];
		$jsonArr['invoiceCode'] = $rs['invoiceCode'];
		$jsonArr['id'] = $rs['id'];
		return json_encode($jsonArr);
	}

}
