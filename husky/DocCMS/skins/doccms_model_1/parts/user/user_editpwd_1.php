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
.admin_main { border:1px solid #ccc; border-top:none; padding:22px 0 22px 0; height:220px; }
.avatar { border-right:1px dashed #ccc; width:154px; height:180px; padding:5px 0 0 32px; float:left; margin-right:42px; }
.avatar img { border:1px solid #ccc; margin-bottom:18px; }
.ch_tou { width:73px; height:24px; border:1px solid #ccc; -moz-border-radius:3px; border-radius:3px; color:#666; cursor:pointer; background:-webkit-gradient(linear, 0 100%, 0 0, from(#E2E2E2), to(#ffffff)); background:-moz-linear-gradient(top, #ffffff, #E2E2E2); filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E2E2E2');
-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E2E2E2')"; }
.inad li { height:60px; padding-top:12px; }
.inad li span { display:block; width:32px; height:32px; background:url(<?php echo $tag['path.skin']?>res/images/adin.jpg); float:left; margin-right:15px; }
.inad li .in2 { background-position:0 -32px; }
.inad li .in3 { background-position:0 -64px; }
.inad li a { color:#3865B8; text-decoration:underline; font-weight:bold; line-height:20px; }
.inad li p { color:#999; }
.inad li p b { color:#FF3300; margin:0 4px; }
</style>
<?php global $user; ?>
<div class="admin_index">
  <div class="admin_til"><a href="<?php echo sys_href($params['id'],'user','logout')?>">退出登录</a>
    <h3><?php echo $user->username; ?>，<?php echo sayHello();?></h3>
  </div>
  <p class="admin_wei">信息统计：我的留言 <?php echo info_num('message')?> 条， 我的评论 <?php echo info_num('comment')?> 条. 最后一次登录时间：<?php echo date('Y-m-d H:i:s',$user->lastlogin); ?></p>
  <div class="admin_menu">
    <ul>
      <li><a href="<?php echo sys_href($params['id'],'user')?>">中心首页</a></li>
      <li><a href="<?php echo sys_href($params['id'],'user','guestbook')?>">留言管理</a></li>
      <li><a href="<?php echo sys_href($params['id'],'user','comment')?>">评论管理</a></li>
      <li><a href="<?php echo sys_href($params['id'],'user','order')?>">订单管理</a></li>
      <li><a href="<?php echo sys_href($params['id'],'user','edit')?>">基本资料</a></li>
      <li class="hover"><a href="<?php echo sys_href($params['id'],'user','editpwd')?>">修改密码</a></li>
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
      <form name="form1" method="post" action="<?php echo sys_href($params['id'],'user','editpwd')?>" >
        <table border="0" id="tbguest">
          <tr>
            <td>用户名：</td>
            <td><?php echo $user->username; ?></td>
          </tr>
          <tr>
            <td width="70">旧密码</td>
            <td><input name="pwd" id="pwd" type="password" value="" onMouseOver="onMOver(this)" onMouseOut="onMOout(this)"/></td>
          </tr>
          <tr>
            <td width="70">新密码</td>
            <td><input name="newpass" id="newpass" type="password" value="" onMouseOver="onMOver(this)" onMouseOut="onMOout(this)"/></td>
          </tr>
          <tr>
            <td width="70">重复</td>
            <td ><input name="repwd"  id="repwd" type="password" value="" onMouseOver="onMOver(this)" onMouseOut="onMOout(this)"/></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td colspan="2"><input type="submit" name="submit" value="保存" onMouseOver="this.style.borderColor='#9ecc00'" onMouseOut="this.style.borderColor='#D2D9D8'"/></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
function onMOver(obj){
	obj.style.borderColor='#9ecc00';
}
function onMOout(obj){
	obj.style.borderColor='#D2D9D8';
}
</script>