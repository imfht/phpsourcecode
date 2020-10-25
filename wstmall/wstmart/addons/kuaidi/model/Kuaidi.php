<?php
namespace addons\kuaidi\model;
use think\addons\BaseModel as Base;
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
 * 快递查询业务处理
 */
class Kuaidi extends Base{
	
	/**
	 * 绑定勾子
	 */
	public function install(){
		Db::startTrans();
		try{
			$hooks = array("adminDocumentOrderView","homeDocumentOrderView","shopDocumentOrderView","afterQueryUserOrders","mobileDocumentOrderList","wechatDocumentOrderList","supplierShopDocumentOrderView","supplierDocumentOrderView","supplierAdminDocumentOrderView");
			$this->bindHoods("Kuaidi", $hooks);
			
			Db::commit();
			return true;
		}catch (\Exception $e) {
			Db::rollback();
			return false;
		}
	}
	
	/**
	 * 解绑勾子
	 */
	public function uninstall(){
		Db::startTrans();
		try{
			$hooks = array("adminDocumentOrderView","homeDocumentOrderView","shopDocumentOrderView","afterQueryUserOrders","mobileDocumentOrderList","wechatDocumentOrderList","supplierShopDocumentOrderView","supplierDocumentOrderView","supplierAdminDocumentOrderView");
			$this->unbindHoods("Kuaidi", $hooks);
			
			Db::commit();
			return true;
		}catch (\Exception $e) {
			Db::rollback();
			return false;
		}
	}
	
	public function getExpress($orderId,$uId = 0){
		$uId = ($uId>0)?$uId:session('WST_USER.userId');
		$staff = session('WST_STAFF');
		$conf = $this->getConf("Kuaidi");
		$temp = Db::name('orders')
            ->alias('o')
            ->join('__ORDER_EXPRESS__ oe','o.orderId=oe.orderId')
            ->field('o.userId,o.shopId,oe.expressId,oe.expressNo')
            ->where([["o.orderId",'=',$orderId],['oe.isExpress','=',1]])->select();
        $express = [];
		if($staff['staffId'])return $temp;
		if(!empty($temp)){
			if($temp[0]['userId']==$uId){
				return $temp;
			}else{
				$shopId = $temp[0]['shopId'];
				$suser = Db::name('shop_users')->where(['shopId'=>$shopId,'userId'=>$uId,'dataFlag'=>1])->find();
				if(!empty($suser)){
					return $temp;
				}
			}
		}
		return $express;
	}

    public function getSupplierExpress($orderId,$uId = 0){
        $uId = ($uId>0)?$uId:session('WST_USER.userId');
        $staff = session('WST_STAFF');
        $conf = $this->getConf("Kuaidi");
        $temp = Db::name('supplier_orders')
            ->alias('o')
            ->join('__SUPPLIER_ORDER_EXPRESS__ oe','o.orderId=oe.orderId')
            ->field('o.userId,o.supplierId,oe.expressId,oe.expressNo')
            ->where([["o.orderId",'=',$orderId],['oe.isExpress','=',1]])->select();
        $express = [];
        if($staff['staffId'])return $temp;
        if(!empty($temp)){
            if($temp[0]['userId']==$uId){
                return $temp;
            }else{
                $supplierId = $temp[0]['supplierId'];
                $suser = Db::name('shop_users')->where(['supplierId'=>$supplierId,'userId'=>$uId,'dataFlag'=>1])->find();
                if(!empty($suser)){
                    return $temp;
                }
            }
        }
        return $express;
    }



    public function getOrderExpress($orderId,$uId = 0){
        $uId = ($uId>0)?$uId:session('WST_USER.userId');
        $conf = $this->getConf("Kuaidi");
        $express = $this->getExpress($orderId,$uId);
        if($express["expressId"]>0){
            $expressId = $express["expressId"];
            $row = Db::name('express')->where(["expressId"=>$expressId])->find();
            $typeCom =  strtolower($row["expressCode"]);
            $typeNu = $express["expressNo"];
            $kuaidiKey = $conf["kuaidiKey"];
            $kuaidiType = $conf["kuaidiType"];
            $kuaidiCustomer = $conf["kuaidiCustomer"];
            $expressLogs = null;
            if($kuaidiType==1){
                $data = [];
                $param = '{"com":"'.$typeCom.'","num":"'.$typeNu.'"}';
                $sign = md5($param.$kuaidiKey.$kuaidiCustomer);
                $sign = strtoupper($sign);
                $data['customer'] = $kuaidiCustomer;
                $data['sign'] = $sign;
                $data['param'] = $param;
                $temp="";
                foreach ($data as $k=>$v){
                    $temp.= "$k=".urlencode($v)."&";
                }
                $data=substr($temp,0,-1);
                $url ='http://poll.kuaidi100.com/poll/query.do';
                $expressLogs = $this -> curl($url,$data);
            }else{
                $url ='http://api.kuaidi100.com/api?id='.$kuaidiKey.'&com='.$typeCom.'&nu='.
                    $typeNu.'&show=0&muti=1&order=asc';
                $expressLogs = $this -> curl($url);
            }
            return $expressLogs;
        }

    }


