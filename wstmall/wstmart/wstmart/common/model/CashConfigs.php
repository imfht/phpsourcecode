<?php
namespace wstmart\common\model;
use wstmart\common\validate\CashConfigs as Validate;
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
 * 提现账号业务处理器
 */
class CashConfigs extends Base{
     /**
      * 获取列表
      */
      public function pageQuery($targetType,$targetId){
      	  $type = (int)input('post.type',-1);
          $where = [];
          $where['targetType'] = (int)$targetType;
          $where['targetId'] = (int)$targetId;
          $where['c.dataFlag'] = 1;
          if(in_array($type,[0,1]))$where['moneyType'] = $type;
          $page = $this->alias('c')->join('__BANKS__ b','c.accTargetId=b.bankId')->where($where)->field('b.bankName,b.bankImg,c.*')->order('c.id desc')->paginate()->toArray();
          if(count($page['data'])>0){
              foreach($page['data'] as $key => $v){
                  $areas = model('areas')->getParentNames($v['accAreaId']);
                  $page['data'][$key]['areaName'] = implode('',$areas);
                  $page['data'][$key]['accNo'] = '**** '.substr($v['accNo'],-4);
              }
          }
          return $page;
      }
      /**
       * 获取列表
       */
      public function listQuery($targetType,$targetId){
          $where = [];
          $where['a.targetType'] = (int)$targetType;
          $where['a.targetId'] = (int)$targetId;
          $where['a.dataFlag'] = 1;
          $list = $this->alias('a')->join('__BANKS__ b','a.accTargetId=b.bankId')->where($where)->field('a.id,a.accNo,a.accUser,b.bankName')->select();
          if(count($list)>0){
              foreach ($list as $key => $v) {
                   $list[$key]['accNo'] = '**** '.substr($v['accNo'],-4);
              }
          }
          return $list;
      }
      /**
       * 获取资料
       */
      public function getById($id, $uId=0){
          $userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
          $config = $this->where([['id','=',(int)$id],['dataFlag','=',1],['targetId','=',$userId]])->find();
          $areas = model('areas')->getParentIs($config['accAreaId']);
          $config['accAreaIdPath'] = implode('_',$areas)."_";
          return $config;
      }
      /**
       * 新增卡号
       */
      public function add($uId=0){
          $data = input('post.');
          $data['targetId'] = ($uId==0)?(int)session('WST_USER.userId'):$uId;
          $accNo = $this->where([ ['targetId','=', $data['targetId']], ['accNo','=',$data['accNo']], ['dataFlag','=',1] ])->count();
          if ($accNo>0) return WSTReturn("该提现账号已存在", -1);
          unset($data['id']);
          $data['targetType'] = 0;
          $data['accType'] = 3; 
          $data['createTime'] = date('Y-m-d H:i:s');
          WSTUnset($data,'id');
          $validate = new Validate;
          if (!$validate->scene('add')->check($data)) {
          	  return WSTReturn($validate->getError());
          }else{
          	  $result = $this->allowField(true)->save($data);
          }
          if(false !== $result){
              return WSTReturn("新增成功", 1,['id'=>$this->id]);
          }else{
              return WSTReturn($this->getError(),-1);
          }
      }
      /**
       * 编辑卡号
       */
      public function edit($uId=0){
          $id = (int)input('id');
          $userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
          $data = input('post.');
          unset($data['id']);
          $accNo = $this->where([ ['targetId','=', $userId], ['accNo','=',$data['accNo']], ['dataFlag','=',1], ['id','<>',$id] ])->count();
          if ($accNo>0) return WSTReturn("该提现账号已存在", -1);
          WSTUnset($data,'id,targetType,dataFlag,targetId,accType,createTime');
          $validate = new Validate;
          if (!$validate->scene('edit')->check($data)) {
          	return WSTReturn($validate->getError());
          }else{
          	$result = $this->allowField(true)->save($data,['id'=>$id,'targetId'=>$userId]);
          }
          if(false !== $result){
              return WSTReturn("编辑成功", 1);
          }else{
              return WSTReturn($this->getError(),-1);
          }
      }
      /**
       *  删除提现账号
       */
      public function del($uId=0){
        $userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
         $object = $this->get(['id'=>(int)input('id'),'targetId'=>$userId]);
         if($object==null)return WSTReturn('操作失败',-1);
         $object->dataFlag = -1;
         $result = $object->save();
         if(false !== $result){
            return WSTReturn('操作成功',1);
         }
         return WSTReturn('操作失败',-1);
      }
}
