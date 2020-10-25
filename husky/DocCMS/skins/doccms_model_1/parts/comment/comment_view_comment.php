<?php
    // 为方便并保证您以后的快速升级 请使用SHL提供的如下全局数组
	
	// 数组定义/config/doc-global.php
	
	// 如有需要， 请去掉注释，输出数据。
	/*
	echo '<pre>';
		print_r($tag);
	echo '</pre>';
	*/
$comment_mdtp=intval($request['comment_mdtp']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $moduleTitle ?></title>
<meta name="keywords" content="DocCms，稻壳Cms，模板，稻壳网，企业建站，网站建设，互联网，信息化，网站管理，seo，网站推广，网站系统，php，免费，开源" />
<meta name="description" content="稻壳企业建站系统，又名稻壳cms、doccms，前身源于深喉咙企业建站系统ShlCms，是业内领先的免费开源企业网站建设系统、企业网站生成系统。DocCms采用网站功能模块化和网站风格模板化的设计方针，使得用户制作网站更加简易快捷，从而在企业网站建设领域应用非常广泛，是成熟的企业建站系统之一，目前正在为数十万计的中小企业服务。" />
<style type="text/css">
* { padding:0; margin:0; }
img { border:none; }
.guestbk, .guestbk p, #guestsmt { width:100%; float:left; }
.ask-box { background:#EEE; margin-bottom:30px; float:left; }
.ask-box .wrap { background:#EEE;/* 修正IE6 */ _position:relative; _z-index:10; } /* arrow-effect */
.ask-left { border-left:20px solid #FFF; border-top:20px solid #EEE; margin-top:20px; }
.answer-right { border-right:20px solid #FFF; border-top:20px solid #4FBCD8; }
.ask-left .wrap, .answer-right .wraps { padding:12px 10px 12px 10px; margin-top:-40px; line-height:25px; width:auto!important; max-width:500px;  /* sets min-width & max-width for ie */ _width: expression(document.body.clientwidth > 500 ? "500px" : "auto");
}
.answer-box { background:#4FBCD8; margin-bottom:30px; float:right; }
.answer-box .wraps { background:#4FBCD8;/* 修正IE6 */ _position:relative; _z-index:10; color:#fff; } /* arrow-effect */
.useript { background-color: white; border-color: #CCCCCC #E2E2E2 #E2E2E2 #CCCCCC; border-style: solid; border-width: 1px; box-shadow: 1px 2px 3px #F0F0F0 inset; overflow: hidden; padding: 5px 0 3px 8px; vertical-align: middle; }
#guestsmt { padding:30px 0 30px 10px; }
.guestinfo { width:96%; height:80px; margin-bottom:15px; float:left; }
#guestsmt p { width:97%; }
#guestsmt span { font-family:"微软雅黑"; font-size:14px; }
.usertel { width:120px; margin-right:15px; }
.userbtn { width:80px; height:40px; font-family:"微软雅黑"; font-size:20px; border:none; float:left; cursor:pointer; }
#commentlist { float:left; width:97%; margin:0 10px; font-size:12px; display:inline; }
#commentlist ul { list-style:none; border:1px solid #e9e9e9; border-bottom:none; padding-top:30px; }
#commentlist ul li { border-bottom:1px solid #e9e9e9; }
#commentlist ul li p { margin:0 15px; padding:15px; line-height:25px; }
#commentlist ul li a { margin:0 15px; color:#fff; padding:0.5em 2em; text-decoration:none; }
#commentlist ul li a:hover { color:#fc3; }
.commettext { border-top:1px dashed #e9e9e9; }
.comtitle { height:26px; line-height:26px; padding:0 0 0 20px; color:#999; background:#f6f6f6; }
#commentlist h1 { text-align:center; padding:20px 0; font-family:"微软雅黑"; font-size:22px; font-weight:normal; color:#234E9B; }
#commentlist h1 a { color:#f30; font-size:12px; padding-left:20px; }
.button { background-color: #ECECEC; background-image: -moz-linear-gradient(#F4F4F4, #ECECEC); border: 1px solid #D4D4D4; border-radius: 0.2em 0.2em 0.2em 0.2em; color: #333333; cursor: pointer; display: inline-block; font:12px "微软雅黑"; margin: 0; outline: medium none; overflow: visible; padding: 0.3em 1em; position: relative; text-decoration: none; text-shadow: 1px 1px 0 #FFFFFF; white-space: nowrap; }
.button:hover, .button:focus, .button:active { background-color: #3072B3; background-image: -moz-linear-gradient(#599BDC, #3072B3); border-color: #3072B3 #3072B3 #2A65A0; color: #FFFFFF; text-decoration: none; text-shadow: -1px -1px 0 rgba(0, 0, 0, 0.3); }
.usersbmt { float:right; margin-top:25px; }
.savebt { -moz-border-bottom-colors: none; -moz-border-image: none; -moz-border-left-colors: none; -moz-border-right-colors: none; -moz-border-top-colors: none; background-color: #7FBF4D; background-image: -moz-linear-gradient(center top, #7FBF4D, #63A62F); border-color: #63A62F #63A62F #5B992B; border-radius: 3px 3px 3px 3px; border-style: solid; border-width: 1px; box-shadow: 0 1px 0 0 #96CA6D inset; color: #FFFFFF; font: 12px; padding: 5px 0 5px; text-align: center; text-shadow: 0 -1px 0 #4C9021; width:80px; cursor:pointer; }
.savebt:hover { background-color: #76B347; background-image: -moz-linear-gradient(center top, #76B347, #5E9E2E); box-shadow: 0 1px 0 0 #8DBF67 inset; cursor: pointer; }
.pager{ float:left; width:99%;}
#articeBottom {font-size: 14px; margin: 6px 0 10px; padding-top: 10px; text-align: right; width: 97%;}
#articeBottom a{ font-size:12px; color:#666;}
#articeBottom a:hover{ color:#0099FF;}
</style>
</head>
<?php
$sourceUrl = null;
if($request['r']>0)
{
	$sourceUrl = sys_href($request['p'],sys_menu_info('type',false,$request['p']),$request['r']);
}else{
	$sourceUrl = sys_href($request['p']);
} 
?>
<body>
<div id="commentlist">
  <h1>话题：<?php echo $moduleTitle; ?><a target="_blank" href="<?php echo $sourceUrl; ?>">[查看原文]</a></h1>
  <?php
	if(!empty($tag['data.results']))
	{
		?>
  <ul>
    <?php
		foreach($tag['data.results'] as $data)
		{
			?>
    <li>
      <div class="comtitle"> <?php echo $data['dtTime']  ?>&nbsp;&nbsp;&nbsp;&nbsp;
        <?php 
			if(intval($data['memberId'])==0)
				echo '昵称:（游客）'.$data['name'];
			else
				echo '昵称:（会员）'.$data['nickname'];
			?>
      </div>
      <p><?php echo $data['content']; ?></p>
      <?php 
			if(!empty($tag['data.other']['username']) || $tag['data.other']['userlevel']>=8)
			{
				if($data['auditing'] == '1')
				{
					?>
      <p class="commettext"><a href="<?php echo sys_href($request['p'],'destroycomment',$data['id'],$comment_mdtp); ?>" class="savebt">删除</a><a class="savebt">已审核</a></p>
      <?php
				}
				else 
				{
					?>
      <p class="commettext"><a href="<?php echo sys_href($request['p'],'destroycomment',$data['id'],$comment_mdtp); ?>" class="savebt">删除</a><a href="<?php echo sys_href($request['p'],'auditingcomment',$data['id'],$comment_mdtp); ?>" class="savebt">审核</a></p>
      <?php	
				}		 
			}
  			?>
      </tr>
      <?php
		}
		?>
    </li>
  </ul>
</div>
<div class="pager">
  <?php if(!empty($tag['pager.cn'])) echo $tag['pager.cn'];?>
</div>
<?php
	}
	?>
<div class="guestbk">
  <form action="<?php echo sys_href($request['p'],'submitcomment',$request['r'],$comment_mdtp);?>" method="post">
    <div id="guestsmt">
      <textarea name="content" cols="" rows="" class="useript guestinfo"></textarea>
      <p> <span>昵称：</span>
        <input name="name" type="text" id="name" class="useript usertel"  value="<?php echo $data['nickname'] ?>"/>
        <span>E-mail：</span>
        <input name="email" type="text" id="email" class="useript usertel"  value="<?php echo $data['email'] ?>"/>
        <span>验证码：</span>
        <input name="checkcode" type="text" id="checkcode" class="useript usertel"  value=""/>
        <img src="<?php echo $tag['path.root']; ?>/inc/verifycode.php"> </p>
      <p>
        <input name="" type="submit" value="提 交" class="button usersbmt" />
      </p>
    </div>
  </form>
</div>
</body>
</html>