<?php
/*--------------------------------------------------------------------
  小微OA系统 - 让工作更轻松快乐 

  Copyright (c) 2013 http://www.smeoa.com All rights reserved.                                             

   

  Author:  jinzhu.yin<smeoa@qq.com>                         

  Support: https://git.oschina.net/smeoa/xiaowei        
  
--------------------------------------------------------------*/
namespace Home\Controller;
use Think\Controller;


class LdapController extends Controller {
	protected $config=array('app_type'=>'public');
	// 检查用户是否登录

	public function index(){
		if ($_POST){
			$opmode = $_POST["opmode"];
			$this->assign('opmode',$opmode);
			if($opmode=="sync"){
				$this->sync();
			}
		}
		$ldap_host = C("LDAP_SERVER");//LDAP 服务器地址
		$ldap_port = C("LDAP_PORT");//LDAP 服务器端口号
		$ldap_user = C("LDAP_USER"); //设定服务器用户名
		$ldap_pwd = C("LDAP_PWD"); //设定服务器密码
		$this->assign("ldap_host",$ldap_host);
		$this->assign("ldap_port",$ldap_port);		
		$this->assign("ldap_user",$ldap_user);
		$this->assign("ldap_pwd",$ldap_pwd);

		$this->display();
	}

	private function sync(){

		$ldap_host = C("LDAP_SERVER");//LDAP 服务器地址
		$ldap_port = C("LDAP_PORT");//LDAP 服务器端口号
		$ldap_user = C("LDAP_USER"); //设定服务器用户名
		$ldap_pwd = C("LDAP_PWD"); //设定服务器密码

		$ldap_conn = ldap_connect($ldap_host, $ldap_port) //建立与 LDAP 服务器的连接
		or die("Can't connect to LDAP server");
		ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION,3);
		$r=ldap_bind($ldap_conn, $ldap_user, $ldap_pwd) or die(ldap_error($ldap_conn));//与服务器绑定
		$base_dn = "cn=users,dc=laxdn,dc=com,dc=cn";//定义要进行查询的目录主键

		$filter_val="(ObjectClass=person)";
		$attr=array("uid","gecos","mail","uidNumber","gidNumber","departmentNumber","apple-birthday","mobile","telephoneNumber","employeeType","title","postalAddress","shadowExpire");
		$result= ldap_search($ldap_conn, $base_dn,$filter_val,$attr);//执行查询
		$entry= ldap_get_entries($ldap_conn,$result);//获得查询结果
		$emp_info=array();
		
