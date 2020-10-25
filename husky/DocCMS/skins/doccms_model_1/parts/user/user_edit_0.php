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
.avatar { border-right:1px dashed #ccc; width:154px; height:220px; padding:5px 0 0 32px; float:left; margin-right:42px; }
.avatar img { border:1px solid #ccc; margin-bottom:18px;  width:100px;height:100px}
.ch_tou { width:73px; height:24px; border:1px solid #ccc; -moz-border-radius:3px; border-radius:3px; color:#666; cursor:pointer; background:-webkit-gradient(linear, 0 100%, 0 0, from(#E2E2E2), to(#ffffff)); background:-moz-linear-gradient(top, #ffffff, #E2E2E2); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E2E2E2');
-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E2E2E2')"; }
.ad_btn { height:30px; padding:6px 0 0 220px; }
.ad_btn a { display:block; width:50px; height:20px; line-height:20px; text-align:center; border:1px solid #ccc; -moz-border-radius:5px; border-radius:5px; }
.mesWindow { background:#fff; width:530px; margin-left:-265px; top:140px; }
.mesWindowTop { width:503px; height:24px; line-height:20px; padding:6px 10px 0 17px; font-weight:bold; background:-webkit-gradient(linear, 0 100%, 0 0, from(#E6E4E0), to(#ffffff)); background:-moz-linear-gradient(top, #ffffff, #E6E4E0); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E6E4E0');
-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E6E4E0')"; }
.mesWindowTop span { float:left; }
.mesWindowTop .close { background:url(<?php echo $tag['path.skin']?>/res/images/x.png); border:none; cursor:pointer; width:18px; height:18px; float:right; }
.ad_tanchu { padding:12px 0; }
.ad_tanchu p { line-height:30px; }
.ad_tanchu p span { display:inline-block; width:180px; margin-right:15px; text-align:right; }
.ad_tanchu p input { width:180px; border:1px solid #ccc; height:18px; }
.ad_tanchu .ad_btn { height:30px; padding:6px 0 0 195px; }
</style>
<script type="text/javascript" src="<?php echo $tag['path.skin']?>/res/js/popup.js"></script>
<?php global $user; ?>
<script>
function testMessageBox(ev)
{
var objPos = mousePosition(ev);
var cont = '<div class="ad_tanchu"><form name="form1" method="post" action="<?php echo sys_href($params['id'],'user_edit')?>" >'
+'<p><span>用户名：</span><input name="username" id="username" type="text" value="<?php echo $user->username?>"/></p>'
+'<p><span>昵称：</span><input name="nickname" id="nickname" type="text" value="<?php echo $user->nickname?>" /></p>'
+'<p><span>真实姓名：</span><input name="name" id="name" type="text"value="<?php echo $user->name?>" /></p>'
+'<p><span>性别：</span><select name="sex" ><option value="1" <?php if($user->sex=='1') echo 'selected="selected"'; ?> >男</option><option value="2" <?php if($user->sex=='2') echo 'selected="selected"'; ?> >女</option></select></p>'
+'<p><span>年龄：</span><input name="age" id="age" type="text" value="<?php echo $user->age?>" /></p>'
+'<p><span>手机：</span><input name="etel" id="etel" type="text" value="<?php echo $user->etel?>" /></p>'
+'<p><span>E-mail：</span><input name="email" id="email" type="text" value="<?php echo $user->email?>" /></p>'
+'<p><span>QQ号码：</span><input name="qq" id="qq" type="text" value="<?php echo $user->qq?>" /></p>'
+'<p><span>MSN：</span><input name="msn" id="msn" type="text" value="<?php echo $user->msn?>" /></p>'
+'<p><span>地址：</span><input name="address" id="address" type="text" value="<?php echo $user->address?>" /></p>'
+'<div class="ad_btn"><input name="" type="submit" value="确定" /></div>'
+'</form></div>';
messContent=cont;
showMessageBox('资料编辑',messContent,objPos,350);
}
</script>
<div class="admin_index">
  <div class="admin_til"><a href="#">退出登录</a>
    <h3><?php echo $user->username; ?>，<?php echo sayHello();?></h3>
  </div>
  <p class="admin_wei">信息统计：我的留言 <?php echo info_num('message')?> 条， 我的评论 <?php echo info_num('comment')?> 条. 最后一次登录时间：<?php echo date('Y-m-d H:i:s',$user->lastlogin); ?></p>
  <div class="admin_menu bg_color">
    <ul>
      <li><a href="<?php echo sys_href($params['id'],'user')?>">中心首页</a></li>
      <li><a href="<?php echo sys_href($params['id'],'user','guestbook')?>">留言管理</a></li>
      <li><a href="<?php echo sys_href($params['id'],'user','comment')?>">评论管理</a></li>
      <li><a href="<?php echo sys_href($params['id'],'user','order')?>">订单管理</a></li>
      <li class="hover"><a href="<?php echo sys_href($params['id'],'user','edit')?>">基本资料</a></li>
      <li><a href="<?php echo sys_href($params['id'],'user','editpwd')?>">修改密码</a></li>
    </ul>
  </div>
  <div class="admin_main">
    <div class="avatar"> <img src="<?php echo ispic($user->smallPic)?>" />
      <div>
        <form action="<?php echo sys_href($params['id'],'user','editpic')?>" enctype="multipart/form-data" method="post">
          <input type="button" class="ch_tou" id="bu_1" value="修改头像" onclick="document.getElementById('bu_1').style.display='none';document.getElementById('uploadfile').style.display='block';document.getElementById('bu_2').style.display='block'"/>
          <input name="uploadfile" id="uploadfile" type="file" size="6"style="display: none;">
          <br>
          <input type="submit" value=" 上 传 " id="bu_2"  class="ch_tou" style="display: none;">
        </form>
      </div>
    </div>
    <div class="inad">
      <p><span>用户名：</span><?php echo $user->username?></p>
      <p><span>昵称：</span><?php echo $user->nickname?></p>
      <p><span>真实姓名：</span><?php echo $user->name?></p>
      <p><span>性别：</span><?php echo $user->sex==1?'男':'女';?></p>
      <p><span>年龄：</span><?php echo $user->age?></p>
      <p><span>手机：</span><?php echo $user->mtel?></p>
      <p><span>E-mail：</span><?php echo $user->email?></p>
      <p><span>QQ号码：</span><?php echo $user->qq?></p>
      <p><span>MSN：</span><?php echo $user->msn?></p>
      <p><span>地址：</span><?php echo $user->address?></p>
      <div class="ad_btn"><a href="#" class="bg_color" onclick="testMessageBox(event);"> 编 辑 </a></div>
    </div>
  </div>
</div>
