<?php
/* Copyright c 2003-2004 Wang, Chun-Pin All rights reserved.
 *
 * Version:	$Id: faq_show.php,v 1.8 2013/07/07 21:25:44 alex Exp $
 *
 */
include("include/header.php");
include("include/project_function.php");

AuthCheckAndLogin();

if (!isset($_GET['project_id']) || ($_GET['project_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "project_id");
}

if (CheckProjectAccessable($_GET['project_id'], $_SESSION[SESSION_PREFIX.'feedback_customer']) == FALSE) {
	WriteSyslog("warn", "syslog_permission_denied", "", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

if (!isset($_GET['faq_id']) || ($_GET['faq_id'] == "")) {
	WriteSyslog("error", "syslog_miss_arg", "", __FILE__.":".__LINE__);
	ErrorPrintOut("miss_parameter", "faq");
}

// Get FAQ Content
$sql = "select * from ".$GLOBALS['BR_faq_content_table']."  where faq_id=".$GLOBALS['connection']->QMagic($_GET['faq_id'])." and is_verified='t'";
$result = $GLOBALS['connection']->Execute($sql) or DBError(__FILE__.":".__LINE__);
$line = $result->Recordcount();
if ($line != 1) {
	ErrorPrintOut("no_such_xxx", "faq");
}

$project_id = $result->fields["project_id"];
$question = $result->fields["question"];
$answer = $result->fields["answer"];

// Get project data
$project_sql = "select * from ".$GLOBALS['BR_project_table']." where project_id=".$GLOBALS['connection']->QMagic($project_id);
$project_result = $GLOBALS['connection']->Execute($project_sql) or DBError(__FILE__.":".__LINE__);
$project_line = $project_result->Recordcount();
if ($project_line == 1) {
	$project_name = $project_result->fields["project_name"];
}else{
	WriteSyslog("error", "syslog_not_found", "project", __FILE__.":".__LINE__);
	ErrorPrintOut("no_such_xxx", "project");
}

$extra_params = GetExtraParams($_GET, "search_key,faq_class,page");

?>
<div id="current_location">
	<b><?php echo $STRING['current_location'].$STRING['colon']?></b> / 
	<a href="faq.php?project_id=<?php echo $project_id.$extra_params?>"><?php echo $project_name?> <?php echo $STRING['faq']?></a> /
	<?php echo $STRING['faq']?>
</div>
<div id="main_container">
	<table width="100%" border="0">
		<tr>
			<td nowrap align="left">
				<img src="images/outline_faq.png" width="48" height="48" align="middle" border="0">
				<tt class="outline"><?php echo $STRING['faq']?></tt>
			</td>
			<td nowrap width="100%" align="right" valign="bottom">
				<a href="faq.php?project_id=<?php echo $project_id.$extra_params?>"><img src="images/return.png" border="0" align="middle"><?php echo $STRING['back']?></a>
			</td>
		</tr>
	</table>

	<div id="sub_container" style="width: 98%;">
		<div class="aw-box-tl"><div class="aw-box-tr"><div class="aw-box-tc"></div></div></div>
		<div class="aw-box-ml"><div class="aw-box-mr"><div class="aw-box-mc">

			<div class="item_prompt_large"><?php echo $question?></div>
			<p>
<?php
echo $STRING['faq_class'].$STRING['colon'];
$old_class_sql = "select * from ".$GLOBALS['BR_faq_map_table']." where faq_id=".$GLOBALS['connection']->QMagic($_GET['faq_id']);
$old_class_result = $GLOBALS['connection']->Execute($old_class_sql) or DBError(__FILE__.":".__LINE__);
$old_class_id = array();
while ($row = $old_class_result->FetchRow()) {
	$faq_class_id = $row["faq_class_id"];
	array_push($old_class_id, $faq_class_id);
}

$get_class_sql = "select * from ".$GLOBALS['BR_faq_class_table']." where project_id=".$GLOBALS['connection']->QMagic($project_id);
$get_class_result = $GLOBALS['connection']->Execute($get_class_sql) or DBError(__FILE__.":".__LINE__);

$all_class_id = array();
$all_class_name = array();
while ($row = $get_class_result->FetchRow()) {
	$faq_class_id = $row["faq_class_id"];
	$class_name = $row["class_name"];
	array_push($all_class_id, $faq_class_id);
	array_push($all_class_name, $class_name);
}

// List old FAQ category
for ($i = 0; $i < sizeof($old_class_id); $i++) {
	$faq_class_id = $old_class_id[$i];

	$class_name = "";
	for ($j = 0; $j < sizeof($all_class_id); $j++) {
		if ($all_class_id[$j] == $faq_class_id) {
			$class_name = $all_class_name[$j];
			break;
		}
	}
	if ($class_name == "") {
		continue;
	}

	echo "$class_name, ";
}
?>

			</p>
			<table width="95%">
			<tr><td><?php echo $answer?></td></tr>
		</table>
		</div></div></div>
		<div class="aw-box-bl"><div class="aw-box-br"><div class="aw-box-bc"></div></div></div>
	</div>
</div>

<?php
include("include/tail.php");
?>