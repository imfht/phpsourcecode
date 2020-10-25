<?php
namespace wstmart\shop\model;
use wstmart\common\model\Express as CExpress;
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
 * 门店管理员类
 */
class Express extends CExpress{
	/**
	 * 快递列表
	 */
	public function flistQuery(){
		$where = [];
		$where[] = ["e.dataFlag","=",1];
		$list = Db::name("express e")
				->field("e.expressId,e.expressName")
				->where($where)
				->select();
		return $list;
	}

	/**
	 * 快递列表
	 */
	public function pageQuery(){
		$shopId = (int)session('WST_USER.shopId');
		$ename = input("expressName");
		$where = [];
		$where[] = ["e.dataFlag","=",1];
		if($ename!="")$where[] = ["e.expressName","like",'%'.$ename.'%'];
		$page = Db::name("express e")
				->join("shop_express se","e.expressId=se.expressId and se.dataFlag=1 and se.shopId=$shopId","left")
				->field("e.expressId,e.expressName,e.expressCode,se.id,se.isEnable,se.isDefault")
				->where($where)
				->paginate(input('limit/d'))->toArray();
		return $page;
	}

	public function toggleSet(){
		$shopId = (int)session('WST_USER.shopId');
		$expressId = (int)input("expressId");
		$spExpress = Db::name("shop_express")->where(["shopId"=>$shopId,"expressId"=>$expressId,"dataFlag"=>1])->find();
		if(empty($spExpress) || $spExpress['isEnable']==0){
			return WSTReturn("无效操作", -1);
		}else{
			Db::startTrans();
			try{
				Db::name("shop_express")->where(["shopId"=>$shopId,"dataFlag"=>1])->update(['isDefault'=>0]	);
				Db::name("shop_express")->where(["shopId"=>$shopId,"expressId"=>$expressId,"dataFlag"=>1])->update(['isDefault'=>1]);
				Db::commit();
				return WSTReturn("设置成功", 1);
			}catch (\Exception $e) {
	        	Db::rollback();
	        }
		}
	}

	/**
	 * 启用快递
	 */
	public function enableExpress(){
		$shopId = (int)session('WST_USER.shopId');
		$expressId = (int)input("expressId");
		$spExpress = Db::name("shop_express")->where(["shopId"=>$shopId,"expressId"=>$expressId,"dataFlag"=>1])->find();
		Db::startTrans();
		try{
			if(empty($spExpress)){
				$cnt = Db::name("shop_express")->where(["shopId"=>$shopId,"dataFlag"=>1])->count();
				$isDefault = ($cnt==0)?1:0;
				$data = [];
				$data["expressId"] = $expressId;
				$data["isEnable"] = 1;
				$data["isDefault"] = $isDefault;
				$data["dataFlag"] = 1;
				$data["shopId"] = $shopId;
				$id = Db::name("shop_express")->insertGetId($data);

				$data = [];
				$data["shopExpressId"] = $id;
				$data["tempName"] = "默认模板(全国)";
				$data["tempType"] = 0;
				$data["provinceIds"] = '';
				$data["cityIds"] = '';
				$data["weightStart"] = 0;
				$data["weightStartPrice"] = 0;
				$data["weightContinue"] = 0;
				$data["weightContinuePrice"] = 0;
				$data["shopId"] = $shopId;
				$data["createTime"] = date("Y-m-d H:i:s");
				Db::name("shop_freight_template")->insert($data);
			}else{
				$id = $spExpress["id"];
				Db::name("shop_express")->where(["id"=>$id])->update(["isEnable"=>1]);
			}
			Db::commit();
		}catch (\Exception $e) {
        	Db::rollback();
        }
		return WSTReturn("",1);
	}

