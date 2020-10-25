<?php
namespace wstmart\store\model;
use wstmart\store\validate\Stores as VStores;
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
class Stores extends Base{
    protected $pk = 'storeId';
    /**
     * 角色列表
     */
    public function pageQuery(){
        $shopId = (int)session('WST_STORE.shopId');
        $loginName = input("loginName/s");
        $storeName = input("storeName/s");
        $areaIdPath = input('areaIdPath');
        $where = [
            ["s.shopId",'=',$shopId],
            ["s.dataFlag",'=',1]
        ];
        if($loginName != ""){
            $where[] = ["u.loginName","like","%".$loginName."%"];
        }
        if($storeName != ""){
            $where[] = ["storeName","like","%".$storeName."%"];
        }
        if($areaIdPath !='')$where[] = ['s.areaIdPath','like',$areaIdPath."%"];
        $page = $this->alias('s')
                ->join("users u", "u.userId=s.userId and u.dataFlag=1")
                ->field('s.storeId,s.shopId,s.storeName,s.storeImg,s.areaId,s.storeTel,s.storeAddress,
                        s.storeStatus,u.userName,u.loginName,u.createTime')
                ->order('s.storeId desc')
                ->where($where)
                ->paginate(input('limit/d'))->toArray();
        $m = model("common/Areas");
        foreach ($page['data'] as $key =>$v){
            $areaNames = $m->getParentNames($v['areaId']);
            
            $page['data'][$key]['areaNames'] = implode('',$areaNames);
        }
        return $page;
    }

    /**
    *  根据id获取店铺用户
    */
    public function getById(){
        $storeId = (int)input('storeId');
        $shopId = (int)session('WST_STORE.shopId');
        $user = $this->alias('s')
                ->join("store_users su","s.storeId=su.storeId")
                ->join("users u", "u.userId=s.userId and u.dataFlag=1")
                ->field('s.*,u.userName,u.loginName')
                ->where([["s.storeId",'=',$storeId],["s.shopId",'=',$shopId],["s.dataFlag",'=',1]])
                ->find();
        return $user;
    }

    
    /**
     * 新增店铺用户
     */
    public function add(){
        $shopId = (int)session('WST_STORE.shopId');
        $data = array();
        $data['loginName'] = input("post.loginName");
        $data['loginPwd'] = input("post.loginPwd");
        $data['reUserPwd'] = input("post.reUserPwd");
        $loginName = $data['loginName'];
        
        //检测账号是否存在
        $crs = WSTCheckLoginKey($loginName);
        if($crs['status']!=1)return $crs;
        $decrypt_data = WSTRSA($data['loginPwd']);
        $decrypt_data2 = WSTRSA($data['reUserPwd']);
        if($decrypt_data['status']==1 && $decrypt_data2['status']==1){
            $data['loginPwd'] = $decrypt_data['data'];
            $data['reUserPwd'] = $decrypt_data2['data'];
        }else{
            return WSTReturn('新增失败');
        }
        if($data['loginPwd']!=$data['reUserPwd']){
            return WSTReturn("两次输入密码不一致!");
        }
        foreach ($data as $v){
            if($v ==''){
                return WSTReturn("信息不完整!");
            }
        }
        if($loginName=='')return WSTReturn("新增失败!");//分派不了登录名
    
        unset($data['reUserPwd']);
        //检测账号，邮箱，手机是否存在
        $data["loginSecret"] = rand(1000,9999);
        $data['loginPwd'] = md5($data['loginPwd'].$data['loginSecret']);
        $data['userName'] = input("post.userName");
        $data['userQQ'] = "";
        $data['userScore'] = 0;
        $data['createTime'] = date('Y-m-d H:i:s');
        $data['dataFlag'] = 1;
        $data['userType'] = 2;//门店用户
        Db::startTrans();
        try{
            $userId = Db::name("users")->insertGetId($data);
            if(false !== $userId){
                //添加门店用户
                $validate = new VStores();
                $data = input('post.');
                WSTUnset($data,'storeId,shopId,dataFlag');

                //保存店铺基础信息
                $areaIds = model('common/Areas')->getParentIs($data['areaId']);
                if(!empty($areaIds))$data['areaIdPath'] = implode('_',$areaIds)."_";

                $data["userId"] = $userId;
                $data["shopId"] = $shopId;
                $data['createTime'] = date('Y-m-d H:i:s');
                if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
                $result = $this->allowField(true)->save($data);

                $data = array();
                $data["shopId"] = $shopId;
                $data["userId"] = $userId;
                $data["roleId"] = 0;
                $data["storeId"] = $this->storeId;
                Db::name('store_users')->insert($data);
                $user = model("common/Users")->get($userId);
                //注册成功后执行钩子
                hook('afterUserRegist',['user'=>$user]);
                //发送消息
                $tpl = WSTMsgTemplates('USER_REGISTER');
                if( $tpl['tplContent']!='' && $tpl['status']=='1'){
                    $find = ['${LOGIN_NAME}','${MALL_NAME}'];
                    $replace = [$user['loginName'],WSTConf('CONF.mallName')];
                    WSTSendMsg($userId,str_replace($find,$replace,$tpl['tplContent']),['from'=>0,'dataId'=>0]);
                }

                Db::commit();
                return WSTReturn("新增成功",1);
            }
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn("新增失败!");
    }

    /**
     * 修改店铺用户
     */
    public function edit(){

        $shopId = (int)session('WST_STORE.shopId');
        Db::startTrans();
        try{
            $data = array();
            $storeId = (int)input("post.storeId");
            $id = (int)input("post.id");
            $loginPwd = input("post.loginPwd/s");
            $reUserPwd = input("post.reUserPwd/s");
            if($loginPwd!=""){
                if($loginPwd!=$reUserPwd){
                    return WSTReturn("两次输入密码不一致!");
                }
                $decrypt_data = WSTRSA($loginPwd);
                if($decrypt_data['status']==1){
                    $loginPwd = $decrypt_data['data'];
                }else{
                    return WSTReturn('修改失败');
                }
                if(!$loginPwd){
                    return WSTReturn('密码不能为空',-1);
                }
                $user = $this->where(["storeId"=>$storeId,"shopId"=>$shopId])->find();
                $userId = $user["userId"];
                $rs = model("users")->where(["userId"=>$userId])->find();
                //核对密码
          
                $oldPass = input("post.oldPass");
                $decrypt_data2 = WSTRSA($oldPass);
                if($decrypt_data2['status']==1){
                    $oldPass = $decrypt_data2['data'];
                }else{
                    return WSTReturn('修改失败');
                }
                
                $data["loginPwd"] = md5($loginPwd.$rs['loginSecret']);
                $rs = model("users")->update($data,['userId'=>$userId]);
                if(false !== $rs){
                    $validate = new VStores();
                    $data = input('post.');
                    WSTUnset($data,'storeId,shopId,userId,dataFlag');
                    if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
                    $result = $this->allowField(true)->save($data,['storeId'=>$storeId,'shopId'=>$shopId]);
                    
                    hook("afterEditPass",["userId"=>$userId]);
                }else{
                    return WSTReturn("修改失败", -1);
                }
                Db::commit();
                return WSTReturn("修改成功", 1);
                   
                
            }else{
                $validate = new VStores();
                $data = input('post.');
                WSTUnset($data,'storeId,shopId,userId,dataFlag');
                if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
                $result = $this->allowField(true)->save($data,['storeId'=>$storeId,'shopId'=>$shopId]);
                Db::commit();
                return WSTReturn("修改成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
        }
    }

    

    /**
     * 删除门店
     */
    public function del(){
        $shopId = (int)session('WST_STORE.shopId');
        $storeId = (int)input('storeId');
        $data = [];
        $data['dataFlag'] = -1;
        Db::startTrans();
        try{
            $result = $this->where([["storeId",'=',$storeId],["shopId",'=',$shopId]])->update($data);
            if(false !== $result){
                Db::name("store_users")->where([["shopId",'=',$shopId],["storeId",'=',$storeId]])->update(['dataFlag'=>-1]);
                Db::commit();
                return WSTReturn("删除成功", 1);
            }
        }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1);
    }

    /**
     * 修改门店状态
     */
    public function setStoreStatus(){
        $shopId = (int)session('WST_STORE.shopId');
        $storeId = (int)input('post.storeId');
        $storeStatus = (int)input('post.storeStatus');
        $data = [];
        $data['storeStatus'] = $storeStatus;
        $result = $this->where([["storeId",'=',$storeId],["shopId",'=',$shopId]])->update($data);
        if(false !== $result){
            return WSTReturn("设置成功", 1);
        }
        return WSTReturn('设置失败',-1);
    }

    /**
     * 门店销售统计
     */
    public function pageQuerySalestatistics(){
        $shopId = (int)session('WST_STORE.shopId');
        $startDate = input('post.startDate');
        $endDate = input('post.endDate');
        $orderStatus = input('post.orderStatus');
        $payType = input('post.payType');
        $storeName = input('post.storeName');
        // 未退款订单
        $refund = (int)input('post.refund');

        $where = ['o.shopId'=>$shopId,'o.dataFlag'=>1,'o.deliverType'=>1];
        $condition = [];
        if($orderStatus!=''){
            $condition[] = ['orderStatus','in',$orderStatus];
        }
        
        if($storeName!=''){
            $condition[] = ['storeName','like',"%$storeName%"];
        }
        if($payType!=''){
            $condition[] = ['payType','in',$payType];
        }
        

        $page = Db::name("orders o")->where($where)->where($condition)
              ->join("stores st","o.storeId=st.storeId")
              ->join('__ORDER_REFUNDS__ orf','orf.orderId=o.orderId and refundStatus=0','left')
              ->field('st.storeName,o.orderRemarks,o.noticeDeliver,o.orderId,orderNo,goodsMoney,totalMoney,realTotalMoney,orderStatus,deliverType,deliverMoney,isAppraise,isRefund
                      ,payType,payFrom,userAddress,orderStatus,isPay,isAppraise,userName,orderSrc,o.createTime,orf.id refundId,o.orderCode')
              ->order('o.createTime', 'desc')
              ->paginate()->toArray();
        $page['totalMoney'] = 0;
        if(count($page['data'])>0){
            $totalMoney = Db::name("orders o")->where($where)->where($condition)
                          ->join("stores st","o.storeId=st.storeId")
                          ->join('__ORDER_REFUNDS__ orf','orf.orderId=o.orderId and refundStatus=0','left')
                          ->sum("o.totalMoney");
            $page['totalMoney'] = $totalMoney;
             $orderIds = [];
             foreach ($page['data'] as $v){
                 $orderIds[] = $v['orderId'];
             }
             $goods = Db::name('order_goods')->where([['orderId','in',$orderIds]])->select();
             $goodsMap = [];
             foreach ($goods as $v){
                $v['goodsName'] = WSTStripTags($v['goodsName']);
                $v['goodsSpecNames'] = str_replace('@@_@@','、',$v['goodsSpecNames']);
                $goodsMap[$v['orderId']][] = $v;
             }
             foreach ($page['data'] as $key => $v){
                 $page['data'][$key]['orderCodeTitle'] = WSTOrderModule($v['orderCode']);
                 $page['data'][$key]['list'] = $goodsMap[$v['orderId']];
                 $page['data'][$key]['payTypeName'] = WSTLangPayType($v['payType']);
                 $page['data'][$key]['deliverTypeName'] = WSTLangDeliverType($v['deliverType']==1);
                 $page['data'][$key]['deliverType'] = $v['deliverType'];
                 $page['data'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
             }
        }
        return $page;
    }
}
