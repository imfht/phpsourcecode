<?php
    // 为方便并保证您以后的快速升级 请使用SHL提供的如下全局数组
	
	// 数组定义/config/doc-global.php
	
	// 如有需要， 请去掉注释，输出数据。
	/*
	echo '<pre>';
		print_r($tag);
	echo '</pre>';
	*/
?>
<style>
.clear { clear:both; overflow:hidden; }
.admin_index { padding:10px; width:580px; }
.admin_index ul{ list-style:none;}
.admin_til { width:100%; height:30px; line-height:30px; }
.admin_til h3 { font-size:14px; font-weight:bold; }
.admin_til a { float:right; color:#FF3300; }
.admin_til a:hover { text-decoration:underline; }
.admin_wei { line-height:30px; }
.admin_menu { border:1px solid #ccc; height:28px; position:relative; background:-webkit-gradient(linear, 0 100%, 0 0, from(#E6E4E0), to(#ffffff)); background:-moz-linear-gradient(top, #ffffff, #E6E4E0); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E6E4E0');
-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E6E4E0')"; }
.admin_menu ul { position:absolute; left:20px; bottom:-1px; }
.admin_menu li { float:left; width:86px; height:24px; line-height:24px; text-align:center; border:1px solid #ccc; border-bottom:none; margin-right:4px; -moz-border-radius-topright: 5px; border-top-right-radius: 5px; -moz-border-radius-topleft: 5px; border-top-left-radius: 5px; }
.admin_menu li.hover { background:#fff; }
.admin_menu li a { display:block; width:86px; height:24px; }
.admin_main { border:1px solid #ccc; border-top:none; padding:22px 0 22px 0; }
.am_mess { padding-bottom:8px; }
.am_mess h3 { height:30px; border-bottom:1px solid #ccc; line-height:30px; background:#eee; margin-bottom:8px; padding:0 20px; font-weight:normal; font-size:12px; }
.am_mess p { line-height:20px; padding:0 10px; }
.am_mess div { border-bottom:1px dashed #ccc; overflow:hidden; height:8px; margin-bottom:8px; }
</style>
<?php global $user,$data; ?>
<div class="admin_index">
  <div class="admin_til"><a href="<?php echo sys_href($params['id'],'user','logout')?>">退出登录</a>
    <h3><?php echo $user->username; ?>，<?php echo sayHello();?></h3>
  </div>
 <p class="admin_wei">信息统计：我的留言 <?php echo info_num('message')?> 条， 我的评论 <?php echo info_num('comment')?> 条. 最后一次登录时间：<?php echo date('Y-m-d H:i:s',$user->lastlogin); ?></p>
  <div class="admin_menu">
    <ul>
      <li><a href="<?php echo sys_href($params['id'],'user')?>">中心首页</a></li>
      <li class="hover"><a href="<?php echo sys_href($params['id'],'user','guestbook')?>">留言管理</a></li>
      <li><a href="<?php echo sys_href($params['id'],'user','comment')?>">评论管理</a></li>
      <li><a href="<?php echo sys_href($params['id'],'user','order')?>">订单管理</a></li>
      <li><a href="<?php echo sys_href($params['id'],'user','edit')?>">基本资料</a></li>
      <li><a href="<?php echo sys_href($params['id'],'user','editpwd')?>">修改密码</a></li>
    </ul>
  </div>
  <div class="admin_main">
    <?php
if( !empty( $tag['data.results'] ) )
{
	foreach($tag['data.results'] as $k =>$data)
	{
	?>
    <div class="am_mess">
      <h3><?php echo $data['dtTime']; ?></h3>
      <p><span>留言内容：</span><?php echo $data['content']; ?></p>
      <div></div>
      <p><span>管理员回复：</span><?php echo $data['content1']; ?></p>
    </div>
    <?php
	}
}
else
{
	echo '暂无留言。';
}
?>
    <div  id="articeBottom"><?php echo $tag['pager.cn']; ?></div>
  </div>
</div>
