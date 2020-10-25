<?php
/* Copyright 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: index.php,v 1.13 2013/07/07 21:25:44 alex Exp $
 *
 */
include("include/header.php");
include("include/datetime_function.php");

AuthCheckAndLogin();

$today=date("md");
if ( ($today > 1215) && ($today < 1231) ) {

?>
<script language="JavaScript" src="javascript/snow.js" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
SnowShow.Show();
</script>
<?php
} // End of X'mas
?>

<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td width="100%" align="left">
				<img src="images/outline_project.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['title_project_list']?></tt>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">
			<h3>&nbsp;</h3>
			<table class="table-main-list" align="center">
			<tr>
				<td class="title" width="10">&nbsp;</td>
				<td class="title" width="360"><p align="left"><?php echo $STRING['project_name']?></td>
				<td class="title" width="120" align="center"><?php echo $STRING['created_date']?></td>
				<td class="title" width="10">&nbsp;</td>
			</tr>
	
<?php

	$sql = "select * from ".$GLOBALS['BR_project_table']." order by project_name";
	$sql_result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
	// ��X�i�H��ܪ��Q�װ�
	$count=0;
	while ($row = $sql_result->FetchRow()){
		$project_id = $row['project_id'];
		$visible_sql="select * from ".$GLOBALS['BR_proj_customer_access_table']." where customer_id=".$GLOBALS['connection']->QMagic($_SESSION[SESSION_PREFIX.'feedback_customer'])." and 
			project_id=".$GLOBALS['connection']->QMagic($project_id);
		$visible_result = $GLOBALS['connection']->Execute($visible_sql) or DBError(__FILE__.":".__LINE__);
		$line = $visible_result->Recordcount();
      
		// �p�G�ӵ{�������\�M�椤���ӫȤ�
		if ($line != 0) {
			$project_name = $row["project_name"];
			$created_date = $sql_result->UserTimeStamp($row["created_date"], GetDateFormat());

			echo '
			<tr>
				<td class="line'.($count%2).'">
					<img border="0" src="images/triangle_s.gif" width="8" height="9">
				</td>
				<td class="line'.($count%2).'">
					<a href="project_list.php?project_id='.$project_id.'">'.htmlspecialchars($project_name).'</a>
				</td>
				<td class="line'.($count%2).'" align="center">'.$created_date.'</td>
				<td valign="top" class="line'.($count%2).'">&nbsp;</td>
			</tr>';
			$count++;
		}//end if
    
	}// end of while


?>
			</table>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>

<?php
include("include/tail.php");
?>