	/**
	 * 停用快递
	 */
	public function disableExpress(){
		$shopId = (int)session('WST_USER.shopId');
		$id = (int)input("id");
		Db::name("shop_express")->where(["id"=>$id,"shopId"=>$shopId])->update(["isEnable"=>0]);
		return WSTReturn("",1);
	}
	/**
	 * 快递列表
	 */
	public function listQuery2(){
		$shopId = (int)session('WST_USER.shopId');
		$tname = input("tempName");
		$shopExpressId = (int)input("shopExpressId");
		$where = [];
		$where[] = ["dataFlag","=",1];
		$where[] = ["shopExpressId","=",$shopExpressId];
		if($tname!="")$where[] = ["tempName","like",'%'.$tname.'%'];
		$list = Db::name("shop_freight_template")
				->where($where)
				->paginate(input('limit/d'))->toArray();
		return $list;
	}

	public function getById(){
		$id = (int)input("id");
		$rs = $this->where(["id"=>$id])->find();
		return $rs;
	}

	public function getFreightById(){
		$shopId = (int)session('WST_USER.shopId');
		$id = (int)input("id");
		$rs = Db::name("shop_freight_template")->where(["id"=>$id,"shopId"=>$shopId])->find();
		return $rs;
	}

	public function getShopExpressInfo($shopExpressId){
		$shopId = (int)session('WST_USER.shopId');
		$rs = Db::name("shop_express se")
			->join("express e","e.expressId=se.expressId","inner")
			->field("e.expressId,e.expressName")
			->where(["se.id"=>$shopExpressId,"shopId"=>$shopId])
			->find();
		return $rs;
	}

	public function getOtherAreas($id,$shopExpressId){
		$shopId = (int)session('WST_USER.shopId');
		$where = [];
		if($id>0)$where[] = ["id","<>",$id];
		$where[] = ["dataFlag","=",1];
		$where[] = ["tempType","=",1];
		$where[] = ["shopId","=",$shopId];
		$where[] = ["shopExpressId","=",$shopExpressId];
		$list = Db::name("shop_freight_template")->where($where)->field("id,provinceIds,cityIds")->select();

		$otherProvinceIds = [];
		$otherCityIds = [];
		foreach ($list as $key => $vo) {
			$otherProvinceIds = array_merge(explode(",", $vo['provinceIds']),$otherProvinceIds);
			$otherCityIds = array_merge(explode(",", $vo['cityIds']),$otherCityIds);
		}
		$data = [];
		$data['otherProvinceIds'] = array_unique($otherProvinceIds);
		$data['otherCityIds'] = array_unique($otherCityIds);
		return $data;
	}
	
	/**
	 * 新增运费模板
	 */
	public function add(){
		$shopId = (int)session('WST_USER.shopId');
		$data = input('post.');
		if($data["tempName"]=="")return WSTReturn('请输入模板名称',-1);
		if($data["provinceIds"]=="")return WSTReturn('请选择地区',-1);
		$shopExpressId = (int)$data['shopExpressId'];
		$otherAreas = $this->getOtherAreas(0,$shopExpressId);
		$otherCityIds = $otherAreas["otherCityIds"];
		$cityIds = explode(",",$data['cityIds']);

		foreach ($cityIds as $key => $cityId) {
			if(in_array($cityId,$otherCityIds)){
				return WSTReturn('选择地区已存在于其他运费模板',-1);
			}
		}
		$temp = [];
		$temp["shopExpressId"] = $shopExpressId;
		$temp["shopId"] = $shopId;
		$temp["tempName"] = $data['tempName'];
		$temp["provinceIds"] = $data['provinceIds'];
		$temp["cityIds"] = $data['cityIds'];


		$temp["buyNumStart"] = (float)$data['buyNumStart'];
		$temp["buyNumStartPrice"] = (float)$data['buyNumStartPrice'];
		$temp["buyNumContinue"] = (float)$data['buyNumContinue'];
		$temp["buyNumContinuePrice"] = (float)$data['buyNumContinuePrice'];

		$temp["weightStart"] = (float)$data['weightStart'];
		$temp["weightStartPrice"] = (float)$data['weightStartPrice'];
		$temp["weightContinue"] = (float)$data['weightContinue'];
		$temp["weightContinuePrice"] = (float)$data['weightContinuePrice'];

		$temp["volumeStart"] = (float)$data['volumeStart'];
		$temp["volumeStartPrice"] = (float)$data['volumeStartPrice'];
		$temp["volumeContinue"] = (float)$data['volumeContinue'];
		$temp["volumeContinuePrice"] = (float)$data['volumeContinuePrice'];

		$temp["dataFlag"] = 1;
		$temp["tempType"] = 1;
		$temp["createTime"] = date("Y-m-d H:i:s");
		$result = Db::name("shop_freight_template")->insert($temp);
		if(false !== $result){
        	return WSTReturn("新增成功", 1);
        }
        return WSTReturn('新增失败',-1);
	}

