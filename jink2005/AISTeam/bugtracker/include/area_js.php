<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: area_js.php,v 1.7 2013/07/07 21:29:55 alex Exp $
 *
 */

// 檔案說明：這個檔案是用來寫出讓使用者在輸入或修改 report 時
//	     改變 Area 下拉選單時就自動改變 Minor 下拉選單的 JAVA Script
//           在要使用 Area 及 Minor Area 的 form 中，請設定 form name="form1"
//           另外還要設 Area 的下拉選單 <select size="1" name="area" onChange="AreaChange()">，
//           Minor Area 的下拉選單 <select size="1" name="minor_area" onChange="UpdateAssignTo()">
//           一開始請自行輸入所有 Area 的資料，及第一個 Area 的所有 Minor Area 的資料
//           order by area_id
if (!$_GET['project_id']) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}
if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'uid']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}
?>

<script language="JavaScript" type="text/javascript">						     
var AreaArray = new Array(
	{name: '', assign_to: 0, childs: new Array()}
	
<?php
$JS_all_area = "select * from ".$GLOBALS['BR_proj_area_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id'])." and area_parent=0 order by area_name";
$JS_root_area_result = $GLOBALS['connection']->Execute($JS_all_area) or DBError(__FILE__.":".__LINE__);
$JS_area_line = $JS_root_area_result->Recordcount();
while ($row = $JS_root_area_result->FetchRow()) {
   $JS_area_id = $row["area_id"];
   $JS_area_name = $row["area_name"];
   $JS_owner = $row["owner"];
   if ($JS_owner =="") {$JS_owner=0;}
   
   echo ",{ name: '".addslashes($JS_area_name)."', assign_to: $JS_owner, childs: new Array(";
   
   $JS_minor_area = "select * from ".$GLOBALS['BR_proj_area_table']." where project_id=".$GLOBALS['connection']->QMagic($_GET['project_id'])." and area_parent=".$GLOBALS['connection']->QMagic($JS_area_id)." order by area_name";
   $JS_minor_result = $GLOBALS['connection']->Execute($JS_minor_area) or DBError(__FILE__.":".__LINE__);
   $JS_minor_line = $JS_minor_result->Recordcount();
   
   $i = 0;
   while($JS_minor_row = $JS_minor_result->FetchRow()){
      $JS_minor_name = $JS_minor_row["area_name"];
      $JS_minor_owner = $JS_minor_row["owner"];
      
      if($i != 0) {
	      echo ",";
      }
      echo "{name: '".addslashes($JS_minor_name)."', assign_to: $JS_minor_owner}";
      $i++;
   }
   echo ")}";
}

?>	
	);

function UpdateAssignTo()
{
	var assign_to=0;
	
	assign_to = AreaArray[document.form1.area.selectedIndex].assign_to;
	if(AreaArray[document.form1.area.selectedIndex].childs.length>0) {
		assign_to = AreaArray[document.form1.area.selectedIndex].childs[document.form1.minor_area.selectedIndex].assign_to;
	}
	if (!document.form1.orig_assign_to || (document.form1.orig_assign_to.value == -1)) {
		for (var i=0;i<document.form1.assign_to.length;i++) {
			if (document.form1.assign_to.options[i].value==assign_to){
				document.form1.assign_to.options[i].selected=true;
				break;
			}
		}
	}
}
function AreaChange()
{
	document.form1.minor_area.options.length = AreaArray[document.form1.area.selectedIndex].childs.length ;

	for(var i=0 ; i< AreaArray[document.form1.area.selectedIndex].childs.length ; i++) {
		document.form1.minor_area.options[i].text = AreaArray[document.form1.area.selectedIndex].childs[i].name ;
	}
	UpdateAssignTo();
}
</script>													 
