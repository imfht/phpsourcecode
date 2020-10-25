<?php
function index()
{
	checklogin();
	global $db;
	global $request;
	global $tag;
	global $user;
	$sql="SELECT * FROM ".TB_PREFIX."user WHERE id=".$_SESSION[TB_PREFIX.'user_ID'];
	$user = $db->get_row($sql);
}
function reg()
{
	global $db;
	global $request;
	global $tag;
	
	if($_POST)
	{
		foreach ($request as $k=>$v)
		{
			$request[$k]=RemoveXSS($v);
		}
		if(!empty($request['username']))
		{
			$sql="SELECT COUNT(*) FROM ".TB_PREFIX."user WHERE username='".$request['username']."'";
			$isUser = $db->get_var($sql);
			$isUser>0?exit('该用户名已存在,请更换一用户名!'):'';
		}else{
			exit('用户名不能为空');
		}
		//必填验证
		require(ABSPATH.'/inc/class.validate.php');
		if(!validate::username(4,16,$request['username'],'ENNUM')){
			die('用户名为4至6位的英文或英文+数字！');
		}
		if(!validate::is_email($request['email'])){
			die('请确认邮箱填写正确！');
		}
		if($request['pwd']!=$request['repwd']){
			die('两次密码不一致！');
		}
		if(!validate::password(6,16, $request['pwd'])){
			die('密码长度6至16位！');
		}
		
		//加载数据库类
		require_once(ABSPATH.'/inc/models/user.php');
		require_once(ABSPATH.'/inc/class.docencryption.php');
		$docEncryption = new docEncryption($request['pwd']);
		$user = new c_user();
		$user->addnew();
		//必填字段
		$user->get_request($request);
		$user->pwd = $docEncryption->to_string();
		$user->role = '1';
		$user->auditing = '1';
		$user->dtTime=date('Y-m-d H:i:s');
		$user->ip=$_SERVER['REMOTE_ADDR'];
		if($user->save()){
			echo '<script language="javascript">alert("恭喜，注册成功！");</script>';
		}else{
			echo '<script language="javascript">alert("对不起，注册失败！");</script>';
		}
		if($request['p'])
		redirect(sys_href($request['p'],'user','login'));
		else
		redirect('/?m=user');
	}
}
function login()
{
	global $db;
	global $request;
	global $tag;
		
	if(!empty($request['username']) || !empty($request['P']))
	{	

		foreach ($request as $k=>$v)
		{
			$request[$k]=RemoveXSS($v);
		}	
		if(checkPwd($request['username'],$request['pwd'],$flag=false))
		{
			echo "<script language='javascript'>alert('登录成功，您现在可以以会员的身份浏览网页!');</script>";
			if(!empty($request['url']))	
			redirect($request['url']);
			else	
			redirect(sys_href($request['p'],'user'));
		}else{
			echo '<script language="javascript">alert("用户名或密码错误,登录失败!");history.back(1);</script>';
		}
		exit;
	}
}
function edit()
{
	checklogin();
	global $db;
	global $request;
	global $tag;
	
	if($_POST)
	{
		foreach ($request as $k=>$v)
		{
			$request[$k]=RemoveXSS($v);
		}
		//必填验证
		require(ABSPATH.'/inc/class.validate.php');
		if(!validate::is_email($request['email'])){
			die('请确认邮箱填写正确！');
		}
		require(ABSPATH.'/inc/models/user.php');
		$user = new c_user();
		$user->id=$_SESSION[TB_PREFIX.'user_ID'];
		
		$user->name=$request['name'];
		$user->email=$request['email'];
		$user->nickname=$request['nickname'];
		$user->age=$request['age'];
		$user->mtel=$request['etel'];
		$user->sex=$request['sex'];
		$user->qq=$request['qq'];
		$user->msn=$request['msn'];
		$user->email=$request['email'];
		$user->address=$request['address'];
		if($user->save())
		{	
			redirect(sys_href($request['p'],'user','edit'));		
		}else{
			exit('<script language="javascript">alert("修改失败!");history.back(1);</script>');
		}
	}else{
		global $user;
		$sql="SELECT * FROM ".TB_PREFIX."user WHERE id=".$_SESSION[TB_PREFIX.'user_ID'];
		$user = $db->get_row($sql);
	}
}
//会员密码修改   by grysoft
function editpwd()
{
	checklogin();
	global $db;
	global $request;
	global $tag;
	
	if($_POST)
	{
		foreach ($request as $k=>$v)
		{
			$request[$k]=RemoveXSS($v);
		}
		require(ABSPATH.'/inc/class.validate.php');
		$sql='SELECT * FROM '.TB_PREFIX.'user WHERE id='.$_SESSION[TB_PREFIX.'user_ID'];
			$row = $db->get_row($sql);
	
			require(ABSPATH.'/inc/models/user.php');
			$user = new c_user();
			$user->id=$_SESSION[TB_PREFIX.'user_ID'];
			
			if(!empty($request['newpass'])){//预留修改密码
				if(!validate::password(6,16, $request['newpass']))die('密码长度6至16位！');
				require_once(ABSPATH.'/inc/class.docencryption.php');
				$docEncryption = new docEncryption($request['pwd']);
				$pwd = $docEncryption->to_string();
				if($request['newpass'] == $request['repwd'])
				{
					if($pwd == $row->pwd )
					{
						$docEncryption = new docEncryption($request['newpass']);
						$user->pwd = $docEncryption->to_string();
						if($user->save())
						{	
							echo '<script>alert("恭喜，密码修改成功。");</script>';	
							redirect(sys_href($request['p'],'user'));	
						}
				
						else
						{
							echo '<script language="javascript">alert("修改失败!");history.back(1);</script>';
							exit;
						}
					}
					else
					{
						echo '<script>alert("密码错误");history.back(1);</script>';
					}
				}
				else
				{
					echo '<script>alert("重复密码错误");history.back(1);</script>';
				}
			}
			else
			{
				echo '<script>alert("密码不能为空");history.back(1);</script>';
			}		
		
	}else{
		global $user;
		$sql="SELECT * FROM ".TB_PREFIX."user WHERE id=".$_SESSION[TB_PREFIX.'user_ID'];
		$user = $db->get_row($sql);
	}
}
//会员头像上传   by grysoft
function editpic()
{
	checklogin();
	global $db;
	global $request;
	
	require(ABSPATH.'/inc/models/user.php');
	require(ABSPATH.'/inc/class.upload.php');	
	$id   = $_SESSION[TB_PREFIX.'user_ID'];	
	$user = new c_user();	
	$user->id=$id;
	
	$sql = "SELECT * FROM ".TB_PREFIX."user WHERE id='$id'";
	$row = $db->get_row($sql);

	if(!empty($_FILES['uploadfile']) && $_FILES['uploadfile']['size']>0 && $_FILES['uploadfile']['size']<200000)
	{
		@unlink(ABSPATH.$row->originalPic);
		@unlink(ABSPATH.$row->smallPic);
		
		$upload = new Upload();
		$upload->AllowExt='jpg|jpeg|gif|bmp|png';
		$fileName = $upload->SaveFile('uploadfile');
		
		if(empty($fileName))echo $upload->showError();				
		require_once(ABSPATH."/inc/class.paint.php");
		$paint = new Paint(UPLOADPATH.$fileName);
		$user->originalPic =UPLOADPATH.$fileName;
		$user->smallPic=$paint->Resize(moduleUserWidth,moduleUserHight,'s_');				
	}
	if($user->save())
	{			
		redirect(sys_href($request['p'],'user','edit'));
	}
	else
	{
		echo '<script language="javascript">alert("修改失败!");history.back(1);</script>';
		exit;
	}	
}
//会员评论管理   by grysoft
function comment()
{
	checklogin();
	global $db;
	global $request;
	global $tag;
	global $user;
	$sql="SELECT * FROM ".TB_PREFIX."user WHERE id=".$_SESSION[TB_PREFIX.'user_ID'];
	$user = $db->get_row($sql);
	
	$sql="SELECT * FROM (SELECT * FROM `".TB_PREFIX."comment` WHERE memberId=".$_SESSION[TB_PREFIX.'user_ID'].") AS `temptable` ";

	$sb = new sqlbuilder('mdt',$sql,'id DESC',$db,6,true,URLREWRITE?'/':'./');
	if(!empty($sb->results))
	{
		foreach($sb->results as $k =>$v)
	    {
			$title    = '评论来源空标题';
			$recordId = '';
			$type = sys_menu_info('type',false,$v['channelId']);
			$from = sys_menu_info('title',false,$v['channelId']);
			$recordId = $v['recordId'];
			
			if(!empty($v['recordId']))
			{
				$sql = "SELECT title FROM ".TB_PREFIX.$type." WHERE id = ".$v['recordId'].' LIMIT 1';
				$title = $db->get_var($sql);
			}
			else
			{
				$v['recordId'] = $v['channelId'];
				$sql = "SELECT title FROM ".TB_PREFIX.$type." WHERE channelId = ".$v['recordId'].' ORDER BY pageId ASC LIMIT 1';
				$title = $db->get_var($sql);
			}
			$sb->results[$k]['title']	= $title;
			$sb->results[$k]['from']	= $from;
	        $sb->results[$k]['url']		= sys_href($v['channelId'],$type,$recordId);
	    }
		$tag['data.results']=$sb->results;
		if($sb->totalPageNo()>1)
		{
			$tag['pager.cn']=$sb->get_pager_show();
			$tag['pager.en']=$sb->get_en_pager_show();
		}
	}
	$sb=null;
}
//会员留言管理   by grysoft
function guestbook()
{
	checklogin();
	global $db;
	global $request;
	global $tag;
	global $user;
	$sql="SELECT * FROM ".TB_PREFIX."user WHERE id=".$_SESSION[TB_PREFIX.'user_ID'];
	$user = $db->get_row($sql);
	
	$sql="SELECT * FROM (SELECT * FROM `".TB_PREFIX."guestbook` WHERE uid=".$_SESSION[TB_PREFIX.'user_ID'].") AS `temptable` ";

	$sb = new sqlbuilder('mdt',$sql,'id DESC',$db,6,true,URLREWRITE?'/':'./');
	if(!empty($sb->results))
	{
		$tag['data.results']=$sb->results;
		if($sb->totalPageNo()>1) 
		{
			$tag['pager.cn']=$sb->get_pager_show();
			$tag['pager.en']=$sb->get_en_pager_show();
		}
	}
	$sb=null;
}
//会员订单管理   by grysoft
function order()
{
	checklogin();
	global $db;
	global $request;
	global $tag;
	global $user;
	$sql="SELECT * FROM ".TB_PREFIX."user WHERE id=".$_SESSION[TB_PREFIX.'user_ID'];
	$user = $db->get_row($sql);
	
	$sql="SELECT * FROM (SELECT * FROM `".TB_PREFIX."product_order` WHERE userid=".$_SESSION[TB_PREFIX.'user_ID'].") AS `temptable` ";

	$sb = new sqlbuilder('mdt',$sql,'id DESC',$db,6,true,URLREWRITE?'/':'./');
	if(!empty($sb->results))
	{
		$tag['data.results']=$sb->results;
		if($sb->totalPageNo()>1) 
		{
			$tag['pager.cn']=$sb->get_pager_show();
			$tag['pager.en']=$sb->get_en_pager_show();
		}
	}
	$sb=null;
}
function gotopay()
{
	checklogin();
	global $db;
	global $request;
	global $tag;
	global $user;
	$sql="SELECT * FROM ".TB_PREFIX."user WHERE id=".$_SESSION[TB_PREFIX.'user_ID'];
	$user = $db->get_row($sql);
	
	$sql="SELECT * FROM `".TB_PREFIX."product_order` WHERE id=".$request['r']." AND userid =".$_SESSION[TB_PREFIX.'user_ID'];
	$result = $db->get_row($sql);

	if($result)
	{
		$order=unserialize($result->orederinfo);

		foreach($order as $k=>$v){
		$subject[] =$v['title'];
		$price[]   =$v['preferPrice'];
		$body[]    =$v['title'].' * '.$v['num'];
		}
		$subject = @implode('<@>',$subject);
		$price   = @implode('<@>',$price);
		$body    = @implode('<@>',$body);
		$_SESSION[TB_PREFIX.'pay_price']  =$price;
		$_SESSION[TB_PREFIX.'pay_body']   =$body;
		$_SESSION[TB_PREFIX.'pay_subject']=$subject;
		$_SESSION[TB_PREFIX.'pay_orderId']=$result->orderId;

	}
	redirect(sys_href('','pay'));
}
function logout()
{
	session_start();
	session_destroy();
	setcookie(TB_PREFIX.'user_email','');
	setcookie(TB_PREFIX.'user','');
	setcookie(TB_PREFIX.'nick','');
	setcookie(TB_PREFIX.'user_roleId','');
	setcookie(TB_PREFIX.'user_ID','');
	if(URLREWRITE)redirect('/');else redirect('./index.php');
}

