<?php
/**
 * DOCCMS 系统 sys 标签库
 * @author: grysoft    QQ:767912290
 * @copyright DOCCMS
 */
/*菜单项*/
$menus=array();
$subs=array();
$menuRoot=$db->get_results("SELECT * FROM ".TB_PREFIX."menu WHERE dtLanguage = '".$_SESSION[TB_PREFIX.'doclang']."' ORDER BY ordering ASC",ARRAY_A);
if(!empty($menuRoot))
{   
   foreach($menuRoot as $menu){
	  $menus[$menu['id']]=$menu; //重构菜单数组;
	  $subs[$menu['parentId']][]=$menu['id'];//频道子类数组;		  
   }	
}
//自定义参数输出
$tempId = $menu_arr['type']=='product'?sys_menu_info('id',true):$params['id'];
$tag['custom'] = $db->get_row('SELECT * FROM `'.TB_PREFIX.'models_set` WHERE channelId = '.$tempId,ARRAY_A);

/*****加载标签库*****/
$model_results = $db->get_results("SELECT type FROM ".TB_PREFIX."models_reg");
if($model_results){
	foreach ($model_results as $mt)
	{
		$tempfile=ABSPATH.'content/index/'.$mt->type.'.php';
		if(is_file($tempfile))require_once($tempfile);
	}
	require_once(ABSPATH.'content/index/__nav.php');
	require_once(ABSPATH.'content/index/focus.php');
	require_once(ABSPATH.'content/index/bbs.php');
}
//自定义表单输出  by grysoft
function sys_push($value='',$style='<p>{name}:{value}</p>',$tab=0,$coo='<|@|>')
{
	global $tag;
	$rs = $tag['custom'];
	if(!empty($rs['field'])||!empty($rs['field_tab']))
	{
		$fields  = explode('@',$rs['field']);
		$tabs 	 = explode('@',$rs['field_tab']);
		$data 	 = explode($coo,$value);
		if(!$tab)
		{
			for($s=0;$s<count($fields);$s++)
			{
				$palce = array('{name}'=>$fields[$s],'{value}'=>$data[$s],'{i}'=>$s);
				$push = strtr($style,$palce);	
				echo $push;
			}
		}
		else
		{
			for($s=0;$s<count($tabs);$s++)
			{
				$palce = array('{name}'=>$tabs[$s],'{value}'=>$data[$s],'{i}'=>$s);
				$push = strtr($style,$palce);
				echo $push;
			}
		}
	}
	else
	{
		echo $value;
	}
}
/*自定义表单单条数据输出  by grysoft   eg. <?php echo sys_push_one($value,'规格')?>  */
function sys_push_one($value='',$name=1,$coo='<|@|>')
{
	$data  = explode($coo,$value);
	for($i=0;$i<count($data);$i++)
	{
		if(($i+1)== $name)
		{
			return $data[$i];
		}
	}	
}
/**
 * 相关数据调用标签
 * @author：grysoft QQ:767912290
 * @copyright DOCCMS
 */