		$dept_list=M("Dept")->getField("name,id");
		$rank_list=M("Rank")->getField("name,id");
		$position_list=M("Position")->getField("name,id");
		$role_list=M("Role")->getField("name,id");
	//	dump($entry);
		foreach($entry as $item){	
			if(is_array($item)){
				$data['id']=$item['uidnumber'][0];
				$data['emp_no']=$item['uid'][0];
				$data['name']=$item['gecos'][0];
				$data['letter']=get_letter($item['gecos'][0]);
				$data['email']=$item['mail'][0];
				$is_del=$item['shadowexpire'][0];	
				if($is_del==-1){
					$data['is_del']=0;
				}
				if($is_del==1){
					$data['is_del']=1;
				}

				$dept_name=$item['departmentnumber'][0];
				$data['dept_id']=$dept_list[$dept_name];	
				$birthday=$item['apple-birthday'][0];
				$data['birthday']=date("Y-m-d",strtotime($birthday));
				$data['mobile_tel']=$item['mobile'][0];	
				$data['office_tel']=$item['telephonenumber'][0];	
				$data['rank_id']=$rank_list[$item['employeetype'][0]];	
				$data['position_id']=$position_list[$item['title'][0]];	
				D("Role")->del_role($data['id']);	//删除用户权限
				D("Role")->set_role($data['id'],$role_list[$item["postaladdress"][0]]);	//用户权限初始化

				$rs=M("User")->add($data);				
				if($rs){
					$new++;
					$data['mail_name']=$item['gecos'][0];
					$data['pop3svr']="pop.laxdn.com.cn";
					$data['smtpsvr']="s.laxdn.com";
					$data['mail_id']=$item['uid'][0];									
					$rs_account=M("MailAccount")->add($data);
					M("UserConfig")->add($data); //用户配置信息初始化
				}else{
					$rs_save=M("User")->save($data);
					if($rs_save){
						$update++;
					}
				}
			}
		}
		$this->assign("new",$new);
		$this->assign("update",$update);
	}

		public function login(){
		$ldap_conn = ldap_connect("192.168.1.199:389") //建立与 LDAP 服务器的连接
		or die("Can't connect to LDAP server");
		ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION,3);
		$r=ldap_bind($ldap_conn, "CN=Administrator,CN=Users,DC=test,dc=local","Qiwei123") or die(ldap_error($ldap_conn));//与服务器绑定
		$base_dn = "CN=Users,DC=test,dc=local";//定义要进行查询的目录主键
		$justthese =array("cn","users");
		//搜索函数中的一个参数，要求返回哪些信息，
	
		}
		public function add(){
			$emp_no='101';
			$name='101';
			$ldap_conn = ldap_connect("121.42.30.248:636") //建立与 LDAP 服务器的连接
			or die("Can't connect to LDAP server");
			ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION,3);
			$r=ldap_bind($ldap_conn, "CN=Administrator,CN=Users,DC=ldap,dc=test","yin1234.") or die(ldap_error($ldap_conn));//与服务器绑定
			$info["objectClass"]="posixAccount";
	        $info["objectClass"]="top";
	        $info["objectClass"]="user";
			$info['name'] = $name;
			$info['userpassword']="Qiwei123";
			$info['userPrincipalName']= $emp_no;
			$info['userAccountControl']= 1;
			
			// $info['department']= $dept_name;
			// $info['title']= $position_id;
			$info['displayName']= $name;
			$info['cn']= $name;
	        ldap_add($ldap_conn,"CN=$name,CN=Users,DC=ldap,DC=test",$info)or die('no');
			ldap_close($ldap_conn);

		}
		public function update($name){
			$ldap_conn = ldap_connect("192.168.1.199:389") //建立与 LDAP 服务器的连接
			or die("Can't connect to LDAP server");
			ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION,3);
			$r=ldap_bind($ldap_conn, "CN=Administrator,CN=Users,DC=test,dc=local","Qiwei123") or die(ldap_error($ldap_conn));//与服务器绑定
			$dn="CN=$name,CN=Users,DC=test,DC=local";
			//$info['name'] = "ccc";
			$info['userPrincipalName']= "ccc";
			//$info['department']= $dept_name;
			//$info['title']= $position_id;
			$info['displayName']= "bbb";
			
			 ldap_modify($ldap_conn,$dn,$info) or die('no');
			ldap_unbind($ldap_conn);
		    ldap_close($ldap_conn);
		}
		public function del($name){
			$ldap_conn = ldap_connect("192.168.1.199:389") //建立与 LDAP 服务器的连接
			or die("Can't connect to LDAP server");
			ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION,3);
			$r=ldap_bind($ldap_conn, "CN=Administrator,CN=Users,DC=test,dc=local","Qiwei123") or die(ldap_error($ldap_conn));//与服务器绑定
			$dn="CN=$name,CN=Users,DC=test,DC=local";
			 ldap_delete($ldap_conn, $dn) or die('no');
			ldap_unbind($ldap_conn);
			ldap_close($ldap_conn);
			}
		
		
		function test(){
				
			$emp_no='105';
			$name='105';
			$pwd='Abc12345.';
			$ldap_conn = ldap_connect("ldaps://121.42.30.248",636) //建立与 LDAP 服务器的连接
			or die("Can't connect to LDAP server");

	 
			ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION,3);
			ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS,0);
		 
			$r=ldap_bind($ldap_conn, "Administrator@ldap.test","yin1234.") or die(ldap_error($ldap_conn));//与服务器绑定
		 
			//$r=ldap_bind($ldap_conn, "CN=Administrator,CN=Users,DC=ldap,DC=test","yin1234.") or die(ldap_error($ldap_conn));//与服务器绑定
			
		
			$info["objectClass"]="posixAccount";
	        $info["objectClass"]="top";
	        $info["objectClass"]="user";
			$info['name'] = $name;
			$info['userpassword']="Qiwei123";
			$info['userPrincipalName']= $emp_no;
			$info['userAccountControl']= 512;
			
			$info['unicodePwd']=mb_convert_encoding('"'.$pwd.'"', 'utf-16le');
			// $info['department']= $dept_name;
			// $info['title']= $position_id;
			$info['displayName']= $name;
			$info['cn']= $name;
	        ldap_add($ldap_conn,"CN=$name,CN=Users,DC=ldap,DC=test",$info)or die('no');
			ldap_close($ldap_conn);
		}
}
?>