    public function getOrderExpresses($orderId,$expressId,$expressNo,$uId = 0){
        $uId = ($uId>0)?$uId:session('WST_USER.userId');
        $conf = $this->getConf("Kuaidi");
        $row = Db::name('express')->where(["expressId"=>$expressId])->find();
        $typeCom =  strtolower($row["expressCode"]);
        $typeNu = $expressNo;
        $kuaidiKey = $conf["kuaidiKey"];
        $kuaidiType = $conf["kuaidiType"];
        $kuaidiCustomer = $conf["kuaidiCustomer"];
        $expressLogs = null;
        if($kuaidiType==1){
            $data = [];
            $param = '{"com":"'.$typeCom.'","num":"'.$typeNu.'"}';
            $sign = md5($param.$kuaidiKey.$kuaidiCustomer);
            $sign = strtoupper($sign);
            $data['customer'] = $kuaidiCustomer;
            $data['sign'] = $sign;
            $data['param'] = $param;
            $temp="";
            foreach ($data as $k=>$v){
                $temp.= "$k=".urlencode($v)."&";
            }
            $data=substr($temp,0,-1);
            $url ='http://poll.kuaidi100.com/poll/query.do';
            $expressLogs = $this -> curl($url,$data);
        }else{
            $url ='http://api.kuaidi100.com/api?id='.$kuaidiKey.'&com='.$typeCom.'&nu='.
                    $typeNu.'&show=0&muti=1&order=asc';
            $expressLogs = $this -> curl($url);
        }
        $data = [];
        $data['expressName'] = $row['expressName'];
        $data['logs'] = json_decode($expressLogs, true);
        return $data;

    }
	
	public function getOrderInfo($uId = 0){
		$uId = ($uId>0)?$uId:session('WST_USER.userId');
		$data = array();
		$orderId = input("orderId");
		$data["goodlist"] = Db::name('orders o')->join('__ORDER_GOODS__ og','o.orderId=og.orderId')->where(["o.orderId"=>$orderId,'userId'=>$uId])->field(["goodsId","goodsImg"])->limit(1)->select();
		return $data;
	}

    public function getExpressName($expressId){
        return Db::name('express')->field('expressName')->where('expressId','=',$expressId)->find();
    }
	
	public function curl($url,$post_data=null) {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);//允许请求的内容以文件流的形式返回
    	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);//禁用https
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec($ch);
        $data = str_replace("\"",'"',$result );
        return $data;
	}
	/**
     * 判断是否有物流信息
     */
    public function checkHasExpress($orderId){
        $rs = Db::name('orders')
            ->field('deliverType,orderStatus')
            ->where([["orderId",'=',$orderId]])->find();
        if($rs["deliverType"]==1 || in_array($rs["orderStatus"],[-1,-2]))return false;
        $rs = Db::name('order_express')->where([["orderId",'=',$orderId],['isExpress','=',1]])->count();
        if($rs==0)return false;
        return true;
    }

    /**
     * 判断是否有物流信息【供货商】
     */
    public function checkSupplierHasExpress($orderId){
        $rs = Db::name('supplier_orders')
            ->field('deliverType,orderStatus')
            ->where([["orderId",'=',$orderId]])->find();
        if($rs["deliverType"]==1 || in_array($rs["orderStatus"],[-1,-2]))return false;
        $rs = Db::name('supplier_order_express')->where([["orderId",'=',$orderId],['isExpress','=',1]])->count();
        if($rs==0)return false;
        return true;
    }

	public function getExpressState($state){
		$stateTxt = "";
		switch ($state) {
			case '0':$stateTxt="运输中";break;
			case '1':$stateTxt="揽件";break;
			case '2':$stateTxt="疑难";break;
			case '3':$stateTxt="收件人已签收";break;
			case '4':$stateTxt="已退签";break;
			case '5':$stateTxt="派件中";break;
			case '6':$stateTxt="退回";break;
			default:$stateTxt="暂未获取到状态";break;
		}
		return $stateTxt;
	}
	
	
	
}
