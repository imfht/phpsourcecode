<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb"> 
  <tr class="adtbtitle">
    <td><h3>会员管理：</h3><a href="?p=<?php echo $request['p'];?>&a=create" class="creatbt">添加会员</a></td>
    <td width="91">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF"><?php echo $users->render(); ?></td>
  </tr>
</table>