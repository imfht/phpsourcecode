<?php
function submitcomment()
{
	global $db,$params,$request;
	global $tag;	// 标签数组
	if($_POST)
	{
		if ($_SESSION['verifycode'] != $request['checkcode'])
		{
			echo '<script>alert("请正确填写验证码！");location.href="javascript:history.go(-1)";</script>';
			exit;
		}
		else
		{
			if(!empty($request['content']))
			{
				require(ABSPATH.'/admini/models/comment.php');			
				$comment = new comment();
				$comment->addnew();
				//必填字段
				$comment->name=$request['name'];
				$comment->email=$request['email'];
				$comment->content=$request['content'];
				$comment->homepage=$request['homepage'];
				$comment->dtTime=date('Y-m-d H:i:s');
				$comment->channelId=$params['id'];
				$comment->ip=$_SERVER['REMOTE_ADDR'];
				$comment->recordId=$request['r'];
				
				if(intval($_SESSION[TB_PREFIX.'user_ID'])>0)
				{
					$comment->memberId=$_SESSION[TB_PREFIX.'user_ID'];
					$comment->memberTableName='user';
				}
				if(intval($_SESSION['shopping_admin_userID'])>0)
				{
					$comment->memberId=$_SESSION['shopping_admin_userID'];
					$comment->memberTableName='shopping_customers';
				}
				if(intval($_SESSION[TB_PREFIX.'admin_userID'])>0)
				{
					$comment->memberId=$_SESSION[TB_PREFIX.'admin_userID'];
					$comment->memberTableName='user';
				}
				if($comment->save())
				{
					if(COMMENTAUDITING)
					{
						echo '<script>alert("恭喜，您的评论已提交成功，为了不出现违法言论信息，我们设置了需经过审核才能显示您的评论，请等待我们的工作人员审核！");</script>';
					}
					redirect(sys_href($request['p'],'comment',$request['r'],$request['mdtp']));
				}
				else 
				{
					echo '<script>alert("对不起，系统错误，您的评论未能及时提交，请与我们的工作人员联系！");location.href="javascript:history.go(-1)";</script>';
					exit;
				}
			}
			else
			{
				echo '<script>alert("您的评论内容不能为空！");location.href="javascript:history.go(-1)";</script>';
				exit;
			}
		}
	}
}
function auditingcomment()
{
	global $db,$request;
	
	$comment_mdtp=intval($request['comment_mdtp']);	
	if(!empty($_SESSION[TB_PREFIX.'user']) || $_SESSION[TB_PREFIX.'admin_roleId']>=8)
	{
		if($request['comment'] > 0)
		{
			$sql='UPDATE '.TB_PREFIX.'comment SET auditing = 1 WHERE id='.$request['comment'].' limit 1';
			if($db->query($sql))
			{
				redirect(sys_href($request['p'],'comment',$request['r'],$comment_mdtp));
			}
			else
			{
				echo '<script>alert("对不起，系统错误，审核失败！");location.href="javascript:history.go(-1)";</script>';
				exit;
			}
		}
	}
	else
	{
		echo '<script>alert("对不起，您没有对该评论审核的权限！");location.href="javascript:history.go(-1)";</script>';
		exit;
	}
}
function destroycomment()
{
	global $db,$request;

	$comment_mdtp=intval($request['comment_mdtp']);

	if(!empty($_SESSION[TB_PREFIX.'user']) || $_SESSION[TB_PREFIX.'admin_roleId']>=8)
	{
		if($request['comment'] > 0)
		{
			$sql='DELETE FROM '.TB_PREFIX.'comment WHERE id='.$request['comment'].' limit 1';
			if($db->query($sql))
			{
				redirect(sys_href($request['p'],'comment',$request['r'],$comment_mdtp));
			}
			else
			{
				echo '<script>alert("对不起，系统错误，删除失败！");location.href="javascript:history.go(-1)";</script>';
				exit;
			}
		}
	}
	else
	{
		echo '<script>alert("对不起，您没有对该评论删除的权限！");location.href="javascript:history.go(-1)";</script>';
		exit;
	}
}
function view_comment()
{
	global $db;
	global $params;
	global $tag;	// 标签数组
	global $request;
	global $moduleTitle;
	
	$sql = "SELECT isComment FROM ".TB_PREFIX."menu WHERE id=".$params['id'];
	$isComment = $db->get_var($sql);
	if(($isComment == '1' && $params['action'] == 'view_comment') || ($isComment == '1' && ($params['model'] == 'article'|| $params['model'] == 'mapshow')))
	{
		$moduleTitle =  $tag['title'];
		
		$sql="SELECT a.*,b.nickname FROM ".TB_PREFIX."comment a left join  ".TB_PREFIX."user b on a.memberId=b.id WHERE a.channelId=".$params['id']." and a.recordId=".$params['args']." ORDER BY id DESC";
		
		$request['comment_mdtp'] = intval($request['comment_mdtp']);
		
		
		$countData   = count(totalPageNo($sql));
		$page = commentCount;
		
		if($countData%$page)
		$totalPageNo = intval($countData/$page)+1;
		else
		$totalPageNo = intval($countData/$page);
		
		$thisPageNo  = $request['comment_mdtp']<=1?1:$request['comment_mdtp'];

		if($request['comment_mdtp']<=1)
		$limit = ' LIMIT 0,'.$page;
		else
		$limit = ' LIMIT '.($page*($request['comment_mdtp']-1)).','.$page;
		
		$sql=$sql.$limit;
		
		$username=isset($_SESSION[TB_PREFIX.'user']) ? $_SESSION[TB_PREFIX.'user'] : '';
		$userlevel=isset($_SESSION[TB_PREFIX.'admin_roleId']) ? $_SESSION[TB_PREFIX.'admin_roleId'] : '';
		
		if(COMMENTAUDITING)
		{
			if(!empty($username) || $userlevel>=8 ){ }else{	$sql.=" and auditing=1 ";  }
		}
		$sb->results = $db->get_results($sql,ARRAY_A);
		
		if(!empty($sb->results)){
			$tag['data.results']=$sb->results;
			if($totalPageNo>1) 
			{
				$tag['pager.cn']=get_comment_pager($totalPageNo,$thisPageNo,$countData);
			}
			$tag['data.other']['username']=$username;
			$tag['data.other']['userlevel']=$userlevel;
		}
		$sb=null;
		//载入评论模块模版
			
		$part_path = ABSPATH.'/skins/'.STYLENAME.'/parts/comment/comment_view_comment.php';
		if(is_file($part_path))
		require($part_path);
		else 
		echo '<span style="color:RED"><strong>加载 /skins/'.STYLENAME.'/parts/comment/comment_index.php 样式资源文件失败，程序意外终止。</strong></span>';
	}else{
		echo 'Comment Forbidden ';
	}
	exit();
}
function get_comment_pager($totalPageNo,$thisPageNo,$countData)
{
	global $request,$tag;
	
	$request['comment_mdtp']=$request['comment_mdtp']<=1?1:$request['comment_mdtp'];
	
	$pageup   = $thisPageNo<=1?'':'<a href="'.sys_href($request['p'],'comment',$request['r'],$thisPageNo-1).'">上一页</a>';
	$pagedown = $thisPageNo==$totalPageNo?'':'<a href="'.sys_href($request['p'],'comment',$request['r'],$thisPageNo+1).'">下一页</a>';
	for($i=1;$i<=$totalPageNo;$i++)
	{
		$option .= '<option value="'.$i.'"'.($i==$thisPageNo?' selected':'').'>'.$i.'</option>';
	}
		
	if(URLREWRITE)
	{
		return '<div id="articeBottom">'.$thisPageNo.'/'.$totalPageNo.'页 共'.$countData.'条 <a href="/'.$tag['channel.menuname'].'/comment_'.$request['r'].'.html">首页</a> '.$pageup.$pagedown.' <a href="/'.$tag['channel.menuname'].'/comment_'.$request['r'].'_'.$totalPageNo.'.html">尾页</a> 跳转至<select name="pagerMenu" onChange="location=\'/'.$tag['channel.menuname'].'/comment_'.$request['r'].'_\'+this.options[this.selectedIndex].value+\'.html\'";>'.$option.'</select>页</div>';
	}
	else
	{
		return '<div id="articeBottom">'.$thisPageNo.'/'.$totalPageNo.'页 共'.$countData.'条 <a href="./?p='.$request['p'].'&a=view_comment&r='.$request['r'].'">首页</a> '.$pageup.$pagedown.' <a href="./?p='.$request['p'].'&a=view_comment&r='.$request['r'].'&comment_mdtp='.$totalPageNo.'">尾页</a> 跳转至<select name="pagerMenu" onChange="location=\'./?p='.$request['p'].'&a=view_comment&r='.$request['r'].'&comment_mdtp=\'+this.options[this.selectedIndex].value+\'\'";>'.$option.'</select>页</div>';
		
	}
}
function totalPageNo($sql)
{
	global $db;
	return $db->get_results($sql);
}