	/**
     * 修改运费模板
     */
    public function edit(){

    	$shopId = (int)session('WST_USER.shopId');
    	Db::startTrans();
		try{
	    	$shopId = (int)session('WST_USER.shopId');
			$data = input('post.');
			$id = (int)$data["id"];
			$ftemp = Db::name("shop_freight_template")->where(["shopId"=>$shopId,"id"=>$id])->find();
			if($data["tempName"]=="")return WSTReturn('请输入模板名称',-1);
			if($ftemp["tempType"]==1 && $data["provinceIds"]=="")return WSTReturn('请选择省份',-1);
			
			$temp = [];
			$temp["tempName"] = $data['tempName'];
			if($ftemp["tempType"]==1){
				$temp["provinceIds"] = $data['provinceIds'];
				$temp["cityIds"] = $data['cityIds'];
				$otherAreas = $this->getOtherAreas($id,$ftemp['shopExpressId']);
				$otherCityIds = $otherAreas["otherCityIds"];
				$cityIds = explode(",",$data['cityIds']);

				foreach ($cityIds as $key => $cityId) {
					if(in_array($cityId,$otherCityIds)){
						return WSTReturn('选择地区已存在于其他运费模板',-1);
					}
				}
			}
			$temp["buyNumStart"] = (float)$data['buyNumStart'];
			$temp["buyNumStartPrice"] = (float)$data['buyNumStartPrice'];
			$temp["buyNumContinue"] = (float)$data['buyNumContinue'];
			$temp["buyNumContinuePrice"] = (float)$data['buyNumContinuePrice'];

			$temp["weightStart"] = (float)$data['weightStart'];
			$temp["weightStartPrice"] = (float)$data['weightStartPrice'];
			$temp["weightContinue"] = (float)$data['weightContinue'];
			$temp["weightContinuePrice"] = (float)$data['weightContinuePrice'];

			$temp["volumeStart"] = (float)$data['volumeStart'];
			$temp["volumeStartPrice"] = (float)$data['volumeStartPrice'];
			$temp["volumeContinue"] = (float)$data['volumeContinue'];
			$temp["volumeContinuePrice"] = (float)$data['volumeContinuePrice'];
			Db::name("shop_freight_template")
						->where(["shopId"=>$shopId,"id"=>$id])
						->update($temp);
			Db::commit();
			return WSTReturn("修改成功", 1);
			
		}catch (\Exception $e) {
        	Db::rollback();
        	return WSTReturn("修改失败", -1);
        }
    }

	/**
	 * 删除运费模板
	 */
	public function del(){
		$shopId = (int)session('WST_USER.shopId');
		$id = (int)input('post.id');
		$data = [];
		$data['dataFlag'] = -1;
		Db::startTrans();
		try{
			$where = [];
			$where[] = ["shopId",'=',$shopId];
			$where[] = ["id",'=',$id];
			$where[] = ["tempType",'=',1];
	   		Db::name("shop_freight_template")
	   		->where(["shopId"=>$shopId,"id"=>$id])
	   		->update(['dataFlag'=>-1]);
	   		Db::commit();
        	return WSTReturn("删除成功", 1);
     	}catch (\Exception $e) {
        	Db::rollback();
        }
        return WSTReturn('删除失败',-1);
	}

}
