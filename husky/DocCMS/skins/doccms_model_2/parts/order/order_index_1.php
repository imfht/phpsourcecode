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
<style type="text/css">
*{ padding:0; margin:0;}
img{ border:none;}
a{ text-decoration:none;}
.useript{ background-color: white; border-color: #CCCCCC #E2E2E2 #E2E2E2 #CCCCCC; border-style: solid; border-width: 1px;  box-shadow: 1px 2px 3px #F0F0F0 inset; overflow: hidden; padding: 10px 0 8px 8px; vertical-align: middle;}
.usertel{ width:180px; margin-right:20px;}
.userbtn{ padding:0.2em 0.8em; font-family:"微软雅黑"; font-size:20px; border:none; float:left; cursor:pointer;}
.usersbmt{ background:url(<?php echo $tag['path.skin']; ?>res/images/logbg.jpg) no-repeat; color:#fff; float:left; margin-top:15px;}
.guestinfo{ width:70%; height:80px; margin-bottom:15px; float:left;}
#tbguest td{ line-height:40px; padding-bottom:10px;}
</style>
<script language="javascript">
function validator()
{
 	if(document.getElementById('title').value=="")
	{alert("请填写您的公司名称!"); document.getElementById('title').focus(); return false;}
}
</script>
<br />
<div id="stuffbox">
<form id="form1" name="form1" method="post" action="<?php echo sys_href($params['id'],'form_action')?>" onsubmit="return validator()">
  <table width="95%" border="0" id="tbguest">
	<tr>
      <td width="100">公司名称：</td><td><input name="title" type="text" id="title" class="useript usertel" /><span style="color:#f00;">*(* 选项为必填)</span></td>
    </tr>
    <!-------------------可选参数------------------------->
    <?php echo sys_push('','<tr>
		<td>{name}：</td>
		<td><input name="custom[]" type="text" class="useript usertel" value=""></td></tr>')?>
    <!--------------------------------------------------------->
    <tr>
      <td>备注：</td><td><textarea name="remark" rows="3" id="remark" class="useript guestinfo"></textarea></td>
    </tr>
    <tr>
      <td>&nbsp;</td><td><label><input type="submit" name="Submit" value="提交" class="userbtn usersbmt" /></label></td>
    </tr>
  </table>
  </form>
</div>