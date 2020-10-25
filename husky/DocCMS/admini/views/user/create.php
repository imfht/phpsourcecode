<script language="JavaScript" type="text/javascript">
function letsok()
{
	if(document.getElementById('username').value=="")
	{
		alert('用户名不能为空！');
		return;
	}
	if(document.getElementById('pwd').value=="")
	{
		alert('密码不能为空！');
		return;
	}
	if(document.getElementById('email').value=="")
	{
		alert('邮箱不能为空！');
		return;
	}

	if(document.getElementById('pwd').value==document.getElementById('repwd').value)
	{
		document.getElementById('form1').submit();
	}
	else
	{
		alert('两次密码不相符！');
		return;
	}
}
</script>
<script type="text/javascript" src="./views/user/js/create_action_1.0.0.js"></script>
<script>
var gpurl='./index.php?p=<?php echo $request['p']; ?>';
$(document).ready(function(){
	$("#judgeUserName").click(function(){//检测用户名
		//var options={"url":"","action":"action_server","parameters":"id1-id2-id3","messbox":{"errorFlag":true,"":""}}
		judgeUserName({"url":gpurl,"AjaxAction":"judgeUserName","parameters":"username","messbox":{"tagsId":"usernamemess"}});
	});
});
</script>
 <form id="form1" name="form1" method="post" action="./index.php?p=<?php echo $request['p']; ?>&a=create">
 <table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
  <tr class="adtbtitle">
    <td width="892"><h3>添加会员：</h3><a href="javascript:history.back(1)" class="creatbt">返回</a> </td>
    <td width="72"><div align="right">
      <input name="button" type="button" onclick="letsok()" value="保存" class="savebt"/>
    </div></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
	  <table width="96%" border="0" align="center" cellpadding="0" cellspacing="4">
        <tr>
          <td width="70">用户名</td>
          <td ><input name="username" class="txt" type="text" id="username" /> <font color="Red" id="usernamemess">* </font><input type="button" id="judgeUserName"  name="judgeUserName" value="检测用户名" /></td>
        </tr>
        <tr>
          <td width="70">密码</td>
          <td width="861"><input name="pwd" class="txt" type="password" id="pwd" /> *<img border="0"  src="./images/light.gif" title="请输入20位以内的字母或数字"></td>
        </tr>	
        <tr>
          <td width="70">重复密码</td>
          <td width="861"><input name="repwd" class="txt" type="password" id="repwd" /> <font color="Red">*</font></td>
        </tr>	
        <tr>
          <td width="70">昵称</td>
          <td width="861"><input name="nickname" class="txt" type="text" id="nickname" /></td>
        </tr>	
        <tr>
          <td width="70">权限</td>
          <td width="861">
		  <?php  $user=new user();$user->user_power_list_select('role','1',false); $user=null;?>
		  </td>
        </tr>
        <tr>
          <td width="10%">审核</td>
          <td >
           <select name="auditing" id="auditing">    	
           		<option value="0">取消</option>
		    	<option value="1"  selected>审核</option>
			</select>	
          </td>
        </tr>
        <tr>
          <td width="10%">姓名</td>
          <td ><input name="name" value="" class="txt" type="text" id="name" /></td>
        </tr>
        <tr>
          <td width="10%">性别</td>
          <td > <select name="sex"><option value="1">男</option><option value="2">女</option></select>*</td>
        </tr>
        <tr><td width="70">QQ  </td><td width="861"><input name="qq" class="txt" type="text" id="qq" /></td></tr>
        <tr><td width="70">MSN  </td><td width="861"><input name="msn" class="txt" type="text" id="msn" /></td></tr>
        <tr>
          <td width="70">Email</td>
          <td width="861"><input name="email" class="txt" type="text" id="email" /> <font color="Red">*</font><span id="v_email"></span></td>
        </tr>	
        <tr><td width="70">手机</td><td width="861"><input name="mtel" class="txt" type="text" id="mtel" /></td></tr>
        <tr><td width="70">地址</td><td width="861"><input name="address" class="txt" type="text" id="address" /></td></tr>
      </table>
	  </td>
  </tr>
</table>
  </form>