function sys_about($n=0,$style=0,$strcount=0,$model='',$id=0)
{
	global $db,$tag,$menu_arr;
	$data = $tag['data.row'];

	$data['channelId'] = $data['channelId']?$data['channelId']:0;
	
	$model      = empty($model)?$menu_arr['type']:$model;
	$data['id'] = empty($id)   ?$data['id']:$id;
	
	if(empty($model) || empty($data['id']))return;
	
	$path = 'index/__sys/'.$model.'_about_'.$style.'.php';
	if(is_file(get_abs_skin_root().$path))
	{
		$path = get_abs_skin_root().$path;
	}
	else
	{
		return ('加载'.$tag['path.skin'].'index/__sys/'.$model.'_about_'.$style.'.php 样式资源文件失败，程序意外终止。');
	}
	if(!is_int($strcount))return ('parameters $strcount is not integer in '.$opts['fun'].'()!');
	
	if($n%2==0)
	{
		$coo = (int)($n/2);
	}
	else
	{
		$coo = (int)($n/2+1);
	}
	
	$sql = 'SELECT * FROM  `'.TB_PREFIX.$model.'` WHERE channelId ='.$data['channelId'].' ORDER BY id ASC';
	$rs= $db->get_results($sql,ARRAY_A);
	$v		 = array();
	$results = array();
	if($rs)
	{
		for($i=0;$i<count($rs);$i++){
			$v[$rs[$i]['id']]=$rs[$i];
		}
		for($i=0;$i<count($v);$i++){
			if(isset($v[$data['id']-($i+1)])){
				$results[$data['id']-($i+1)] = $v[$data['id']-($i+1)];
			}
			if(count($results)>=$coo)
			break;
		}
		for($i=0;$i<count($v);$i++){
			if(isset($v[$data['id']+($i+1)])){
				$results[$data['id']+($i+1)] = $v[$data['id']+($i+1)];
			}
			if(count($results)>=$n)
			break;
		}
		
		foreach($results as $data)
		{
			$data['indexPic']      = ispic($data['indexPic']);
			$data['middlePic']     = ispic($data['middlePic']);
			$data['smallPic']      = ispic($data['smallPic']);
			$data['originalPic']   = ispic($data['originalPic']);
			$data['type']  		   = $model;
			
			$data['title']		   = sys_substr($data['title'],$strcount,false);
			require($path);
		}
	}
}
//邮件发送  by grysoft
function sys_mail($title,$body,$to=smtpReceiver)
{
	require_once(ABSPATH.'/inc/class.smtp.php');
	require_once(ABSPATH.'/inc/class.phpmailer.php');
	$mail = new PHPMailer();
	$mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
	$mail->IsSMTP();
	$mail->SMTPAuth   = true;         // SMTP服务器是否需要验证
	$mail->Host       = smtpServer;   // 设置SMTP服务器
	$mail->Port		  = smtpPort; 		  // 设置端口
	$mail->Username   = smtpId;      // 开通SMTP服务的邮箱帐号
	$mail->Password   = smtpPwd;      // 开通SMTP服务的邮箱密码
	$mail->From       = smtpSender;       // 发件人Email
	$mail->FromName   = WEBURL;        // 发件人昵称或姓名
	$mail->Subject    = WEBURL.$title;       // 邮件标题（主题）
	$mail->WordWrap   = 50;			  // 自动换行的字数
	$mail->MsgHTML($body);            // 邮件内容
	
	$receiver   = explode(';',$to);
	for($i=0;$i<count($receiver);$i++)
	{
		$mail->AddAddress($receiver[$i],"尊敬的站长"); //收件人地址。参数一：收信人的邮箱地址，可添加多个。参数二：收件人称呼
	}
	$mail->IsHTML(true); // 是否以HTML形式发送，如果不是，请删除此行
	
	$mail->Send();  //邮件发送
}
//站点访问统计标签  by grysoft
function sys_counts($times='all',$iswrite=false,$timefrom='',$timeover='')
{
    $counter_fname=ABSPATH.'/config/doc-counts';
	if(!is_file($counter_fname))
	{
		$fp=fopen($counter_fname, "w+");
	}
	$counter=file_get_contents($counter_fname); 
	$counts = array();
	$counts =  unserialize($counter);
	if($iswrite)
	{
		$time = date('Y-m-d');
		if(!isset($counts['all'])){
			$counts['all']=1;
		}else{
			$counts['all']++;
		}
		if(!isset($counts[$time])){
		   $counts[$time] = 1; 
		}else{
			$counts[$time]++;
		}
		$counter =serialize($counts);
		if($fp=fopen($counter_fname,'w')){	
			fputs($fp,$counter);
			fclose($fp);
		}
	}
	if($times=='all')
	{
		return $counts['all'];
	}
    else if(isset($counts[date('Y-m-d',strtotime($times.' day'))]))
	{
		return $counts[date('Y-m-d',strtotime($times.' day'))];
	}
	else
	{
		return '0';
	}
}

