<?php
namespace wstmart\common\model;
use wstmart\common\validate\UserAddress as Validate;
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
 * 用户地址
 */
class UserAddress extends Base{
    
    protected $pk = 'addressId';
     /**
      * 获取列表
      */
      public function listQuery($userId){
         $where = ['userId'=>(int)$userId,'dataFlag'=>1];
         $rs = $this->order('isDefault asc, addressId desc')->where($where)->select();
         $areaIds = [];
         $areaMaps = [];
         foreach ($rs as $key => $v){
         	 $tmp = explode('_',$v['areaIdPath']);
         	 foreach ($tmp as $vv){
         		if($vv=='')continue;
         	    if(!in_array($vv,$areaIds))$areaIds[] = $vv;
         	 }
         	 $rs[$key]['areaId2'] = $tmp[1];
         }
         if(!empty($areaIds)){
	         $areas = Db::name('areas')->where([['areaId','in',$areaIds],['isShow','=',1],['dataFlag','=',1]])->field('areaId,areaName')->select();
	         foreach ($areas as $v){
	         	 $areaMaps[$v['areaId']] = $v['areaName'];
	         }
	         foreach ($rs as $key => $v){
	         	$tmp = explode('_',$v['areaIdPath']);
	         	$areaNames = [];
                $isFind = true;
		        foreach ($tmp as $vv){
	         		if($vv=='')continue;
                    if(!isset($areaMaps[$vv])){
                        $isFind = false;
                        continue;
                    }
	         	    $areaNames[] = $areaMaps[$vv];
	            }
                if($isFind){
    	         	$rs[$key]['areaName'] = implode('',$areaNames);
    	         	$rs[$key]['areaName1'] = $areaMaps[$v['areaId2']];
                }
	         }
             $tmp = [];
             for($i=count($rs)-1;$i>=0;$i--){
                if(isset($rs[$i]['areaName']))$tmp[] = $rs[$i];
             }
             $rs = $tmp;
         }
         return $rs;
      }
    /**
    *  获取用户信息
    */
    public function getById($id, $uId=0){
        $userId = ((int)$uId==0)?(int)session('WST_USER.userId'):$uId;
    	$rs = $this->get(['addressId'=>$id,'userId'=>$userId,'dataFlag'=>1]);
        if(empty($rs))return [];
        $areaIds = [];
        $areaMaps = [];
        $tmp = explode('_',$rs['areaIdPath']);
        $rs['areaId2'] = $tmp[1];
        foreach ($tmp as $vv){
         	if($vv=='')continue;
         	if(!in_array($vv,$areaIds))$areaIds[] = $vv;
        }
        if(!empty($areaIds)){
	         $areas = Db::name('areas')->where([['areaId','in',$areaIds],['isShow','=',1],['dataFlag','=',1]])->field('areaId,areaName')->select();
	         foreach ($areas as $v){
	         	 $areaMaps[$v['areaId']] = $v['areaName'];
	         }
	         $tmp = explode('_',$rs['areaIdPath']);
	         $areaNames = [];
		     foreach ($tmp as $vv){
	         	 if($vv=='')continue;
                 if(!isset($areaMaps[$vv]))return [];
	         	 $areaNames[] = $areaMaps[$vv];
	         	 $rs['areaName'] = implode('',$areaNames);
	         }
         }
        return $rs;
    }
    /**
     * 新增
     */
    public function add($uId=0){
        $data = input('post.');
        unset($data['addressId']);
        $data['userId'] = ((int)$uId==0)?(int)session('WST_USER.userId'):$uId;
        //先判断一下用户已经创建了多少个地址，限制不能超过20个
        $count = $this->where('userId='.$data['userId'].' and dataFlag=1')->count();
        if($count>=20)return WSTReturn('请勿新增超过20个收货地址');
        $data['createTime'] = date('Y-m-d H:i:s');
        if($data['userId']==0)return WSTReturn('新增失败，请先登录');
        // 检测是否存在下级地区
        $hasChild = model('Areas')->hasChild(input('areaId'));
        if($hasChild)return WSTReturn('请选择完整的地区信息',-1);

        $areaIds = model('Areas')->getParentIs((int)input('areaId'));
        if(!empty($areaIds))$data['areaIdPath'] = implode('_',$areaIds)."_";
        $validate = new Validate;
        if (!$validate->scene('add')->check($data)) {
        	return WSTReturn($validate->getError());
        }else{
        	$result = $this->allowField(true)->save($data);
        }
        if(false !== $result){
            //修改默认地址
            if((int)input('post.isDefault')==1){
            	$this->where("addressId != $this->addressId and userId=".$data['userId'])->setField('isDefault',0);
            }
            return WSTReturn("新增成功", 1,['addressId'=>$this->addressId]);
        }else{
            return WSTReturn($this->getError(),-1);
        }
    }
    /**
     * 编辑资料
     */
    public function edit($uId=0){
        $userId = ((int)$uId==0)?(int)session('WST_USER.userId'):$uId;
        $id = (int)input('post.addressId');
        $data = input('post.');
        unset($data['addressId']);
        // 检测是否存在下级地区
        $hasChild = model('Areas')->hasChild(input('areaId'));
        if($hasChild)return WSTReturn('请选择完整的地区信息',-1);
        
        $areaIds = model('Areas')->getParentIs((int)input('areaId'));
        if(!empty($areaIds))$data['areaIdPath'] = implode('_',$areaIds)."_";
        $validate = new Validate;
        if (!$validate->scene('edit')->check($data)) {
        	return WSTReturn($validate->getError());
        }else{
        	$result = $this->allowField(true)->save($data,['addressId'=>$id,'userId'=>$userId]);
        }
        //修改默认地址
        if((int)input('post.isDefault')==1)
          $this->where("addressId != $id and userId=".$userId)->setField('isDefault',0);
        if(false !== $result){
            return WSTReturn("编辑成功", 1);
        }else{
            return WSTReturn($this->getError(),-1);
        }
    }
    /**
     * 删除
     */
    public function del($uId=0){
        $userId = ((int)$uId==0)?(int)session('WST_USER.userId'):$uId;
        $id = input('post.id/d');
        $data = [];
        $data['dataFlag'] = -1;
        $result = $this->update($data,['addressId'=>$id,'userId'=>$userId]);
        if(false !== $result){
            return WSTReturn("删除成功", 1);
        }else{
            return WSTReturn($this->getError(),-1);
        }
    }

