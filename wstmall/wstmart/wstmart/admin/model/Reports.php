<?php 
namespace wstmart\admin\model;
use think\Db;
use Env;
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
 * 报表业务处理
 */
class Reports extends Base{   
    /**
     * 获取商品销售统计
     */
    public function topSaleGoodsByPage(){
    	$start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
    	$end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $prefix = config('database.prefix');
    	return Db::table($prefix.'order_goods')
          ->alias([$prefix.'order_goods'=>'og',$prefix.'orders'=>'o',$prefix.'goods'=>'g',$prefix.'shops'=>'s'])
          ->field('og.goodsId,g.goodsName,goodsSn,s.shopId,shopName,sum(og.goodsNum) goodsNum,og.goodsImg')
    	  ->join($prefix.'orders','og.orderId=o.orderId')
    	  ->join($prefix.'goods','og.goodsId=g.goodsId')
    	  ->join($prefix.'shops','g.shopId=s.shopId')
    	  ->order('goodsNum','desc')
    	  ->whereTime('o.createTime','between',[$start,$end])
          ->where('(o.payType=0 or (o.payType=1 and o.isPay=1)) and o.dataFlag=1')->group('og.goodsId,g.goodsName,goodsSn,s.shopId,shopName,og.goodsImg')
          ->paginate(input('limit/d'));
    }
    /**
     * 获取店铺销售统计
     */
    public function topShopSalesByPage(){
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $prefix = config('database.prefix');
        $rs = Db::table($prefix.'shops')
                 ->alias([$prefix.'shops'=>'s',$prefix.'orders' => 'o'])
                 ->field('s.shopId,s.shopImg,s.shopName,sum(o.totalMoney) totalMoney,count(o.orderId) orderNum')
                 ->join($prefix.'orders o','s.shopId=o.shopId')
                 ->order('totalMoney desc,orderNum desc')
                 ->whereTime('o.createTime','between',[$start,$end])
                 ->where('(payType=0 or (payType=1 and o.isPay=1)) and o.dataFlag=1 and orderStatus=2')
                 ->group('o.shopId')
                 ->paginate(input('limit/d'))->toArray();
        foreach($rs['data'] as $k=>$v){
            $onLineArr = Db::name('orders')
                 ->whereTime('createTime','between',[$start,$end])
                 ->field('sum(totalMoney) totalMoney,sum(realTotalMoney) realTotalMoney')
                 ->where('payType=1 and isPay=1 and dataFlag=1 and orderStatus=2')
                 ->where(['shopId'=>$v['shopId']])
                 ->find();
            $rs['data'][$k]['onLinePayMoney'] = (float)$onLineArr['totalMoney'];// 在线支付总金额
            $rs['data'][$k]['onLinePayTrueMoney'] = (float)$onLineArr['realTotalMoney'];// 在线支付实际金额
            $rs['data'][$k]['offLinePayMoney'] = (float)
            Db::name('orders')
                 ->whereTime('createTime','between',[$start,$end])
                 ->where('payType=0 and dataFlag=1 and orderStatus=2')
                 ->where(['shopId'=>$v['shopId']])
                 ->value('sum(totalMoney)');;// 货到付款金额
        }
        return $rs;

    }

    /**
     * 获取销售额
     */
    public function statSales(){
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $payType = (int)input('payType',-1);
        $rs = Db::field('left(createTime,10) createTime,orderSrc,sum(totalMoney) totalMoney')->name('orders')->whereTime('createTime','between',[$start,$end])
                ->where('((payType=0 or (payType=1 and isPay=1)) and dataFlag=1) '.(in_array($payType,[0,1])?" and payType=".$payType:''))
                ->order('createTime asc')
                ->group('left(createTime,10),orderSrc')->select();
        $rdata = [];
        if(count($rs)>0){
            $days = [];
            $payTypes = [0,1,2,3,4,5];
            $tmp = [];
            foreach($rs as $key => $v){
                if(!in_array($v['createTime'],$days))$days[] = $v['createTime'];
                $tmp[$v['orderSrc']."_".$v['createTime']] = $v['totalMoney'];
            }
            $rdata['map'] = ['p0'=>0,'p1'=>0,'p2'=>0,'p3'=>0,'p4'=>0,'p5'=>0];
            foreach($days as $v){
                $total = 0;
                foreach($payTypes as $p){
                    $pv = isset($tmp[$p."_".$v])?$tmp[$p."_".$v]:0;
                    $rdata['p'.$p][] = (float)$pv;
                    $total = $total + (float)$pv;
                    $rdata['map']['p'.$p] = $rdata['map']['p'.$p] + (float)$pv;
                }
                $rdata['total'][] = $total;
            }
            $rdata['days'] = $days;
        }
        return WSTReturn('',1,$rdata);
    }