//菜单数据标签   by grysoft
function sys_menu_info($field='title',$ischannel=false,$id=0)
{
	global $params,$menus;
	$id=empty($id)?$params['id']:$id;		
	if($ischannel)
	{		
		if($menus[$id]['deep']!=0)
		{
			do
			{	  
			 	$id=$menus[$id]['parentId'];
			}while($menus[$id]['deep']!=0);			
		}		
		return $menus[$id][$field];
	}
	else
	{
	  return $menus[$id][$field];	
	}
}
//系统URL路由器  by grysoft
function sys_href($channelId=0,$type='article',$id=0,$action=0)
{
	global $db;
	if($type=='user')
	{
		$rs = $db->get_row('SELECT * FROM '.TB_PREFIX.'menu WHERE type ="user" LIMIT 1');
		if($rs)
		$channelId = $rs->id;
		else
		exit('对不起。您尚未创建会员频道，会员功能暂不可用。');	 
	}
	$menuName=sys_menu_info('menuName',false,$channelId);
	switch ($type)
	{
		case 'article':       //图片模块、频道栏目链接
			$link = URLREWRITE?'/'.$menuName.'/':'./?p='.$channelId;
			break;
		case 'comment':       //评论模块
		    $id = $id?$id:0;
			$action = $action?$action:0;
			$link = URLREWRITE?'/'.$menuName.'/comment_'.$id.'_'.$action.'.html':'./?p='.$channelId.'&a=view_comment&r='.$id.'&comment_mdtp='.$action;
			break;
		case 'submitcomment':       //评论模块 提交评论 action
		    $id = $id?$id:0;
			$link = URLREWRITE?'/'.$menuName.'/comment_submitcomment_'.$id.'_'.$action.'.html':'./?p='.$channelId.'&a=submitcomment&r='.$id.'&comment_mdtp='.$action;
			break;
		case 'destroycomment':       //评论模块 删除评论 action
		    $id = $id?$id:0;
			$action = $action?$action:0;
			$link = URLREWRITE?'/'.$menuName.'/comment_destroycomment_'.$id.'_'.$action.'.html':'./?p='.$channelId.'&a=destroycomment&comment='.$id.'&comment_mdtp='.$action;
			break;
		case 'auditingcomment':       //评论模块 审核评论 action
		    $id = $id?$id:0;
			$action = $action?$action:0;
			$link = URLREWRITE?'/'.$menuName.'/comment_auditingcomment_'.$id.'_'.$action.'.html':'./?p='.$channelId.'&a=auditingcomment&comment='.$id.'&comment_mdtp='.$action;
			break;
		case 'list':        //文章列表
			$link = URLREWRITE?'/'.$menuName.'/n'.$id.'.html':'./?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'download':    //下载模块
			$link = URLREWRITE?'/'.$menuName.'/action_download_'.$id.'.html':'./?p='.$channelId.'&a=download&r='.$id;
			break;
		case 'form_action': //表单提交
			$link = URLREWRITE?'/'.$menuName.'/action_create.html':'./?p='.$channelId.'&a=create';
			break;
		case 'guestbook':    //留言模块
			$link = URLREWRITE?'/'.$menuName.'/guestbook_'.$id.'.html':'./?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'video':    //视频模块
			$link = URLREWRITE?'/'.$menuName.'/video_'.$id.'.html':'./?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'view':         //view
			$link = URLREWRITE?'/'.$menuName.'/v'.$id.'.html':'./?p='.$channelId.'&a=view&r='.$id;
			break; 
		case 'job':    //招聘模块 提交页
			$link = URLREWRITE?'/'.$menuName.'/jobs_'.$id.'.html':'./?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'job_send':  //招聘模块 表单提交
			$link = URLREWRITE?'/'.$menuName.'/action_send_'.$id.'.html':'./?p='.$channelId.'&a=send&r='.$id;
			break;
		case 'picture':       //图片模块
			$link = URLREWRITE?'/'.$menuName.'/pic_'.$id.'.html':'./?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'poll':       //投票模块
			$link = URLREWRITE?'/'.$menuName.'/poll_'.$id.'.html':'./?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'poll_send':       //投票模块 提交投票
			$link = URLREWRITE?'/'.$menuName.'/action_send_'.$id.'.html':'./?p='.$channelId.'&a=send&r='.$id;
			break;
		case 'product':         //产品模块
			$link = URLREWRITE?'/'.$menuName.'/product_'.$id.'.html':'./?p='.$channelId.'&a=view&r='.$id;
			break;
		case 'product_basket': //查看购物车
			$link = URLREWRITE?'/'.$menuName.'/action_basket.html':'./?p='.$channelId.'&a=basket';
			break;
		case 'product_basketqr':  //购物车数据确认修改
			$link = URLREWRITE?'/'.$menuName.'/action_basketqr.html':'./?p='.$channelId.'&a=basketqr';
			break;
		case 'product_intobasket':  //购物车加入产品
			$link = URLREWRITE?'/'.$menuName.'/action_intobasket_'.$id.'.html':'./?p='.$channelId.'&a=intobasket&r='.$id;
			break;
		case 'product_basket_submit': //提交购物车
			$link = URLREWRITE?'/'.$menuName.'/action_submitbasket.html':'./?p='.$channelId.'&a=submitbasket';
			break;
		case 'rss':    //RSS
			$link = URLREWRITE?'/'.$menuName.'/rss_'.$id.'_'.$action.'.html':'./?p='.$channelId.'&a=get_rss&r='.$id.'$i='.$action;
			break;
		case 'user':     //会员中心
		$id=$id?$id:'index';
		$link = URLREWRITE?'/'.$menuName.'/'.$id.'.html':'./?p='.$channelId.'&a='.$id;
			break;
		case 'user_pay':     //会员中心  订单支付
		$link = URLREWRITE?'/'.$menuName.'/action_gotopay_'.$id.'.html':'./?p='.$channelId.'&a=gotopay&r='.$id;
			break;	
		case 'pay':  //在线支付
		if(empty($channelId))$link = URLREWRITE?'/pay.html':'./?m=pay';else $link = URLREWRITE?'/pay_'.$channelId.'.html':'./?m=pay&a='.$channelId;
			break;
		case 'search':  //搜索
		$link = URLREWRITE?'/search.html':'./?m=search';
			break;
		default:
		$link = URLREWRITE?'/'.$menuName.'/'.$type.'.html':'./?p='.$channelId.'&a='.$type;
	}
	return $link;
}
//获取SESSION
function sys_get_session($key=false){
    if(!isset($_SESSION)) return false;
    if($key){
        return $_SESSION[TB_PREFIX.$key];
    }else{
        return $_SESSION;
    }
}//设置SESSION
function sys_set_session($key,$value){
    return $_SESSION[TB_PREFIX.$key]=$value;
}