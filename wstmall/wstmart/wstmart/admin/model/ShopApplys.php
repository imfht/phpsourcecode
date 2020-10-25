<?php
namespace wstmart\admin\model;
use wstmart\admin\validate\ShopApplys as validate;
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
 * 商家入驻业务处理
 */
class ShopApplys extends Base{
	
    /**
	 * 编辑
	 */
	public function edit(){
		$data = input('post.');
		Db::startTrans();
		try{
			if($data['applyStatus']==1)$data['handleReamrk'] = '';
			if($data['applyStatus']==-1)$data['shopName'] = '';
			$validate = new validate();
		    if(!$validate->scene('edit')->check($data))return WSTReturn($validate->getError());
		    $result = $this->allowField('applyStatus,handleReamrk,shopName')->save($data,['id'=>(int)$data['id'],'applyStatus'=>0,'dataFlag'=>1]);
	        if(false !== $result){
	        	$this->sendMessages((int)$data['id']);
	        	Db::commit();
	        	return WSTReturn("操作成功", 1);
	        }
	    }catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('操作失败',-1);  
	}

	/**
	 * 发送信息
	 */
	public function sendMessages($id){
		$data = $this->alias('a')->join('__USERS__ u','a.userId=u.userId','inner')->where([["a.id","=",$id],["a.dataFlag","=",1]])->field('u.loginName,u.userEmail,a.*')->find();
	    if((int)$data['applyStatus']==1){
            //如果存在手机则发送手机号码提示
		    $tpl = WSTMsgTemplates('PHONE_USER_SHOP_OPEN_SUCCESS');
		    if( $tpl['tplContent']!='' && $tpl['status']=='1' && $data['linkPhone']!=''){
		        $params = ['tpl'=>$tpl,'params'=>['MALL_NAME'=>WSTConf("CONF.mallName"),'LOGIN_NAME'=>$data['loginName']]];
		        $rv = model('admin/LogSms')->sendSMS(0,$data['userId'],$data['linkPhone'],$params,'shopapplys');
		    }
		    //发送邮件
		    $tpl = WSTMsgTemplates('EMAIL_USER_SHOP_OPEN_SUCCESS');
		    if( $tpl['tplContent']!='' && $tpl['status']=='1' && $data['userEmail']){
		        $find = ['${LOGIN_NAME}','${MALL_NAME}'];
		        $replace = [$data['loginName'],WSTConf("CONF.mallName")];
		        $sendRs = WSTSendMail($data['userEmail'],'申请入驻审核通过',str_replace($find,$replace,$tpl['content']));
		    }
		    // 会员发送一条商城消息
	        $tpl = WSTMsgTemplates('SHOP_OPEN_SUCCESS');
		    if( $tpl['tplContent']!='' && $tpl['status']=='1'){
		        $find = ['${LOGIN_NAME}','${MALL_NAME}'];
		        $replace = [$data['loginName'],WSTConf("CONF.mallName")];
		        WSTSendMsg($data['userId'],str_replace($find,$replace,$tpl['tplContent']),['from'=>0,'dataId'=>0]);
		    }
		    //微信消息
		    if((int)WSTConf('CONF.wxenabled')==1){
			    $params = [];
			    $params['SHOP_NAME'] = $data['shopName'];
				$params['APPLY_TIME'] = $data['createTime'];
				$params['NOW_TIME'] = date('Y-m-d H:i:s');
				$params['REASON'] = "申请入驻成功";
				WSTWxMessage(['CODE'=>'WX_SHOP_OPEN_SUCCESS','userId'=>$data['userId'],'params'=>$params]);
			} 
	    }else{   	
	        //如果存在手机则发送手机号码提示
		    $tpl = WSTMsgTemplates('PHONE_SHOP_OPEN_FAIL');
		    if( $tpl['tplContent']!='' && $tpl['status']=='1' && $data['linkPhone']!=''){
		        $params = ['tpl'=>$tpl,'params'=>['MALL_NAME'=>WSTConf("CONF.mallName"),'REASON'=>$data['handleReamrk']]];
		        $rv = model('admin/LogSms')->sendSMS(0,$data['userId'],$data['linkPhone'],$params,'shopapplys');
		    }
		    //发送邮件
		    $tpl = WSTMsgTemplates('EMAIL_SHOP_OPEN_FAIL');
		    if( $tpl['tplContent']!='' && $tpl['status']=='1' && $data['userEmail']){
		        $find = ['${LOGIN_NAME}','${MALL_NAME}','${REASON}'];
		        $replace = [$data['loginName'],WSTConf("CONF.mallName"),$data['handleReamrk']];
		        $sendRs = WSTSendMail($data['userEmail'],'申请入驻失败',str_replace($find,$replace,$tpl['content']));
		    }
	    	// 会员发送一条商城消息
	    	$tpl = WSTMsgTemplates('SHOP_OPEN_FAIL');
		    if( $tpl['tplContent']!='' && $tpl['status']=='1'){
		        $find = ['${LOGIN_NAME}','${MALL_NAME}','${REASON}'];
		        $replace = [$data['loginName'],WSTConf("CONF.mallName"),$data['handleReamrk']];
		        WSTSendMsg($data['userId'],str_replace($find,$replace,$tpl['tplContent']),['from'=>0,'dataId'=>0]);
		    }
		    //微信消息
			if((int)WSTConf('CONF.wxenabled')==1){
				$params = [];
				$params['SHOP_NAME'] = $data['shopName'];
				$params['APPLY_TIME'] = $data['createTime'];
				$params['NOW_TIME'] = date('Y-m-d H:i:s');
				$params['REASON'] = $data['handleReamrk'];
				WSTWxMessage(['CODE'=>'WX_SHOP_OPEN_FAIL','userId'=>$data['userId'],'params'=>$params]);
			} 
	    }
	}
	    
    /**
     * 获取记录
     */
	public function getById($id){
		return $this->alias('a')->join('__USERS__ u','a.userId=u.userId')->where([["a.id","=",(int)input("id")],["a.dataFlag","=",1]])->field('u.loginName,u.userName,a.*')->find();
	}
	
	/**
	 * 删除
	 */
    public function del(){
	    $id = (int)input('post.id');
	    Db::startTrans();
		try{
		    $result = $this->where(['id'=>$id])->update(['dataFlag'=>-1]);	
	        if(false !== $result){
	        	Db::commit();
	        	return WSTReturn("删除成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1); 
	}

	/**
	 * 分页列表
	 */
	public function pageQuery(){
		$linkkey = input('linkkey');
	    $where = [];
		$where[] = ["u.dataFlag","=",1];
		$where[] = ["a.dataFlag","=",1];
		if($linkkey!='')$where[] = ["a.linkman|a.linkPhone","like","%".$linkkey."%"];
		return $this->alias("a")
		->join("__USERS__ u","u.userId=a.userId","left")
		->where($where)->field("u.loginName,u.userName,a.*")->order("a.id desc")->paginate(input("limit/d"));
	}
}
