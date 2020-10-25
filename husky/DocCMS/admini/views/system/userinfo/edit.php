<script language="JavaScript" type="text/javascript" src="../inc/js/jquery.imgareaselect.pack.js"></script>
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
<script type="text/javascript">

$(document).ready(function(){
	var n = $('#ferret').width();
	$('#ferret').width()>=600?$('#ferret').width('600'):'';
	if(n>=600)
	var b=n/600;
	else
	var b=1;
	$('input[name="x1"]').val(0);
	$('input[name="y1"]').val(0);
	$('input[name="x2"]').val(88);
	$('input[name="y2"]').val(88);
	$('#ferret').imgAreaSelect({
		x1: 0,
		y1: 0,
		x2: 88,
		y2: 88,
		minHeight:88,
		minWidth :88,
		aspectRatio: '1:1',
		onSelectChange: preview,
		onSelectEnd: function (img, selection) {
			$('input[name="x1"]').val((selection.x1)*b);
			$('input[name="y1"]').val((selection.y1)*b);
			$('input[name="x2"]').val((selection.x2)*b);
			$('input[name="y2"]').val((selection.y2)*b);	
		}
	});
});
var preview=function(img, selection) {
    var scaleX = 400 / (selection.width || 1);
    var scaleY = 240 / (selection.height || 1);  
    $('#ferret + div > img').css({
        width: Math.round(scaleX * 400) + 'px',
        height: Math.round(scaleY * 240) + 'px',
        marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
        marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
    });
}
</script>

<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="./index.php?m=system&s=userinfo">用户管理</a> → <a href="./index.php?m=system&s=userinfo&a=edit&cid=<?php echo $request['cid']?>">修改用户信息</a></div>
<form id="form1" name="form1" method="post" action="./index.php?m=system&s=userinfo&a=edit&cid=<?php echo $user->id?>" enctype="multipart/form-data">
  <table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb">
    <tr class="adtbtitle">
      <td width="892"><h3>修改管理者档案</h3>
        <a href="javascript:history.back(1)" class="creatbt">返回</a></td>
      <td width="72"><div align="right">
          <input name="button" type="button" onclick="letsok()" value="保存 " class="savebt" />
        </div></td>
    </tr>
    <tr>
      <td colspan="2" bgcolor="#FFFFFF"><table width="96%" border="0" align="center" cellpadding="0" cellspacing="4">
          <tr>
            <td width="70">用户头像</td>
            <td ><img src="<?php echo ispic(ispic($user->cropPic,$user->smallPic))?>" width="88" height="88"><br />
              <br />
              <input name="image" id="image" type="text" class="txt" value="<?php echo $user->originalPic ?>" size="60">
              <input name="uploadfile" id="uploadfile" type="file" size="50" maxlength= "50" style="display: none;">
              <input type="button" name="bt2" id="bt2" value="本地上传" class="bluebutton" onclick="document.getElementById('image').disabled=true;document.getElementById('uploadfile').disabled=false;document.getElementById('uploadfile').style.display='block';document.getElementById('image').style.display='none';document.getElementById('bt2').style.display='none';">
              <span class="picture">当您重新从本地上传图片时，原来的图片将被删除。</span></td>
            <td rowspan="13" align="left"><img src="<?php echo ispic($user->originalPic)?>?<?php echo rand(0,100)?>" id="ferret" >
            <br>
            <input type="hidden" name="x1" value="0" />
            <input type="hidden" name="y1" value="0" />
            <input type="hidden" name="x2" value="0" />
            <input type="hidden" name="y2" value="0" />
            <input type="hidden" name="url" value="<?php echo $user->originalPic ?>" />
            <input type="button" name="sub" onclick="letsok()" value="保 存 截 图" class="savebt"/>
            </td>
          </tr>
          <tr>
            <td width="70">用户名</td>
            <td width="560"><input name="username" class="txt" type="text" id="username" readonly value="<?php echo $user->username?>" onchange="xajax_validateUsername(this.value);" />
              <font color="Red">*</font></td>
          </tr>
          <tr>
            <td width="70">密码</td>
            <td width="560"><input name="pwd" class="txt" type="text" id="pwd" />
              (不修改，则留空.请输入20位以内的字母或数字)<span id="pwd_info"></span></td>
          </tr>
          <tr>
            <td width="70">昵称</td>
            <td width="560"><input name="nickname" class="txt" type="text" id="nickname" value="<?php echo $user->nickname?>" /></td>
          </tr>
          <tr>
            <td width="70">权限</td>
            <td width="560"><?php $userRights=new userRights();echo $userRights->user_power_list_select_edit('role',intval($user->role),$user->id,$_SESSION[TB_PREFIX.'admin_roleId'],$_SESSION[TB_PREFIX.'admin_userID'])?></td>
          </tr>
          <tr>
            <td width="10%">审核</td>
            <td ><select name="auditing" id="auditing">
                <option value="0" <?php echo !$user->auditing?'selected="selected"':''; ?> >取消</option>
                <option value="1" <?php echo $user->auditing?'selected="selected"':''; ?>>审核</option>
              </select></td>
          </tr>
          <tr>
            <td width="10%">姓名</td>
            <td ><input name="name" value="<?php echo $user->name?>" class="txt" type="text" id="name" /></td>
          </tr>
          <tr>
            <td width="10%">性别</td>
            <td ><select name="sex">
                <option value="1" <?php if($user->sex=='1') echo 'selected'; ?> >男</option>
                <option value="2" <?php if($user->sex=='2') echo 'selected'; ?> >女</option>
              </select>
              * </td>
          </tr>
          <tr>
            <td width="70">QQ</td>
            <td width="560"><input name="qq" value="<?php echo $user->qq?>" class="txt" type="text" id="qq" /></td>
          </tr>
          <tr>
            <td width="70">MSN </td>
            <td width="560"><input name="msn" value="<?php echo $user->msn?>" class="txt" type="text" id="msn" /></td>
          </tr>
          <tr>
            <td width="70">Email</td>
            <td width="560"><input name="email" class="txt" type="text" id="email" value="<?php echo $user->email?>" onchange="isEmail(this.value);"/>
              <font color="Red">*</font><span id="v_email"></span></td>
          </tr>
          <tr>
            <td width="70">手机</td>
            <td width="560"><input name="mtel" value="<?php echo $user->mtel?>" class="txt" type="text" id="mtel" /></td>
          </tr>
          <tr>
            <td width="70">地址</td>
            <td width="560"><input name="address" value="<?php echo $user->address?>" class="txt" type="text" id="address" /></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
