<?php
namespace wstmart\common\model;
use wstmart\common\validate\ShopApplys as Validate;
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
 * 商家入驻类
 */
class ShopApplys extends Base{
	protected $pk = 'id';

	/**
	 * 保存商家入驻
	 */
	public function add($uId=0){
		$userId = $uId > 0 ? $uId : (int)session('WST_USER.userId');
		Db::startTrans();
		try{
			$data['userId'] = $userId?$userId:0;
			$data['linkman'] = input('linkman');
			$data['linkPhone'] = input('linkPhone');
			$data['applyIntention'] = input('applyIntention');
			$data['applyStatus'] = 0;
			$data['createTime'] = date('Y-m-d H:i:s');
			$validate = new Validate;
			if (!$validate->scene('add')->check($data)) {
				return WSTReturn($validate->getError());
			}else{
				$rs = $this->save($data);
				if($rs !==false){
					Db::commit();
					return WSTReturn('申请提交成功，我们将会尽快联系您',1);
				}
			}
		}catch (\Exception $e) {
		    Db::rollback();
	    }
	    return WSTReturn('申请提交失败',-1);
	}

    /**
     * 获取是否已经填写商家入驻
     */
	public function isApply($uId=0){
        $userId = $uId > 0 ? $uId : (int)session('WST_USER.userId');
        $rs = $this->where([['userId','=',$userId],['dataFlag','=',1],['applyStatus','=','0']])->find();
        if(empty($rs)){
            return false;
        }else{
            return true;
        }
    }
}