    /**
    * 设置为默认地址
    */
    public function setDefault($uId=0){
        $userId = ((int)$uId==0)?(int)session('WST_USER.userId'):$uId;
        $id = (int)input('post.id');
        $this->where([['addressId','<>',$id],['userId','=',$userId]])->setField('isDefault',0);
        $rs = $this->where("addressId = $id and userId=".$userId)->setField('isDefault',1);
        if(false !== $rs){
            return WSTReturn("设置成功", 1);
        }else{
            return WSTReturn($this->getError(),-1);
        }
    }
    /**
     * 获取默认地址
     */
    public function getDefaultAddress($uId=0){
    	$userId = ((int)$uId==0)?(int)session('WST_USER.userId'):$uId;
    	$where = ['userId'=>$userId,'dataFlag'=>1];
        $rs = $this->where($where)->order('isDefault desc,addressId desc')->find();
        if(empty($rs))return [];
        $areaIds = [];
        $areaMaps = [];
        $tmp = explode('_',$rs['areaIdPath']);
        $rs['areaId2'] = $tmp[1];
        foreach ($tmp as $vv){
         	if($vv=='')continue;
         	if(!in_array($vv,$areaIds))$areaIds[] = $vv;
        }
        if(!empty($areaIds)){
	         $areas = Db::name('areas')->where([['areaId','in',$areaIds],['isShow','=',1],['dataFlag','=',1]])->field('areaId,areaName')->select();
	         foreach ($areas as $v){
	         	 $areaMaps[$v['areaId']] = $v['areaName'];
	         }
	         $tmp = explode('_',$rs['areaIdPath']);
	         $areaNames = [];
		     foreach ($tmp as $vv){
	         	 if($vv=='')continue;
                 if(!isset($areaMaps[$vv]))return [];
	         	 $areaNames[] = $areaMaps[$vv];
	         	 $rs['areaName'] = implode('',$areaNames);
	         }
         }
         return $rs;
    }
}
