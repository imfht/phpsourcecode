<?php
namespace wstmart\home\model;
use wstmart\common\model\Suppliers as CSuppliers;
use wstmart\home\validate\Suppliers as VSupplier;
use wstmart\home\validate\SupplierBase as VSupplierBase;
use think\Db;
use think\Loader;
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
 * 门店类
 */
class Suppliers extends CSuppliers{
    
    
    /**
     * 获取店铺指定字段
     */
    public function getFieldsById($supplierId,$fields){
        return $this->where(['supplierId'=>$supplierId,'dataFlag'=>1])->field($fields)->find();
    }

    /**
     * 保存入驻资料
     */
    public function saveStep($data = []){
        $userId = (int)session('WST_USER.userId');
        $flowId = (int)input('flowId');
        //判断是否存在入驻申请
        $suppliers = $this->alias('s')->join('__SUPPLIER_USERS__ sur','s.supplierId=sur.supplierId','left')->field('s.*')->where(['sur.userId'=>$userId])->find();
        if(!empty($suppliers))return WSTReturn('请勿重复申请入驻');
        $suppliers = $this->where('userId',$userId)->find();
        $supplierId = 0;
        if(empty($suppliers)){
            $supplier = ['userId'=>$userId,'applyStatus'=>0];
            $this->save($supplier);
            $exData['supplierId'] = $this->supplierId;
            Db::name('supplier_extras')->insert($exData);
            $supplierId = $this->supplierId;
        }else{
            $supplierId = $suppliers['supplierId'];
        }
        if($suppliers['applyStatus']==1)return WSTReturn('您的入驻申请正在审核，请勿重复提交');
        if($suppliers['applyStatus']==2)return WSTReturn('请勿重复申请入驻');

        // 保存流程id
        $applyStep = ['applyStep'=>$flowId];
        $this->save($applyStep,['supplierId'=>$supplierId]);
        //获取完整流程信息
        $supplierFlows = $this->getSupplierFlowDatas($flowId);

        //新增入驻申请
        // 先遍历前台传来的data,根据supplier_base表判断是属于suppliers表还是supplier_extras表，分别用两个数组保存
        $suppliersData = [];
        $supplierExtrasData = [];
        // 保存上传图片的路径，用来启用上传图片
        $uploadSuppliersImgPath = [];
        $uploadSupplierExtrasImgPath = [];
        $unsetField = [];
        $goodsCats = [];
        foreach($data as $k => $v){
            $field = Db::name('supplier_bases')->where(['fieldName'=>$k,'dataFlag'=>1])->field('fieldName,fieldType,fieldAttr,isSuppliersTable,dateRelevance,isShow,isRequire')->find();
            if($field['isSuppliersTable']==1){
                // 属于suppliers表
                $suppliersData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaId = $suppliersData[$k];
                    $areaIds = model('Areas')->getParentIs($suppliersData[$k]);
                    if(!empty($areaIds))$suppliersData[$k] = implode('_',$areaIds)."_";
                    if($field['fieldName'] == 'areaIdPath')$suppliersData['areaId'] = $areaId;
                    if($field['fieldName'] == 'bankAreaIdPath')$suppliersData['bankAreaId'] = $areaId;
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadSuppliersImgPath[] = $data[$k];
                }
            }else{
                // 属于supplier_extras表
                $supplierExtrasData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaIds = model('Areas')->getParentIs($supplierExtrasData[$k]);
                    if(!empty($areaIds))$supplierExtrasData[$k] = implode('_',$areaIds)."_";
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadSupplierExtrasImgPath[] = $data[$k];
                }
                // 日期字段入库前处理
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'date'){
                    // 当日期字段不是必填项，需删除该字段
                    if($field['isRequire'] == 0){
                        $unsetField[] = $field['fieldName'];
                    }
                    if($field['dateRelevance']){
                        $dateRelevance = explode(',',$field['dateRelevance']);
                        // 如果选择了长期，就删除字段的结束日期
                        if($data[$dateRelevance[1]]==1){
                            $unsetField[] = $dateRelevance[0];
                        }
                    }
                }
                //经营范围
                if(!empty($data['goodsCatIds']))$goodsCats = explode(',',$data['goodsCatIds']);
            }
        }
        // 删除无需入库的字段
        foreach($supplierExtrasData as $k => $v){
            if(in_array($k,$unsetField)){
                unset($supplierExtrasData[$k]);
            }
        }

        $validate = new VSupplierBase();
        $validate->setRuleAndMessage($suppliersData);
        $validate->setRuleAndMessage($supplierExtrasData);

        Db::startTrans();
        try{
            $suppliersData['supplierId'] = $supplierId;
            //$suppliersData['applyStatus'] = 1;
            $supplierExtrasData['supplierId'] = $supplierId;
            if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
            //判断是不是最后一个表单环节了
            $flows = $supplierFlows['flows'];
            if($flows[count($flows)-1]['flowId']==$supplierFlows['nextStep']['flowId']){
                $suppliersData['createTime'] = date('Y-m-d');
                $suppliersData['expireDate'] = date('Y-m-d');
                $suppliersData['applyTime'] = date('Y-m-d H:i:s');
                $tmpPkey = session('tmpPkey');
                if($tmpPkey==''){
                    // 选择不用交钱的类目，直接更改申请状态为待审核
                    $suppliersData['applyStatus'] = 1;
                }else{
                    // 选择要交钱的类目，要等支付成功后才会将申请状态改为待审核
                    $suppliersData['applyStatus'] = 0;
                }
            }
            $this->allowField(true)->save($suppliersData,['supplierId'=>$supplierId]);
            foreach($uploadSuppliersImgPath as $v){
                //启用上传图片
                WSTUseResource(0, $this->supplierId, $v ,'suppliers');
            }
            $seModel = model('SupplierExtras');
            $seModel->allowField(true)->save($supplierExtrasData,['supplierId'=>$supplierId]);
            $extraId = $seModel->where(['supplierId'=>$supplierId])->value('id');// 获取主键
            foreach($uploadSupplierExtrasImgPath as $v){
                //启用上传图片
                WSTUseResource(0, $extraId, $v ,'supplierextras');
            }
            if($goodsCats){
                Db::name('cat_suppliers')->where('supplierId','=',$supplierId)->delete();
                foreach ($goodsCats as $v){
                    if((int)$v>0)Db::name('cat_suppliers')->insert(['supplierId'=>$supplierId,'catId'=>$v]);
                }
            }
            Db::commit();
            session('tmpApplyStep',$supplierFlows['nextStep']['flowId']);
            return WSTReturn('保存成功', 1, ['nextflowId'=>$supplierFlows['nextStep']['flowId']]);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('保存失败',-1);
        }
    }

    /**
     * 获取商家入驻资料
     */
    public function getSupplierApply(){
        $userId = (int)session('WST_USER.userId');
        $rs = $this->alias('s')->join('__SUPPLIER_EXTRAS__ ss','s.supplierId=ss.supplierId','inner')
                   ->where('s.userId',$userId)
                   ->find();
        if(!empty($rs)){
            $rs = $rs->toArray();
            $goodscats = Db::name('cat_suppliers')->where('supplierId',$rs['supplierId'])->select();
            $rs['catsuppliers'] = [];
            foreach ($goodscats as $v){
                $rs['catsuppliers'][$v['catId']] = true;
            }
        }else{
            $rs = [];
            $data1 = $this->getEModel('suppliers');
            $data2 = $this->getEModel('supplier_extras');
            $rs = array_merge($data1,$data2);
        }
        return $rs;
    }

    /**
     * 判断是否申请入驻过
     */
    public function checkApply(){
        $userId = (int)session('WST_USER.userId');
        $rs = $this->alias('s')->join('__SUPPLIER_USERS__ sur','s.supplierId=sur.supplierId','left')->field('s.*')->where(['sur.userId'=>$userId])->find();
        if(empty($rs)){
            $rs = $this->where('userId',$userId)->find();
        }
        if(!empty($rs)){
            $WST_USER = session('WST_USER');
            $WST_USER['tempSupplierId'] = $rs->supplierId;
            session('WST_USER',$WST_USER);
            session('tmpApplyStep',$rs['applyStep']);
        }else{
            session('tmpApplyStep',0);
        }
        return $rs;
    }
    /**
    * 首页店铺街列表
    */
    public function indexSupplierQuery($num=4){
        $cacheData = cache('PC_SUPPLIER_STREET');
        if(!$cacheData){
            $cacheData = $this->alias('s')
                ->join('__SUPPLIER_CONFIGS__ sc','s.supplierId=sc.supplierId','inner')
                ->join('__RECOMMENDS__ r','s.supplierId=r.dataId')
                ->where(['r.goodsCatId'=>0,'s.supplierStatus'=>1,'s.dataFlag'=>1,'r.dataSrc'=>1,'r.dataType'=>0])
                ->field('s.supplierId,s.supplierName,s.supplierAddress,sc.supplierStreetImg')->order('r.dataSort asc')->select();
            cache('PC_SUPPLIER_STREET',$cacheData,86400);
        }
        return $cacheData;
    }

    /*
     * 获取入驻流程
     */
    public function getSupplierFlows(){
        return Db::name('supplier_flows')->where(['isShow'=>1,'dataFlag'=>1])->order('sort asc')->select();
    }

    /*
     * 获取单个入驻流程
     */
    public function getSupplierFlowById($id){
        return Db::name('supplier_flows')->where(['flowId'=>$id,'isShow'=>1,'dataFlag'=>1])->find();
    }

    /*
     * 获取单个入驻流程里的字段信息
     */
    public function getFlowFieldsById($id){
        return Db::name('supplier_bases')->where(['flowId'=>$id,'dataFlag'=>1])->order('fieldSort asc,id asc')->select();
    }
    /**
     * 获取商家入驻流程
     */
    public function getSupplierFlowDatas($flowId = 0){
        $data = ['flows'=>[],'prevStep'=>[],'nextStep'=>[]];
        $data['flows'] = Db::name('supplier_flows')->where(['isShow'=>1,'dataFlag'=>1])->order('sort asc')->select();
        $flowNum = count($data['flows']);
        $flowId = ($flowId==0)?$data['flows'][0]['flowId']:$flowId;
        foreach ($data['flows'] as $key => $v) {
            if($key>0){
               $data['prevStep'] =  $data['flows'][$key-1];
            }
            if($v['flowId'] == $flowId){
                $data['currStep'] = $v;
                if(($flowNum-1)>$key){
                    $data['nextStep'] = $data['flows'][$key+1];
                }
                break;
            }
        }
        return $data;
    }

    /**
     * 获取店铺信息
     */
    public function getTradeFee($userId){
        $rs = Db::name("trades t")->join("suppliers s","s.tradeId=t.tradeId","inner")
            ->field("t.tradeFee")
            ->where(['s.userId'=>$userId])
            ->find();
        return $rs;
    }
}