    /**
     * 获取订单统计
     */
    public function statOrders(){
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $payType = (int)input('payType',-1);
        $rs = Db::field('left(createTime,10) createTime,orderSrc,count(orderId) orderNum')->name('orders')->whereTime('createTime','between',[$start,$end])
                ->where('((payType=0 or (payType=1 and isPay=1)) and dataFlag=1) '.(in_array($payType,[0,1])?" and payType=".$payType:''))
                ->order('createTime asc')
                ->group('left(createTime,10),orderSrc')->select();
        $rdata = [];
        if(count($rs)>0){
            $days = [];
            $payTypes = [0,1,2,3,4,5];
            $tmp = [];
            foreach($rs as $key => $v){
                if(!in_array($v['createTime'],$days))$days[] = $v['createTime'];
                $tmp[$v['orderSrc']."_".$v['createTime']] = $v['orderNum'];
            }
            $rdata['map'] = ['p0'=>0,'p1'=>0,'p2'=>0,'p3'=>0,'p4'=>0,'p5'=>0];
            foreach($days as $v){
                $total = 0;
                foreach($payTypes as $p){
                    $pv = isset($tmp[$p."_".$v])?$tmp[$p."_".$v]:0;
                    $rdata['p'.$p][] = (float)$pv;
                    $total = $total + (float)$pv;
                    $rdata['map']['p'.$p] = $rdata['map']['p'.$p] + (float)$pv;
                }
                $rdata['total'][] = $total;
            }
            $rdata['days'] = $days;
        }
        return WSTReturn('',1,$rdata);
    }
    /*首页获取订单数量*/
    public function getOrders(){
    	$data = cache('orderData');
        if(empty($data)){
        	$start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
            $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
            $payType = -1;
            $rs = Db::field('left(createTime,10) createTime,orderSrc,count(orderId) orderNum')->name('orders')->whereTime('createTime','between',[$start,$end])
                    ->where('((payType=0 or (payType=1 and isPay=1)) and dataFlag=1) '.(in_array($payType,[0,1])?" and payType=".$payType:''))
                    ->order('createTime asc')
                    ->group('left(createTime,10),orderSrc')->select();
            $rdata = [];
         if(count($rs)>0){
            $days = [];
            $tmp = [];
			$payTypes = [0,1,2,3,4];
            foreach($rs as $key => $v){
                if(!in_array($v['createTime'],$days))$days[] = $v['createTime'];
                $tmp[$v['orderSrc']."_".$v['createTime']] = $v['orderNum'];
            }
            foreach($days as $v){
		 		 $total = 0;
				foreach($payTypes as $p){
                    $pv = isset($tmp[$p."_".$v])?$tmp[$p."_".$v]:0;
                    $total = $total + (float)$pv;
                }
                $rdata['total'][] = $total;
            }
            $rdata['days'] = $days;
			cache('orderData',$rdata,7200);
          }
        }else{
        	$rdata = cache('orderData');
        }
       return WSTReturn('',1,$rdata);
       }
    /**
     * 获取新增用户
     */
    public function statNewUser(){
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $urs = Db::field('left(createTime,10) createTime,count(userId) userNum')
                ->name('users')
                ->whereTime('createTime','between',[$start,$end])
                ->where(['dataFlag'=>1,'userType'=>0])
                ->order('createTime asc')
                ->group('left(createTime,10)')
                ->select();
        $srs = Db::field('left(createTime,10) createTime,count(shopId) userNum')
                ->name('shops')
                ->whereTime('createTime','between',[$start,$end])
                ->where(['dataFlag'=>1])
                ->order('createTime asc')
                ->group('left(createTime,10)')
                ->select();
        $rdata = [];
        $days = [];
        $tmp = [];
        if(count($urs)>0){
            foreach($urs as $key => $v){
                if(!in_array($v['createTime'],$days))$days[] = $v['createTime'];
                $tmp["0_".$v['createTime']] = $v['userNum'];
            }
        }
        if(count($srs)>0){
            foreach($srs as $key => $v){
                if(!in_array($v['createTime'],$days))$days[] = $v['createTime'];
                $tmp["1_".$v['createTime']] = $v['userNum'];
            }
        }
        sort($days);
        foreach($days as $v){
            $rdata['u0'][] =  isset($tmp['0_'.$v])?$tmp['0_'.$v]:0;
            $rdata['u1'][] =  isset($tmp['1_'.$v])?$tmp['1_'.$v]:0;
        }
        $rdata['days'] = $days;
        return WSTReturn('',1,$rdata);
    }
    /**
     * 会员登录统计
     */
    public function statUserLogin(){
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $prefix = config('database.prefix');
        $sql ='select createTime,userType,count(userId) userNum from ( 
             SELECT left(loginTime,10) createTime,`userType`,u.userId
                FROM `'.$prefix.'users` `u` INNER JOIN `'.$prefix.'log_user_logins` `lg` ON `u`.`userId`=`lg`.`userId` 
                WHERE  `loginTime` BETWEEN "'.$start.'" AND "'.$end.'"  AND (  dataFlag=1 )
                GROUP BY left(loginTime,10),userType,lg.userId
              ) a GROUP BY createTime, userType ORDER BY createTime asc ';
        $rs = Db::query($sql);  
        $rdata = [];
        if(count($rs)>0){
            $days = [];
            $tmp = [];
            foreach($rs as $key => $v){
                if(!in_array($v['createTime'],$days))$days[] = $v['createTime'];
                $tmp[$v['userType']."_".$v['createTime']] = $v['userNum'];
            }
            foreach($days as $v){
                $rdata['u0'][] = isset($tmp['0_'.$v])?$tmp['0_'.$v]:0;
                $rdata['u1'][] = isset($tmp['1_'.$v])?$tmp['1_'.$v]:0;
            }
            $rdata['days'] = $days;
        }
        return WSTReturn('',1,$rdata);
    }


    /**
     * 订单销售额统计
     */
    public function ordereSaleMoneyByPage(){
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $payType = (int)input("payType",-1);
        $orderStatus = (int)input("orderStatus",-1);
        $where = [];
        if($orderStatus>=0){
            $where[] = ["orderStatus","=",$orderStatus]; 
        }else{
            $where[] = ["orderStatus",">=",0]; 
        }
        if($payType>=0){
            $where[] = ["payType","=",$payType]; 
        }
        $page =  Db::name("orders")->alias("o")
                ->join('users u','u.userId=o.userId',"inner")
                ->field('o.orderId,o.orderNo,o.totalMoney,o.realTotalMoney,o.payType,o.scoreMoney,o.createTime,orderStatus,u.userName,u.loginName,u.userPhone')
                ->order('o.orderId','desc')
                ->whereTime('o.createTime','between',[$start,$end])
                ->where($where)
                ->paginate(input('limit/d'))->toArray();
        $rs = Db::name("orders")->alias("o")
                ->join('users u','u.userId=o.userId',"inner")
                ->whereTime('o.createTime','between',[$start,$end])
                ->where($where)
                ->field("sum(realTotalMoney) totalRealTotalMoney,sum(scoreMoney) totalScoreMoney")
                ->find();
        $totalRealTotalMoney = $rs["totalRealTotalMoney"]; 
        $totalScoreMoney = $rs["totalScoreMoney"]; 
        foreach($page['data'] as $k=>$v){
            $page['data'][$k]['userName'] = ($v['userName']!="")?$v['userName']:$v['loginName'];
            $page['data'][$k]['payType'] = WSTLangPayType($v['payType']);
            $page['data'][$k]['status'] = WSTLangOrderStatus($v['orderStatus']);
            $page['data'][$k]['totalScoreMoney'] = $totalScoreMoney;
            $page['data'][$k]['totalRealTotalMoney'] = $totalRealTotalMoney;
        }
        return $page;
    }


    /**
     * 充值统计报表
     */
    public function rechargeMoneyByPage(){
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $where = [];
        $where = [];
        $targetType = input('targetType',-1);
        if(in_array($targetType,[0,1]))$where[] = ['targetType','=',$targetType];
        
        $where[] = ['dataSrc','=', 4];
        $where[] = ['moneyType','=', 1];
        $page =  Db::name("log_moneys")
                ->order('id','desc')
                ->whereTime('createTime','between',[$start,$end])
                ->where($where)
                ->paginate(input('limit/d'))->toArray();
        
        $rs =  Db::name("log_moneys")
                ->order('id','desc')
                ->whereTime('createTime','between',[$start,$end])
                ->where($where)
                ->field("sum(money) totalRechargeMoney,sum(giveMoney) totalGiveMoney")
                ->find();
        $totalRechargeMoney = (float)$rs["totalRechargeMoney"];
        $totalGiveMoney = (float)$rs["totalGiveMoney"];
        if(count($page['data'])>0){
            $userIds = [];
            $shopIds = [];
            foreach ($page['data'] as $key => $v) {
                if($v['targetType']==0)$userIds[] = $v['targetId'];
                if($v['targetType']==1)$shopIds[] = $v['targetId'];
            }
            $userMap = [];
            if(count($userIds)>0){
                $user = Db::name('users')->where([['userId','in',$userIds]])->field('userId,loginName,userName')->select();
                foreach ($user as $key => $v) {
                    $userMap["0_".$v['userId']] = $v; 
                }
            }
            if(count($shopIds)>0){
                $user = Db::name('shops')->alias('s')
                          ->join('__USERS__ u','u.userId=s.userId')
                          ->where([['shopId','in',$shopIds]])
                          ->field('s.shopId,u.loginName,s.shopName as userName')
                          ->select();
                foreach ($user as $key => $v) {
                    $userMap["1_".$v['shopId']] = $v; 
                }
            }
            foreach ($page['data'] as $key => $v) {
                $page['data'][$key]['loginName'] = $userMap[$v['targetType']."_".$v['targetId']]['loginName'];
                $page['data'][$key]['userName'] = $userMap[$v['targetType']."_".$v['targetId']]['userName'];
                $page['data'][$key]['totalRechargeMoney'] = $totalRechargeMoney;
                $page['data'][$key]['totalGiveMoney'] = $totalGiveMoney;
            }
        }
        return $page;
    }


    /**
     * 提现统计报表
     */
    public function cashDrawalMoneyByPage(){
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $where = [];
        $targetType = input('targetType',-1);
        $cashNo = input('cashNo');
        $cashSatus = input('cashSatus',-2);
        if(in_array($targetType,[0,1,2,3]))$where[] = ['targetType','=',$targetType];
        $where[] = ['cashSatus','=', 1];
    
        if($cashNo!='')$where[] = ['cashNo','like','%'.$cashNo.'%'];
        $isOpenSupplier = (int)WSTConf('CONF.isOpenSupplier');
        $page = Db::name("cash_draws")
                ->where($where)
                ->whereTime('createTime','between',[$start,$end])
                ->order('cashId desc')->paginate(input('limit/d'))->toArray();
        $totalMoney = Db::name("cash_draws")
                ->where($where)
                ->whereTime('createTime','between',[$start,$end])
                ->order('cashId desc')->sum("money");
        if(count($page['data'])>0){
            $userIds = [];
            $shopIds = [];
            $supplierIds = [];
            foreach ($page['data'] as $key => $v) {
                if($v['targetType']==0)$userIds[] = $v['targetId'];
                if($v['targetType']==1)$shopIds[] = $v['targetId'];
                if($isOpenSupplier ==1 && $v['targetType']==3)$supplierIds[] = $v['targetId'];
            }
            $userMap = [];
            if(count($userIds)>0){
                $user = Db::name('users')->where([['userId','in',$userIds]])->field('userId,loginName,userName')->select();
                foreach ($user as $key => $v) {
                    $userMap["0_".$v['userId']] = $v; 
                }
            }
            if(count($shopIds)>0){
                $user = Db::name('shops')->alias('s')
                          ->join('__USERS__ u','u.userId=s.userId')
                          ->where([['shopId','in',$shopIds]])
                          ->field('s.shopId,u.loginName,s.shopName as userName')
                          ->select();
                foreach ($user as $key => $v) {
                    $userMap["1_".$v['shopId']] = $v; 
                }
            }
            if($isOpenSupplier ==1 && count($supplierIds)>0){
                $user = Db::name('suppliers')->alias('s')
                          ->join('__USERS__ u','u.userId=s.userId')
                          ->where([['supplierId','in',$supplierIds]])
                          ->field('s.supplierId,u.loginName,s.supplierName as userName')
                          ->select();
                foreach ($user as $key => $v) {
                    $userMap["3_".$v['supplierId']] = $v; 
                }
            }
            foreach ($page['data'] as $key => $v) {
                if(!$isOpenSupplier && $v['targetType']==3){
                    continue;
                }
                $page['data'][$key]['targetTypeName'] = WSTGetTargetTypeName($v['targetType']);
                $page['data'][$key]['loginName'] = $userMap[$v['targetType']."_".$v['targetId']]['loginName'];
                $page['data'][$key]['userName'] = $userMap[$v['targetType']."_".$v['targetId']]['userName'];
                $page['data'][$key]['totalMoney'] = $totalMoney;
            }
        }
        return $page;
    }

    /**
     * 积分抵扣报表
     */
    public function scoreConsumeByPage(){
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $where = [];
        $where[] = ["us.dataSrc","=",1];
        $where[] = ["us.scoreType","=",0];
        $page =  Db::name("user_scores")->alias("us")
                ->join('orders o','us.dataId=o.orderId',"inner")
                ->join('users u','u.userId=us.userId',"inner")
                ->field('us.scoreId,us.userId,us.score,us.dataRemarks,us.createTime,o.scoreMoney,u.userName,u.loginName,u.userPhone')
                ->order('us.scoreId','desc')
                ->whereTime('us.createTime','between',[$start,$end])
                ->where($where)
                ->paginate(input('limit/d'))->toArray();
        $rs =  Db::name("user_scores")->alias("us")
                ->join('orders o','us.dataId=o.orderId')
                ->join('users u','u.userId=us.userId')
                ->order('us.scoreId','desc')
                ->whereTime('us.createTime','between',[$start,$end])
                ->where($where)
                ->field("sum(o.scoreMoney) totalScoreMoney,sum(us.score) totalScore")->find();
        $totalScoreMoney = (float)$rs["totalScoreMoney"];
        $totalScore = (int)$rs["totalScore"];
        foreach($page['data'] as $k=>$v){
            $page['data'][$k]['userName'] = ($v['userName']!="")?$v['userName']:$v['loginName'];
            $page['data'][$k]['totalScoreMoney'] = $totalScoreMoney;
            $page['data'][$k]['totalScore'] = $totalScore;
        }
        return $page;
    }



    /**********************************数据导出************************************/
    public function toExportShopSales(){
        $name='report';
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $prefix = config('database.prefix');
        $rs = Db::table($prefix.'shops')
                 ->alias([$prefix.'shops'=>'s',$prefix.'orders' => 'o'])
                 ->field('s.shopId,s.shopImg,s.shopName,sum(o.totalMoney) totalMoney,count(o.orderId) orderNum')
                 ->join($prefix.'orders o','s.shopId=o.shopId')
                 ->order('totalMoney desc,orderNum desc')
                 ->whereTime('o.createTime','between',[$start,$end])
                 ->where('(payType=0 or (payType=1 and o.isPay=1)) and o.dataFlag=1 and orderStatus=2')
                 ->group('o.shopId')
                 ->select();
        foreach($rs as $k=>$v){
            $onLineArr = Db::name('orders')
                 ->whereTime('createTime','between',[$start,$end])
                 ->field('sum(totalMoney) totalMoney,sum(realTotalMoney) realTotalMoney')
                 ->where('payType=1 and isPay=1 and dataFlag=1 and orderStatus=2')
                 ->where(['shopId'=>$v['shopId']])
                 ->find();
            $rs[$k]['onLinePayMoney'] = (float)$onLineArr['totalMoney'];// 在线支付总金额
            $rs[$k]['onLinePayTrueMoney'] = (float)$onLineArr['realTotalMoney'];// 在线支付实际金额
            $rs[$k]['offLinePayMoney'] = (float)
            Db::name('orders')
                 ->whereTime('createTime','between',[$start,$end])
                 ->where('payType=0 and dataFlag=1 and orderStatus=2')
                 ->where(['shopId'=>$v['shopId']])
                 ->value('sum(totalMoney)');;// 货到付款金额
        }
        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("店铺销售统计");//种类
        // 开始操作excel表
        $objPHPExcel->setActiveSheetIndex(0);
        // 设置工作薄名称
        $objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'Sheet'));
        // 设置默认字体和大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', ''));
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $styleArray = array(
                'font' => array(
                        'bold' => true,
                        'color'=>array(
                                'argb' => 'ffffffff',
                        )
                )
        );
        //设置宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:F1');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '店铺')->setCellValue('B1', '销售额')->setCellValue('C1', '在线支付总金额')->setCellValue('D1', '在线支付实际收入')->setCellValue('E1', '货到付款实际收入')
        ->setCellValue('F1', '订单数');
        $objPHPExcel->getActiveSheet()->getStyle('A1:Y1')->applyFromArray($styleArray);
        $i = 1;
        $totalRow = 0;
        for ($row = 0; $row < count($rs); $row++){
            $i = $row+2;
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $rs[$row]['shopName'])
            ->setCellValue('B'.$i, '￥'.$rs[$row]['totalMoney'])
            ->setCellValue('C'.$i, '￥'.$rs[$row]['onLinePayMoney'])
            ->setCellValue('D'.$i, '￥'.$rs[$row]['onLinePayTrueMoney'])
            ->setCellValue('E'.$i, '￥'.$rs[$row]['offLinePayMoney'])
            ->setCellValue('F'.$i, $rs[$row]['orderNum']);
            $totalRow++;
        }
        $totalRow = ($totalRow==0)?1:$totalRow+1;
        $objPHPExcel->getActiveSheet()->getStyle('A1:F'.$totalRow)->applyFromArray(array(
                'borders' => array (
                        'allborders' => array (
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
                                'color' => array ('argb' => 'FF000000'),     //设置border颜色
                        )
                )
        ));
        $this->PHPExcelWriter($objPHPExcel,$name);
    }
    public function toExportSaleGoods(){
        $name='report';
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $prefix = config('database.prefix');
        $rs = Db::table($prefix.'order_goods')
          ->alias([$prefix.'order_goods'=>'og',$prefix.'orders'=>'o',$prefix.'goods'=>'g',$prefix.'shops'=>'s'])
          ->field('og.goodsId,g.goodsName,goodsSn,s.shopId,shopName,sum(og.goodsNum) goodsNum,og.goodsImg')
          ->join($prefix.'orders','og.orderId=o.orderId')
          ->join($prefix.'goods','og.goodsId=g.goodsId')
          ->join($prefix.'shops','g.shopId=s.shopId')
          ->order('goodsNum','desc')
          ->whereTime('o.createTime','between',[$start,$end])
          ->where('(o.payType=0 or (o.payType=1 and o.isPay=1)) and o.dataFlag=1')->group('og.goodsId,g.goodsName,goodsSn,s.shopId,shopName,og.goodsImg')
          ->select();
        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("商品销售统计");//种类
        // 开始操作excel表
        $objPHPExcel->setActiveSheetIndex(0);
        // 设置工作薄名称
        $objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'Sheet'));
        // 设置默认字体和大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', ''));
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $styleArray = array(
                'font' => array(
                        'bold' => true,
                        'color'=>array(
                                'argb' => 'ffffffff',
                        )
                )
        );
        //设置宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:D1');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
        $objPHPExcel->getActiveSheet()
        ->setCellValue('A1', '商品名称')
        ->setCellValue('B1', '商品编号')
        ->setCellValue('C1', '所属店铺')
        ->setCellValue('D1', '销量');
        $objPHPExcel->getActiveSheet()->getStyle('A1:D1')->applyFromArray($styleArray);
        $i = 1;
        $totalRow = 0;
        for ($row = 0; $row < count($rs); $row++){
            $i = $row+2;
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $rs[$row]['goodsName'])
            ->setCellValue('B'.$i, " ".$rs[$row]['goodsSn'])
            ->setCellValue('C'.$i, $rs[$row]['shopName'])
            ->setCellValue('D'.$i, $rs[$row]['goodsNum']);
            $totalRow++;
        }
        $totalRow = ($totalRow==0)?1:$totalRow+1;
        $objPHPExcel->getActiveSheet()->getStyle('A1:D'.$totalRow)->applyFromArray(array(
                'borders' => array (
                        'allborders' => array (
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
                                'color' => array ('argb' => 'FF000000'),     //设置border颜色
                        )
                )
        ));
        $this->PHPExcelWriter($objPHPExcel,$name);
    }
    public function toExportStatSales(){
        $name='report';
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $payType = (int)input('payType',-1);
        $rs = Db::field('left(createTime,10) createTime,orderSrc,sum(totalMoney) totalMoney')->name('orders')->whereTime('createTime','between',[$start,$end])
                ->where('((payType=0 or (payType=1 and isPay=1)) and dataFlag=1) '.(in_array($payType,[0,1])?" and payType=".$payType:''))
                ->order('createTime asc')
                ->group('left(createTime,10),orderSrc')->select();
        $rdata = [];
        if(count($rs)>0){
            $days = [];
            $payTypes = [0,1,2,3,4,5];
            $tmp = [];
            foreach($rs as $key => $v){
                if(!in_array($v['createTime'],$days))$days[] = $v['createTime'];
                $tmp[$v['orderSrc']."_".$v['createTime']] = $v['totalMoney'];
            }
            $rdata['map'] = ['p0'=>0,'p1'=>0,'p2'=>0,'p3'=>0,'p4'=>0,'p5'=>0];
            foreach($days as $v){
                $total = 0;
                foreach($payTypes as $p){
                    $pv = isset($tmp[$p."_".$v])?$tmp[$p."_".$v]:0;
                    $rdata['p'.$p][] = (float)$pv;
                    $total = $total + (float)$pv;
                    $rdata['map']['p'.$p] = $rdata['map']['p'.$p] + (float)$pv;
                }
                $rdata['total'][] = $total;
            }
            $rdata['days'] = $days;
        }
        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("销售额统计");//种类
        // 开始操作excel表
        $objPHPExcel->setActiveSheetIndex(0);
        // 设置工作薄名称
        $objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'Sheet'));
        // 设置默认字体和大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', ''));
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $styleArray = array(
                'font' => array(
                        'bold' => true,
                        'color'=>array(
                                'argb' => 'ffffffff',
                        )
                )
        );
        //设置宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:H1');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
        $objPHPExcel->getActiveSheet()
        ->setCellValue('A1', '日期')
        ->setCellValue('B1', '电脑端（￥）')
        ->setCellValue('C1', '触屏端(￥)')
        ->setCellValue('D1', '微信端(￥)')
        ->setCellValue('E1', '小程序(￥)')
        ->setCellValue('F1', '安卓端(￥)')
        ->setCellValue('G1', '苹果端(￥)')
        ->setCellValue('H1', '总销售额(￥)');
        $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleArray);
        $i = 1;
        $totalRow = 0;
        for ($row = 0; $row < count($rdata['days']); $row++){
            $i = $row+2;
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $rdata['days'][$row])
            ->setCellValue('B'.$i, $rdata['p0'][$row])
            ->setCellValue('C'.$i, $rdata['p2'][$row])
            ->setCellValue('D'.$i, $rdata['p1'][$row])
            ->setCellValue('E'.$i, $rdata['p5'][$row])
            ->setCellValue('F'.$i, $rdata['p3'][$row])
            ->setCellValue('G'.$i, $rdata['p4'][$row])
            ->setCellValue('H'.$i, $rdata['total'][$row]);
            $totalRow++;
        }
        $totalRow = ($totalRow==0)?1:$totalRow+1;
        $objPHPExcel->getActiveSheet()->getStyle('A1:H'.$totalRow)->applyFromArray(array(
                'borders' => array (
                        'allborders' => array (
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
                                'color' => array ('argb' => 'FF000000'),     //设置border颜色
                        )
                )
        ));
        $this->PHPExcelWriter($objPHPExcel,$name);
    }
    public function toExportStatOrders(){
        $name='report';
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $payType = (int)input('payType',-1);
        $rs = Db::field('left(createTime,10) createTime,orderSrc,count(orderId) orderNum')->name('orders')->whereTime('createTime','between',[$start,$end])
                ->where('((payType=0 or (payType=1 and isPay=1)) and dataFlag=1) '.(in_array($payType,[0,1])?" and payType=".$payType:''))
                ->order('createTime asc')
                ->group('left(createTime,10),orderSrc')->select();
        $rdata = [];
        if(count($rs)>0){
            $days = [];
            $payTypes = [0,1,2,3,4,5];
            $tmp = [];
            foreach($rs as $key => $v){
                if(!in_array($v['createTime'],$days))$days[] = $v['createTime'];
                $tmp[$v['orderSrc']."_".$v['createTime']] = $v['orderNum'];
            }
            $rdata['map'] = ['p0'=>0,'p1'=>0,'p2'=>0,'p3'=>0,'p4'=>0,'p5'=>0];
            foreach($days as $v){
                $total = 0;
                foreach($payTypes as $p){
                    $pv = isset($tmp[$p."_".$v])?$tmp[$p."_".$v]:0;
                    $rdata['p'.$p][] = (float)$pv;
                    $total = $total + (float)$pv;
                    $rdata['map']['p'.$p] = $rdata['map']['p'.$p] + (float)$pv;
                }
                $rdata['total'][] = $total;
            }
            $rdata['days'] = $days;
        }
        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("销售额统计");//种类
        // 开始操作excel表
        $objPHPExcel->setActiveSheetIndex(0);
        // 设置工作薄名称
        $objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'Sheet'));
        // 设置默认字体和大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', ''));
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $styleArray = array(
                'font' => array(
                        'bold' => true,
                        'color'=>array(
                                'argb' => 'ffffffff',
                        )
                )
        );
        //设置宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(16);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A1:H1');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
        $objPHPExcel->getActiveSheet()
        ->setCellValue('A1', '日期')
        ->setCellValue('B1', '电脑端（单）')
        ->setCellValue('C1', '触屏端(单)')
        ->setCellValue('D1', '微信端(单)')
        ->setCellValue('E1', '小程序(单)')
        ->setCellValue('F1', '安卓端(单)')
        ->setCellValue('G1', '苹果端(单)')
        ->setCellValue('H1', '总订单(单)');
        $objPHPExcel->getActiveSheet()->getStyle('A1:H1')->applyFromArray($styleArray);
        $i = 1;
        $totalRow = 0;
        for ($row = 0; $row < count($rdata['days']); $row++){
            $i = $row+2;
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $rdata['days'][$row])
            ->setCellValue('B'.$i, $rdata['p0'][$row])
            ->setCellValue('C'.$i, $rdata['p2'][$row])
            ->setCellValue('D'.$i, $rdata['p1'][$row])
            ->setCellValue('E'.$i, $rdata['p5'][$row])
            ->setCellValue('F'.$i, $rdata['p3'][$row])
            ->setCellValue('G'.$i, $rdata['p4'][$row])
            ->setCellValue('H'.$i, $rdata['total'][$row]);
            $totalRow++;
        }
        $totalRow = ($totalRow==0)?1:$totalRow+1;
        $objPHPExcel->getActiveSheet()->getStyle('A1:H'.$totalRow)->applyFromArray(array(
                'borders' => array (
                        'allborders' => array (
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
                                'color' => array ('argb' => 'FF000000'),     //设置border颜色
                        )
                )
        ));
        $this->PHPExcelWriter($objPHPExcel,$name);
    }


    /**
     * 导出订单销售额统计excel
     */
    public function toExportOrdereSaleMoney(){
        $name='report';
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $payType = (int)input("payType",-1);
        $orderStatus = (int)input("orderStatus",-1);
        $where = [];
        if($orderStatus>=0){
            $where[] = ["orderStatus","=",$orderStatus]; 
        }else{
            $where[] = ["orderStatus",">=",0]; 
        }
        if($payType>=0){
            $where[] = ["payType","=",$payType]; 
        }
        $rs =  Db::name("orders")->alias("o")
                ->join('users u','u.userId=o.userId',"inner")
                ->field('o.orderId,o.orderNo,o.totalMoney,o.realTotalMoney,o.payType,o.scoreMoney,o.createTime,orderStatus,u.userName,u.loginName,u.userPhone')
                ->order('o.orderId','desc')
                ->whereTime('o.createTime','between',[$start,$end])
                ->where($where)
                ->select();
        $rs1 = Db::name("orders")->alias("o")
                ->join('users u','u.userId=o.userId',"inner")
                ->whereTime('o.createTime','between',[$start,$end])
                ->where($where)
                ->field("sum(realTotalMoney) totalRealTotalMoney,sum(scoreMoney) totalScoreMoney")
                ->find();
        $totalRealTotalMoney = $rs1["totalRealTotalMoney"]; 
        $totalScoreMoney = $rs1["totalScoreMoney"]; 
        foreach($rs as $k=>$v){
            $rs[$k]['userName'] = ($v['userName']!="")?$v['userName']:$v['loginName'];
            $rs[$k]['payType'] = WSTLangPayType($v['payType']);
            $rs[$k]['status'] = WSTLangOrderStatus($v['orderStatus']);
        }
                 
        
        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("订单销售额统计");//种类
        // 开始操作excel表
        $objPHPExcel->setActiveSheetIndex(0);
        // 设置工作薄名称
        $objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'Sheet'));
        // 设置默认字体和大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', ''));
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $styleArray = array(
                'font' => array(
                        'bold' => true,
                        'color'=>array(
                                'argb' => 'ffffffff',
                        )
                )
        );
        //设置宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(40);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A2:H2');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);



        $objPHPExcel->getActiveSheet()
        ->setCellValue('G1', '积分抵扣总金额:'.$totalScoreMoney."元")
        ->setCellValue('H1', '订单总销售金额:'.$totalRealTotalMoney."元")
        ->setCellValue('A2', '订单号')
        ->setCellValue('B2', '下单用户')
        ->setCellValue('C2', '支付方式')
        ->setCellValue('D2', '订单状态')
        ->setCellValue('E2', '订单总金额')
        ->setCellValue('F2', '积分抵扣金额')
        ->setCellValue('G2', '订单实付金额')
        ->setCellValue('H2', '下单时间');
        $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleArray);
        $i = 2;
        $totalRow = 0;
        for ($row = 0; $row < count($rs); $row++){
            $i = $row+3;
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $rs[$row]['orderNo'])
            ->setCellValue('B'.$i, $rs[$row]['userName'])
            ->setCellValue('C'.$i, $rs[$row]['payType'])
            ->setCellValue('D'.$i, $rs[$row]['status'])
            ->setCellValue('E'.$i, '￥'.$rs[$row]['totalMoney'])
            ->setCellValue('F'.$i, '￥'.$rs[$row]['scoreMoney'])
            ->setCellValue('G'.$i, '￥'.$rs[$row]['realTotalMoney'])
            ->setCellValue('H'.$i, $rs[$row]['createTime']);
            $totalRow++;
        }
        $totalRow = ($totalRow==0)?1:$totalRow+2;
        $objPHPExcel->getActiveSheet()->getStyle('A2:H'.$totalRow)->applyFromArray(array(
                'borders' => array (
                        'allborders' => array (
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
                                'color' => array ('argb' => 'FF000000'),     //设置border颜色
                        )
                )
        ));
        $this->PHPExcelWriter($objPHPExcel,$name);
    }

    /**
     * 导出充值统计报表excel
     */
    public function toExportRechargeMoney(){
        $name='report';
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $where = [];
        $targetType = input('targetType',-1);
        if(in_array($targetType,[0,1]))$where[] = ['targetType','=',$targetType];
        
        $where[] = ['dataSrc','=', 4];
        $where[] = ['moneyType','=', 1];
        $rs =  Db::name("log_moneys")
                ->order('id','desc')
                ->whereTime('createTime','between',[$start,$end])
                ->where($where)
                ->select();
        $rs1 =  Db::name("log_moneys")
                ->order('id','desc')
                ->whereTime('createTime','between',[$start,$end])
                ->where($where)
                ->field("sum(money) totalRechargeMoney,sum(giveMoney) totalGiveMoney")
                ->find();
        $totalRechargeMoney = (float)$rs1["totalRechargeMoney"];
        $totalGiveMoney = (float)$rs1["totalGiveMoney"];
        
        if(count($rs)>0){
            $userIds = [];
            $shopIds = [];
            foreach ($rs as $key => $v) {
                if($v['targetType']==0)$userIds[] = $v['targetId'];
                if($v['targetType']==1)$shopIds[] = $v['targetId'];
            }
            $userMap = [];
            if(count($userIds)>0){
                $user = Db::name('users')->where([['userId','in',$userIds]])->field('userId,loginName,userName')->select();
                foreach ($user as $key => $v) {
                    $userMap["0_".$v['userId']] = $v; 
                }
            }
            if(count($shopIds)>0){
                $user = Db::name('shops')->alias('s')
                          ->join('__USERS__ u','u.userId=s.userId')
                          ->where([['shopId','in',$shopIds]])
                          ->field('s.shopId,u.loginName,s.shopName as userName')
                          ->select();
                foreach ($user as $key => $v) {
                    $userMap["1_".$v['shopId']] = $v; 
                }
            }
            foreach ($rs as $key => $v) {
                $rs[$key]['loginName'] = $userMap[$v['targetType']."_".$v['targetId']]['loginName'];
                $rs[$key]['userName'] = $userMap[$v['targetType']."_".$v['targetId']]['userName'];
            }
        }
                 
        
        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("充值统计");//种类
        // 开始操作excel表
        $objPHPExcel->setActiveSheetIndex(0);
        // 设置工作薄名称
        $objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'Sheet'));
        // 设置默认字体和大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', ''));
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $styleArray = array(
                'font' => array(
                        'bold' => true,
                        'color'=>array(
                                'argb' => 'ffffffff',
                        )
                )
        );
        //设置宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(40);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A2:F2');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);



        $objPHPExcel->getActiveSheet()
        ->setCellValue('E1', '充值总金额:'.$totalRechargeMoney."元")
        ->setCellValue('F1', '赠送总金额:'.$totalGiveMoney."元")
        ->setCellValue('A2', '会员名称')
        ->setCellValue('B2', '会员类型')
        ->setCellValue('C2', '提现描述')
        ->setCellValue('D2', '充值金额')
        ->setCellValue('E2', '赠送金额')
        ->setCellValue('F2', '提现时间');
        $objPHPExcel->getActiveSheet()->getStyle('A2:F2')->applyFromArray($styleArray);
        $i = 2;
        $totalRow = 0;
        for ($row = 0; $row < count($rs); $row++){
            $i = $row+3;
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $rs[$row]['loginName'])
            ->setCellValue('B'.$i, $rs[$row]['targetType'])
            ->setCellValue('C'.$i, $rs[$row]['remark'])
            ->setCellValue('D'.$i, '￥'.$rs[$row]['money'])
            ->setCellValue('E'.$i, '￥'.$rs[$row]['giveMoney'])
            ->setCellValue('F'.$i, $rs[$row]['createTime']);
            $totalRow++;
        }
        $totalRow = ($totalRow==0)?1:$totalRow+2;
        $objPHPExcel->getActiveSheet()->getStyle('A2:F'.$totalRow)->applyFromArray(array(
                'borders' => array (
                        'allborders' => array (
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
                                'color' => array ('argb' => 'FF000000'),     //设置border颜色
                        )
                )
        ));
        $this->PHPExcelWriter($objPHPExcel,$name);
    }

    /**
     * 导出提现统计报表excel
     */
    public function toExportCashDrawalMoney(){
        $name='report';
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $where = [];
        $targetType = input('targetType',-1);
        $cashNo = input('cashNo');
        $cashSatus = input('cashSatus',-2);
        if(in_array($targetType,[0,1]))$where[] = ['targetType','=',$targetType];
        $where[] = ['cashSatus','=', 1];
    
        if($cashNo!='')$where[] = ['cashNo','like','%'.$cashNo.'%'];
        $isOpenSupplier = (int)WSTConf('CONF.isOpenSupplier');
        $rs = Db::name("cash_draws")
                ->where($where)
                ->whereTime('createTime','between',[$start,$end])
                ->order('cashId desc')
                ->select();
        $totalMoney = Db::name("cash_draws")
                ->where($where)
                ->whereTime('createTime','between',[$start,$end])
                ->order('cashId desc')->sum("money");
        
        if(count($rs)>0){
            $userIds = [];
            $shopIds = [];
            $supplierIds = [];
            foreach ($rs as $key => $v) {
                if($v['targetType']==0)$userIds[] = $v['targetId'];
                if($v['targetType']==1)$shopIds[] = $v['targetId'];
                if($isOpenSupplier ==1 && $v['targetType']==3)$supplierIds[] = $v['targetId'];
            }
            $userMap = [];
            if(count($userIds)>0){
                $user = Db::name('users')->where([['userId','in',$userIds]])->field('userId,loginName,userName')->select();
                foreach ($user as $key => $v) {
                    $userMap["0_".$v['userId']] = $v; 
                }
            }
            if(count($shopIds)>0){
                $user = Db::name('shops')->alias('s')
                          ->join('__USERS__ u','u.userId=s.userId')
                          ->where([['shopId','in',$shopIds]])
                          ->field('s.shopId,u.loginName,s.shopName as userName')
                          ->select();
                foreach ($user as $key => $v) {
                    $userMap["1_".$v['shopId']] = $v; 
                }
            }
            if($isOpenSupplier ==1 && count($supplierIds)>0){
                $user = Db::name('suppliers')->alias('s')
                          ->join('__USERS__ u','u.userId=s.userId')
                          ->where([['supplierId','in',$supplierIds]])
                          ->field('s.supplierId,u.loginName,s.supplierName as userName')
                          ->select();
                foreach ($user as $key => $v) {
                    $userMap["3_".$v['supplierId']] = $v; 
                }
            }
            foreach ($rs as $key => $v) {
                if(!$isOpenSupplier && $v['targetType']==3){
                    continue;
                }
                $rs[$key]['targetTypeName'] = WSTGetTargetTypeName($v['targetType']);
                $rs[$key]['loginName'] = $userMap[$v['targetType']."_".$v['targetId']]['loginName'];
                $rs[$key]['userName'] = $userMap[$v['targetType']."_".$v['targetId']]['userName'];
            }
        }
                 
        
        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("提现统计报表");//种类
        // 开始操作excel表
        $objPHPExcel->setActiveSheetIndex(0);
        // 设置工作薄名称
        $objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'Sheet'));
        // 设置默认字体和大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', ''));
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $styleArray = array(
                'font' => array(
                        'bold' => true,
                        'color'=>array(
                                'argb' => 'ffffffff',
                        )
                )
        );
        //设置宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(40);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A2:H2');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);



        $objPHPExcel->getActiveSheet()
        ->setCellValue('H1', '提现总金额:'.$totalMoney."元")
        ->setCellValue('A2', '提现单号')
        ->setCellValue('B2', '会员名称')
        ->setCellValue('C2', '会员类型')
        ->setCellValue('D2', '提现银行')
        ->setCellValue('E2', '银行卡号')
        ->setCellValue('F2', '持卡人')
        ->setCellValue('G2', '提现金额')
        ->setCellValue('H2', '提现时间');
        $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray($styleArray);
        $i = 2;
        $totalRow = 0;
        for ($row = 0; $row < count($rs); $row++){
            $i = $row+3;
            $userName = $rs[$row]['userName']."(".$rs[$row]['loginName'].")";
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $rs[$row]['cashNo'])
            ->setCellValue('B'.$i, $userName)
            ->setCellValue('C'.$i, $rs[$row]['targetTypeName'])
            ->setCellValue('D'.$i, $rs[$row]['accTargetName'])
            ->setCellValue('E'.$i, $rs[$row]['accNo'])
            ->setCellValue('F'.$i, $rs[$row]['accUser'])
            ->setCellValue('G'.$i, '￥'.$rs[$row]['money'])
            ->setCellValue('H'.$i, $rs[$row]['createTime']);
            $totalRow++;
        }
        $totalRow = ($totalRow==0)?1:$totalRow+2;
        $objPHPExcel->getActiveSheet()->getStyle('A2:H'.$totalRow)->applyFromArray(array(
                'borders' => array (
                        'allborders' => array (
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
                                'color' => array ('argb' => 'FF000000'),     //设置border颜色
                        )
                )
        ));
        $this->PHPExcelWriter($objPHPExcel,$name);
    }

    /**
     * 导出订单销售额统计excel
     */
    public function toExportScoreConsume(){
        $name='report';
        $start = date('Y-m-d 00:00:00',strtotime(input('startDate')));
        $end = date('Y-m-d 23:59:59',strtotime(input('endDate')));
        $where = [];
        $where[] = ["us.dataSrc","=",1];
        $where[] = ["us.scoreType","=",0];
        $rs =  Db::name("user_scores")->alias("us")
                ->join('orders o','us.dataId=o.orderId',"inner")
                ->join('users u','u.userId=us.userId',"inner")
                ->field('us.scoreId,us.userId,us.score,us.dataRemarks,us.createTime,o.scoreMoney,u.userName,u.loginName,u.userPhone')
                ->order('us.scoreId','desc')
                ->whereTime('us.createTime','between',[$start,$end])
                ->where($where)
                ->select();
        $rs1 =  Db::name("user_scores")->alias("us")
                ->join('orders o','us.dataId=o.orderId')
                ->join('users u','u.userId=us.userId')
                ->order('us.scoreId','desc')
                ->whereTime('us.createTime','between',[$start,$end])
                ->where($where)
                ->field("sum(o.scoreMoney) totalScoreMoney,sum(us.score) totalScore")->find();
        $totalScoreMoney = (float)$rs1["totalScoreMoney"];
        $totalScore = (int)$rs1["totalScore"];
        foreach($rs as $k=>$v){
            $rs[$k]['userName'] = ($v['userName']!="")?$v['userName']:$v['loginName'];
        } 
        
        require Env::get('root_path') . 'extend/phpexcel/PHPExcel/IOFactory.php';
        $objPHPExcel = new \PHPExcel();
        // 设置excel文档的属性
        $objPHPExcel->getProperties()->setCreator("WSTMart")//创建人
        ->setLastModifiedBy("WSTMart")//最后修改人
        ->setTitle($name)//标题
        ->setSubject($name)//题目
        ->setDescription($name)//描述
        ->setKeywords("订单销售额统计");//种类
        // 开始操作excel表
        $objPHPExcel->setActiveSheetIndex(0);
        // 设置工作薄名称
        $objPHPExcel->getActiveSheet()->setTitle(iconv('gbk', 'utf-8', 'Sheet'));
        // 设置默认字体和大小
        $objPHPExcel->getDefaultStyle()->getFont()->setName(iconv('gbk', 'utf-8', ''));
        $objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
        $styleArray = array(
                'font' => array(
                        'bold' => true,
                        'color'=>array(
                                'argb' => 'ffffffff',
                        )
                )
        );
        //设置宽
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(90);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(40);
        $objRow = $objPHPExcel->getActiveSheet()->getStyle('A2:E2');
        $objRow->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);
        $objRow->getFill()->getStartColor()->setRGB('666699');
        $objRow->getAlignment()->setVertical(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objRow->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getDefaultStyle()->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);   
        $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);



        $objPHPExcel->getActiveSheet()
        ->setCellValue('D1', '使用积分总数:'.$totalScoreMoney."元")
        ->setCellValue('E1', '积分总抵扣金额:'.$totalScore."元")
        ->setCellValue('A2', '用户')
        ->setCellValue('B2', '描述')
        ->setCellValue('C2', '积分数')
        ->setCellValue('D2', '抵扣金额')
        ->setCellValue('E2', '时间');
        $objPHPExcel->getActiveSheet()->getStyle('A2:E2')->applyFromArray($styleArray);
        $i = 2;
        $totalRow = 0;
        for ($row = 0; $row < count($rs); $row++){
            $i = $row+3;
            $objPHPExcel->getActiveSheet()
            ->setCellValue('A'.$i, $rs[$row]['userName'])
            ->setCellValue('B'.$i, $rs[$row]['dataRemarks'])
            ->setCellValue('C'.$i, $rs[$row]['score'])
            ->setCellValue('D'.$i, '￥'.$rs[$row]['scoreMoney'])
            ->setCellValue('E'.$i, $rs[$row]['createTime']);
            $totalRow++;
        }
        $totalRow = ($totalRow==0)?1:$totalRow+2;
        $objPHPExcel->getActiveSheet()->getStyle('A2:E'.$totalRow)->applyFromArray(array(
                'borders' => array (
                        'allborders' => array (
                                'style' => \PHPExcel_Style_Border::BORDER_THIN,  //设置border样式
                                'color' => array ('argb' => 'FF000000'),     //设置border颜色
                        )
                )
        ));
        $this->PHPExcelWriter($objPHPExcel,$name);
    }
}