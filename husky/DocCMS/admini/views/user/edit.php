<script language="JavaScript" type="text/javascript">

function letsok()
{
	if(document.getElementById('username').value=="")
	{
		alert('用户名不能为空！');
		return;
	}
	if(document.getElementById('email').value=="")
	{
		alert('邮箱不能为空！');
		return;
	}
		document.getElementById('form1').submit();
	
}
</script>
<form id="form1" name="form1" method="post" action="./index.php?p=<?php echo $request['p']; ?>&a=edit&cid=<?php echo $user->id?>">
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
  <tr class="adtbtitle">
    <td width="892"><h3>编辑会员：</h3><a href="javascript:history.back(1)" class="creatbt">返回</a> </td>
    <td width="72"><div align="right">
      <input name="button" type="button" onclick="letsok()" value="保存 " class="savebt"/>
    </div></td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
	  <table width="96%" border="0" align="center" cellpadding="0" cellspacing="4">
        <tr>
          <td width="70">用户名</td>
          <td width="861"><input name="username" class="txt" type="text" id="username" readonly value="<?php echo $user->username?>" onchange="xajax_validateUsername(this.value);" /> <font color="Red">*</font></td>
        </tr>
        <tr>
          <td width="70">密码</td>
          <td width="861"><input name="pwd" class="txt" type="text" id="pwd" /> <img border="0"  src="./images/light.gif" title="(不修改，则留空.请输入20位以内的字母或数字)"></td>
        </tr>
        <tr>
          <td width="70">昵称</td>
          <td width="861"><input name="nickname" class="txt" type="text" id="nickname" value="<?php echo $user->nickname?>" /></td>
        </tr>		
        <tr>
          <td width="70">权限</td>
          <td width="861">
		  <?php  $user1=new user();$user1->user_power_list_select('role',$user->role,false); $user1=null;?>
		  </td>
        </tr>
         <tr>
          <td width="10%">审核</td>
          <td >
           <select name="auditing" id="auditing">    	
           		<option value="0" <?php echo !$user->auditing?'selected="selected"':''; ?> >取消</option>
		    	<option value="1" <?php echo $user->auditing?'selected="selected"':''; ?>>审核</option>
			</select>	
          </td>
        </tr>
        <tr>
          <td width="10%">姓名</td>
          <td ><input name="name" value="<?php echo $user->name?>" class="txt" type="text" id="name" /></td>
        </tr>
        <tr>
          <td width="10%">性别</td>
          <td >
          <select name="sex">
			    <option value="1" <?php if($user->sex=='1') echo 'selected'; ?> >男</option>
			    <option value="2" <?php if($user->sex=='2') echo 'selected'; ?> >女</option>
			</select>*
         </td>
        </tr>
        <tr>
          <td width="70">QQ</td>
          <td width="861"><input name="qq" value="<?php echo $user->qq?>" class="txt" type="text" id="qq" /></td>
        </tr>
        <tr><td width="70">MSN  </td><td width="861"><input name="msn" value="<?php echo $user->msn?>" class="txt" type="text" id="msn" /></td></tr>
        <tr>
          <td width="70">Email</td>
          <td width="861"><input name="email" class="txt" type="text" id="email" value="<?php echo $user->email?>" onchange="isEmail(this.value);"/> <font color="Red">*</font><span id="v_email"></span></td>
        </tr>		
        <tr>
          <td width="70">手机</td>
          <td width="861"><input name="mtel" value="<?php echo $user->mtel?>" class="txt" type="text" id="mtel" /></td>
        </tr>	
        <tr>
          <td width="70">地址</td>
          <td width="861"><input name="address" value="<?php echo $user->address?>" class="txt" type="text" id="address" /></td>
        </tr>	
      </table>	
	  </td>
  </tr>
</table>
</form>