<?php
namespace wstmart\home\model;
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
class ShopUsers extends Base{
	/**
	 * 角色列表
	 */
	public function pageQuery(){
		$shopId = (int)session('WST_USER.shopId');
		$userName = input("userName/s");
		$where = ["s.shopId"=>$shopId,"s.dataFlag"=>1];
		if($userName != ""){
			$where[] = ["loginName","like","%".$userName."%"];
		}
		$page = $this->alias('s')
	    		->join("__SHOP_ROLES__ r","s.roleId=r.id and r.dataFlag=1","LEFT")
	    		->join("__USERS__ u", "u.userId=s.userId and u.dataFlag=1")
	    		->field('s.id,s.shopId,s.roleId,u.userName,u.loginName,u.createTime,u.userStatus,r.roleName')
				->where($where)
				->paginate(input('pagesize/d'))->toArray();
		return $page;
	}

	/**
	*  根据id获取店铺用户
	*/
	public function getById(){
		$id = (int)input('id');
		$shopId = (int)session('WST_USER.shopId');
	    $user = $this->alias('s')
	    		->join("__SHOP_ROLES__ r","s.roleId=r.id","LEFT")
	    		->join("__USERS__ u", "u.userId=s.userId and u.dataFlag=1")
	    		->field('s.id,s.shopId,s.roleId,u.userName,u.loginName,u.createTime,u.userStatus,r.roleName')
				->where([["s.id",'=',$id],["s.shopId",'=',$shopId],["s.dataFlag",'=',1]])
				->find();
		return $user;
	}

	
	/**
     * 新增店铺用户
     */
    public function add(){
    	$data = array();
    	$roleId = (int)input("roleId");
    	$data['loginName'] = input("post.loginName");
    	$data['loginPwd'] = input("post.loginPwd");
    	$data['reUserPwd'] = input("post.reUserPwd");
    	$loginName = $data['loginName'];
    	if($roleId<=0){
    		return WSTReturn('非法操作');
    	}
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
    	$data['userType'] = 1;
    	Db::startTrans();
        try{
	    	$userId = Db::name("users")->insertGetId($data);
	    	if(false !== $userId){
	    		//添加门店用户
	    		$shopId = (int)session('WST_USER.shopId');
	    		$data = array();
	    		$data["shopId"] = $shopId;
	    		$data["userId"] = $userId;
	    		$data["roleId"] = (int)input("roleId");
	    		Db::name('shop_users')->insert($data);
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

    	$shopId = (int)session('WST_USER.shopId');
    	Db::startTrans();
		try{
	    	$data = array();
	    	$roleId = (int)input("post.roleId");
	    	$id = (int)input("post.id");
	    	$newPass = input("post.newPass/s");
	    	if($newPass!=""){
	    		$decrypt_data = WSTRSA($newPass);
		    	if($decrypt_data['status']==1){
		    		$newPass = $decrypt_data['data'];
		    	}else{
		    		return WSTReturn('修改失败');
		    	}
		    	if(!$newPass){
		    		return WSTReturn('密码不能为空',-1);
		    	}
		    	$roleUser = $this->where(["id"=>$id,"shopId"=>$shopId])->find();
		    	$userId = $roleUser["userId"];
		    	$rs = model("users")->where(["userId"=>$userId])->find();
		    	//核对密码
		  
				$oldPass = input("post.oldPass");
				$decrypt_data2 = WSTRSA($oldPass);
				if($decrypt_data2['status']==1){
					$oldPass = $decrypt_data2['data'];
				}else{
					return WSTReturn('修改失败');
				}
				if($rs['loginPwd']==md5($oldPass.$rs['loginSecret'])){
					
						$data["loginPwd"] = md5($newPass.$rs['loginSecret']);
						$rs = model("users")->update($data,['userId'=>$userId]);
						if(false !== $rs){
							$this->where([['roleId','>',0],['id','=',$id],['shopId','=',$shopId]])->update(["roleId"=>$roleId]);
							hook("afterEditPass",["userId"=>$userId]);
						}else{
							return WSTReturn("修改失败", -1);
						}
						Db::commit();
				 		return WSTReturn("修改成功", 1);
			       
				}else{
					return WSTReturn('原始密码错误',-1);
				}
	    	}else{
	    		$this->where([['roleId','>',0],['id','=',$id],['shopId','=',$shopId]])->update(["roleId"=>$roleId]);
	    		Db::commit();
				return WSTReturn("修改成功", 1);
	    	}
		}catch (\Exception $e) {
        	Db::rollback();
        }
    }

	

	/**
	 * 删除店铺用户
	 */
	public function del(){
		$shopId = (int)session('WST_USER.shopId');
		$id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
		Db::startTrans();
		try{
			$role = $this->where([["id",'=',$id],["shopId",'=',$shopId]])->field("userId,id")->find();
			$result = $this->where([["id",'=',$id],["shopId",'=',$shopId],["roleId",'>',0]])->update($data);
		   	if(false !== $result){
		   		Db::name("users")->where([["userId",'=',$role["userId"]]])->update(['dataFlag'=>-1]);
		   		Db::commit();
	        	return WSTReturn("删除成功", 1);
	        }
     	}catch (\Exception $e) {
        	Db::rollback();
        }
        return WSTReturn('删除失败',-1);
	}
}
