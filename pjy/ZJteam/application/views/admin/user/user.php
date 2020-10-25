<?php include 'application/views/admin/public/head.php'?>
<style type="text/css">
<!--
body {
	margin-left: 3px;
	margin-top: 0px;
	margin-right: 3px;
	margin-bottom: 0px;
}
.STYLE1 {
	color: #e1e2e3;
	font-size: 12px;
}
.STYLE6 {color: #000000; font-size: 12; }
.STYLE10 {color: #000000; font-size: 12px; }
.STYLE19 {
	color: #344b50;
	font-size: 12px;
}
.STYLE21 {
	font-size: 12px;
	color: #3b6375;
}
.STYLE22 {
	font-size: 12px;
	color: #295568;
}
-->
</style>
<script>
var  highlightcolor='#d5f4fe';
//此处clickcolor只能用win系统颜色代码才能成功,如果用#xxxxxx的代码就不行,还没搞清楚为什么:(
var  clickcolor='#51b2f6';
function  changeto(){
source=event.srcElement;
if  (source.tagName=="TR"||source.tagName=="TABLE")
return;
while(source.tagName!="TD")
source=source.parentElement;
source=source.parentElement;
cs  =  source.children;
//alert(cs.length);
if  (cs[1].style.backgroundColor!=highlightcolor&&source.id!="nc"&&cs[1].style.backgroundColor!=clickcolor)
for(i=0;i<cs.length;i++){
	cs[i].style.backgroundColor=highlightcolor;
}
}

function  changeback(){
if  (event.fromElement.contains(event.toElement)||source.contains(event.toElement)||source.id=="nc")
return
if  (event.toElement!=source&&cs[1].style.backgroundColor!=clickcolor)
//source.style.backgroundColor=originalcolor
for(i=0;i<cs.length;i++){
	cs[i].style.backgroundColor="";
}
}

function  clickto(){
source=event.srcElement;
if  (source.tagName=="TR"||source.tagName=="TABLE")
return;
while(source.tagName!="TD")
source=source.parentElement;
source=source.parentElement;
cs  =  source.children;
if  (cs[1].style.backgroundColor!=clickcolor&&source.id!="nc")
for(i=0;i<cs.length;i++){
	cs[i].style.backgroundColor=clickcolor;
}
else
for(i=0;i<cs.length;i++){
	cs[i].style.backgroundColor="";
}
}
</script>
</head>
<body>
  <tr>
  <form method="post" action=""/> 
    <td><table width="100%" border="0" class="table_list" cellpadding="0" cellspacing="1" bgcolor="#a8c7ce" onmouseover="changeto()"  onmouseout="changeback()">
    <caption>用户管理</caption>
      <tr>
        <td width="10%" height="25" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">邮箱</span></div></td>
        <td width="5%" height="25" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">真实姓名</span></div></td>
        <td width="5%" height="25" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">性别</span></div></td>
        <td width="20%" height="25" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">学校</span></div></td>
        <td width="10%" height="25" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">手机</span></div></td>
        <td width="10%" height="25" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">QQ</span></div></td>
        <td width="10%" height="25" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">注册时间</span></div></td>
        <td width="5%" height="25" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">参加活动</span></div></td>
        <td width="10%" height="25" bgcolor="d3eaef" class="STYLE6"><div align="center"><span class="STYLE10">管理</span></div></td>
      </tr>
      <tr>
        <?php foreach($userinfo as $userinfo):?>
        <td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center"><?php echo $userinfo['email'];?></div></td>
        <td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center"><?php echo $userinfo['truename'];?></div></td>
        <td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center"><?php echo $userinfo['sex'];?></div></td>
        <td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center"><?php echo $userinfo['school'];?></div></td>
        <td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center"><?php echo $userinfo['phone'];?></div></td>
        <td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center"><?php echo $userinfo['qq'];?></div></td>
        <td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center"><?php echo $userinfo['regtime'];?></div></td>
        <td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center">
		<?php 
			$uid = $userinfo['id'];
			$conn = mysql_connect("localhost","root",$this->db->password);
			mysql_select_db("zj");
			mysql_query("set names utf8");
			$query = mysql_query("select count(*) from baoming where uid='$uid'");
			$row = mysql_fetch_row($query);
			echo $row[0]."个";
			mysql_close($conn);
		?>
		</div></td>
        <td height="20" bgcolor="#FFFFFF" class="STYLE6"><div align="center">
        <a class="blues" href="<?php echo site_url('admin/activityLook/'.$userinfo['id']);?>">查看参加的活动</a> |
        <a class="blues" onclick="return confirm('确定要删除该申请吗?')" href="<?php echo site_url('admin/deluserinfo/'.$userinfo['id']);?>"> 删除</a></div>
        </div></td>
      </tr>
      <?php endforeach;?>
      <tr>
      <td height="20" colspan="14" align="left">
      <div style="float:right;margin-top:3px;"><div style="float:left"><?php echo "共".ceil($usernums)."个用户&nbsp;&nbsp;&nbsp;";?></div><?php echo $userpage?></div>
      </td>
      </tr>
      </form>
</body>
</html>
