<?php
namespace wstmart\admin\model;
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
 * 商城消息业务处理
 */
class Messages extends Base{
	/**
	 * 分页
	 */
	public function pageQuery(){
		$where[] = ['m.dataFlag','=','1'];
		$msgType = (int)input('msgType');  
		if($msgType >= 0)$where[] = ['msgType','=',$msgType];
		$msgContent = input('msgContent');
		if(!empty($msgContent))$where[] = ['msgContent','like',"%$msgContent%"];
		$rs = $this->alias('m')
		->field('m.*,u.loginName,s.shopName,st.loginName stName')
		->join('__USERS__ u','m.receiveUserId=u.userId','left')
		->join('__SHOPS__ s','m.receiveUserId=s.shopId','left')
		->join('__STAFFS__ st','m.sendUserId=st.staffId','left')
		->order('id desc')
		->where($where)
		->paginate(input('limit/d'))->toArray();
	    foreach ($rs['data'] as $key => $v){
         	$rs['data'][$key]['msgContent'] = WSTMSubstr(strip_tags(htmlspecialchars_decode($v['msgContent'])),0,140);
        }
		return $rs;
	}
	public function getById($id){
		$rs = $this->get(['id'=>$id,'dataFlag'=>1]);
		$rs['msgContent'] = str_replace('${DOMAIN}',WSTConf('CONF.resourcePath'),htmlspecialchars_decode($rs['msgContent'])); 	
        return $rs;
	}
	/**
	 * 新增
	 */
	public function add(){
		$data = input('post.');
		$data['createTime'] = date('Y-m-d H:i:s');
		$data['sendUserId'] = session('WST_STAFF.staffId');
		//判断发送对象
		if($data['sendType']=='theUser'){
			$ids = explode(',',$data['htarget']);
			if($data['htarget']=='')return WSTReturn('请选择指定接收消息的账号');
		}elseif($data['sendType']=='shop'){
			//获取所有店铺的id
			$ids = model('Shops')->getAllShopUserId();
		}elseif($data['sendType']=='supplier'){
			//获取所有店铺的id
			$ids = model('Suppliers')->getAllSupplierUserId();
		}elseif($data['sendType']=='users'){
			//获取所有用户id
			$ids = model('users')->getAllUserId();
		}
		if($data['msgContent']=='')return WSTReturn('请输入要发送的消息');
		// 图片记录
		$rule = '/src="\/(upload.*?)"/';
		//对图片域名进行处理
		$data['msgContent'] = WSTRichEditorFilter($_POST['msgContent']);
        $data['msgContent'] = str_replace(WSTConf('CONF.resourcePath').'/upload/','${DOMAIN}/upload/',$data['msgContent']);
        
        preg_match_all($rule,$data['msgContent'],$result);
        // 获取src数组
        $imgs = $result[1];
		WSTUnset($data,'id,sendType,htarget');//删除多余字段
		$list = [];
		//去重
		array_unique($ids);
		foreach($ids as $v){
			$data['receiveUserId'] = $v;
			$data['msgType'] = 0;//后台手工发送消息
			$list[] = $data;
		}
		Db::startTrans();
		try{
			$result = $this->allowField(true)->saveAll($list);
			$id = $result[0]['id'];//新增的第一条消息id
        	if(false !== $result){
        	    //启用上传图片
			    WSTUseResource(1, $id, $imgs);
        		Db::commit();
        	    return WSTReturn("新增成功", 1);
        	}
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('新增失败',-1);
        
	}
	/**
	 * 删除
	 */
    public function del(){
	    $id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
	    $result = $this->update($data,['id'=>$id]);
        if(false !== $result){
        	return WSTReturn("删除成功", 1);
        }else{
        	return WSTReturn($this->getError(),-1);
        }
	}
	/**
	 * 批量删除
	 */
	public function batchDel(){
		$ids = input('ids');
		$ids = explode(',',WSTFormatIn(',',$ids));
		$data = [];
		$data['dataFlag'] = -1;
		$result = $this->where('id','in',$ids)->update($data);
		if(false !== $result){
			return WSTReturn("删除成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
}
