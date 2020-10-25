<script language="javascript" type="text/javascript" src="../inc/js/window_custom.js"></script>
<script> 
function send(ev)
 {
    var objPos = mousePosition(ev);
    messContent = "<div class='setbox'><form action='index.php?p=<?php echo $request['p'] ?>&a=setModels' method='post'><div class='setleftod'><div id='setfield'><?php sys_push('',"<label>属性名称：</label><input name='fields[]' value='{name}' type='text' class='txttc'>")?></div><p class='addline'><a href='javascript:createField();'>添加自定义属性</a> </p></div><p class='saveline'><input type='submit' value=' 保存设置 '  class='savest'></p></form></div>";
    showMessageBox('留言自定义字段设置', messContent, objPos, 350);

}
</script>
<style type="text/css">
.txttc{width:83%; height:20px; padding:3px 0 3px 10px; border:1px solid #ddd; color:#666;}
</style>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb"> 
  <tr class="adtbtitle">
    <td><h3>留言管理：</h3> <a href="javascript:send(0)" class="button orange">设置留言自定义字段</a></td>
    <td width="91">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
	  <?php
		if(isset($sb->results))
		{
			foreach ($sb->results as $o)
			{			
				?>
				<table style="border: 2px solid #ccc;<?php if ($count%2 ==0) {?> background-color:#C5EAF5;<?php }?>"  width="100%" border="0" cellpadding="2" cellspacing="2" bgcolor="#FFFFCC">
				<tr>
				<td style="width:130px;">第<strong><font color="#0000FF"><?php echo $count ?></font></strong>条留言</td>
				<td style="width:130px;"><strong>留言者：</strong><?php echo $o['name'] ?></td>
				<td style="width:210px;"><strong>联系方式：</strong><?php echo $o['contact'] ?></td>
				<td style="width:230px;"><strong>留言时间:</strong><?php echo $o['dtTime'] ?></td>
				<td style="width:170px;"><strong>IP：</strong><?php echo $o['ip'] ?></td>
				<td><input type="button" name="bt22" value="回复留言" onclick="window.location='./index.php?a=edit&amp;p=<?php echo $request['p'] ?>&amp;n=<?php echo $o['id'] ?>&amp;i=<?php echo $count ?>'" /></td>
				<td><input type="button" name="bt32" value="删除" onclick="window.location='./index.php?a=destroy&amp;p=<?php echo $request['p'] ?>&amp;n=<?php echo $o['id'] ?>'" /></td>
				</tr>
				<tr>
                  <?php sys_push($o['custom'],'<td><strong>{name}：</strong>{value}</td>')?>
				</tr>
				<tr>
				  <td style="text-align:center;height:25px;<?php if ($count%2 ==0) {?> background-color:#D0FDF4;<?php } else {?> background-color:#E3E3E3;<?php }?>"><strong>留言内容</strong></td>
				  <td colspan="6" style="width:820px;<?php if ($count%2 ==0) {?> background-color:#D0FDF4;<?php } else {?> background-color:#E3E3E3;<?php }?>" colspan="4"><?php echo $o['content'];?></td>
				</tr>
				<tr>
				  <td style="text-align:center; background-color:#D9D5B3; height:40px;"><strong>回复内容</strong></td>
				  <td colspan="5" style="width:820px;"><?php
    			if($o['content'] != "")
    			{
    				?>
                    <div style="color: #4976A7;"><?php echo $o['content1'] ?></div>
                    <?php
    			}
    			?></td>
				<td><?php
    			if($o['auditing'] == "1")
    			{
    				?>
                  <font color="Green">已审核</font>
                  <?php
    			}
    			else
    			{
    				?>
                  <font color="Red">未审核</font>
                  <?php
    			}
    			?></td>
				</tr>
	  </table>					
				<?php
				$count--;
			}
		}
		else
		{
			?>
			<table><td>暂时还没有留言!</td></table>		
			<?php
		}
		?>
		<table><tr><td><?php echo $sb->get_pager_show() ?></td></tr></table>
    </td>
  </tr>
</table>