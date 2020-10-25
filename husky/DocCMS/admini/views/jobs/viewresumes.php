<h2 class="title"><?php echo $pageInfo['submenuName'] ?></h2>
<table width="100%" border="0" cellpadding="2" cellspacing="1" class="admintb"> 
  <tr class="adtbtitle">
    <td><h3>招聘职位管理：</h3>
    <?php 
	if(!empty($jobs))
	{
		foreach ($jobs as $o)
		{
			?>
			<a href="?p=<?php echo $request['p'] ?>&j=<?php echo $o->id ?>&a=viewresumes" class="creatbt"><?php echo $o->title ?></a>
			<?php
		}
	}
	else 
	{
		echo '<span style="color:Red">暂无应聘者应聘任何职位!</span>';
	}
	?>
	<a href="javascript:history.back(1)" class="creatbt"><span>返回</span></a>
    </td>
    <td width="91">&nbsp;</td>
  </tr>
  <tr>
    <td colspan="2" bgcolor="#FFFFFF">
	  <?php echo $resumes->render() ?>
	</td>
  </tr>
</table>