function checklogin()
{
	global $request;
	global $tag;
	if(!isset($_SESSION[TB_PREFIX.'user_ID']) || empty($_SESSION[TB_PREFIX.'user_ID']) ||empty($request['p'])){
		if($request['p'])
		redirect(sys_href($request['p'],'user','login'));
		else
		exit;
	}
}
function checkusername()
{
	global $db;
	global $request;
	global $tag;
	
	$username = trim($request['username']);
	$username=get_str($username);
	if(!empty($username))
	{
		$sql="SELECT COUNT(*) FROM ".TB_PREFIX."user WHERE username='".$username."'";
		$isUser = $db->get_var($sql);
		$isUser>0?exit('该用户名已存在,请更换一用户名!'):exit('恭喜！此用户名可以注册。');
	}else{
		exit('用户名不能为空');
	}
}
function checkPwd($username,$pwd,$flag=false)
{
	global $db;
	global $request;
	global $tag;
	$username=get_str($username);
	$sql="SELECT * FROM ".TB_PREFIX."user WHERE auditing='1' AND username='".$username."' limit 1";
	$rst=$db->get_row($sql);
	if($rst)
	{
		require_once(ABSPATH.'/inc/class.docencryption.php');
		$docEncryption = new docEncryption($pwd);
		if ($rst->pwd==$docEncryption->to_string()){
			$_SESSION[TB_PREFIX.'user_email']=$rst->email;
			$_SESSION[TB_PREFIX.'user']=$rst->username;
			$_SESSION[TB_PREFIX.'nick'] = $rst->nickname;
			$_SESSION[TB_PREFIX.'user_roleId'] = $rst->role;
			$_SESSION[TB_PREFIX.'user_ID'] = $rst->id;
			if($flag){
				$cookieTime =86400;
				setcookie('doc_username',$rst->username,time()+$cookieTime);
				setcookie('doc_pwd',$rst->pwd,time()+$cookieTime);
			}
			$sql='UPDATE '.TB_PREFIX.'user SET lastlogin ='.time().' WHERE id='.$rst->id;
			$db->query($sql);
			return true;
		}else{
			return false;
		}
	}else{
		return false;
	}
}
//问候语   by grysoft
function sayHello()
{
	$time = date('H');
	switch($time)
	{
		case $time>=6  && $time<9  : return '早上好，早餐很重要哦，喝杯牛奶吧！'        ;break;
		case $time>=9  && $time<11 : return '上午好，工作累了，走两步,让眼睛休息一下吧！';break;
		case $time>=11 && $time<13 : return '中午好，今天中午吃点什么呢？'			   ;break;
		case $time>=13 && $time<18 : return '下午好，工作累了，有空喝杯茶吧！'		   ;break;
		case $time>=18 && $time<21 : return '晚上好，今晚吃点什么呢？'				   ;break;
		case $time>=21 && $time<24 : return '晚上好，喝杯牛奶，放松一下心情吧！'		   ;break;
		case $time>=0  && $time<6  : return '深夜了，注意休息哦！'					   ;break;
		default : return 'Time Error!';;
	}
}
//数据统计   by grysoft
function info_num($type)
{
	global $db;
	if($type == 'message')
	{
		$sql = 'SELECT count(*) FROM '.TB_PREFIX.'guestbook WHERE  uid = '.$_SESSION[TB_PREFIX.'user_ID'];
		$num = $db->get_var($sql);
	}
	if($type == 'replay')
	{
		$sql = 'SELECT count(*) FROM '.TB_PREFIX.'guestbook WHERE auditing = 1 AND uid = '.$_SESSION[TB_PREFIX.'user_ID'];
		$num = $db->get_var($sql);
	}
	else if($type == 'comment')
	{
		$sql = 'SELECT count(*) FROM '.TB_PREFIX.'comment WHERE memberId = '.$_SESSION[TB_PREFIX.'user_ID'];
		$num = $db->get_var($sql);
	}
	return $num;
}
?>