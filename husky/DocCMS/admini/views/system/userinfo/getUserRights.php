<script type="text/javascript">
$(document).ready(function(){
	$("#tree li").mouseover(function (){
		$(this).css({background:"#FFFFCC"});//efefef
	});
	$("#tree li").mouseout(function (){
		$(this).css({background:"none"});
	});
});
</script>
<style>
<!--
#table{width:100%; border:1px solid #ccc;}
ul#tree{width:100%; }
ul#tree li{width:95%; clear:both; height:24px; line-height:24px; border-bottom:1px dashed #ccc;}
ul#tree li span{ display:block;float:left;}
ul#tree li span a{ }
ul#tree li ul{ display:none;}
ul#tree li ul li{ }
.tree{ width:50%;  }
.tree .prefix{ }
.tree .title{ background:url(../inc/img/tree/tree_file.gif) no-repeat 0 50%; padding-left:16px;}
.menuname{ width:20%; }
.type{ width:10%; }

.mod{ width:15%;}
.check{ width:15%; margin-top:3px;}
.check input{ }

-->
</style>
<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="./index.php?m=system&s=userinfo">用户管理</a> → 用户权限查看</div>
<table width="100%" border="0" cellpadding="2" cellspacing="1" bgcolor="#fff" id="table">
  <tr class="adtbtitle">
    <td width="100%"><h3>用户权限查看</h3> <a class="creatbt" href="javascript:history.back(1)">返回</a> </td>
  </tr>
  <tr>
    <td bgcolor="#efefef">
	  <table width="100%" height="60" border="0" align="center" cellpadding="0" cellspacing="0">
	  	 <tr bgcolor="#fff0f5">
          <td width="70">用户名</td>
          <td width="70">昵称</td>
          <td width="70">权限级别</td>
          <td width="70">姓名</td>
          <td width="70">性别</td>
          <td width="70">年龄</td>
          <td width="70">手机</td>
          <td width="70">地址</td>
           <td width="70">Email</td>
        </tr>
        <tr>       
          <td><?php echo $tmp['user']->username?></td>     
          <td><?php echo $tmp['user']->nickname?></td>
          <td>
          <?php $userRights=new userRights();echo $userRights->return_level_name(intval($tmp['user']->role));?>
          </td>
          <td><?php echo $tmp['user']->name?></td>
          <td><?php echo isSex($tmp['user']->sex); ?></td>
          <td><?php echo $tmp['user']->age?></td>
          <td><?php echo $tmp['user']->mtel?></td>
          <td><?php echo $tmp['user']->address?></td>
          <td><?php echo $tmp['user']->email?></td>
        </tr>		
      </table>	
	  </td>
  </tr>
  <tr>
    <td>
    <ul id="tree">
	<li><span class="tree">中文标题</span><span class="menuname">英文名</span><span class="type">模块类型</span><span  class="mod">编辑</span></li>
	<?php echo $tmp['menuinfo'];unset($tmp);?>
	</ul>
	</td>
  </tr> 
</table>