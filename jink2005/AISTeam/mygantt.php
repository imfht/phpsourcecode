<?php
include("init.php");
if (!isset($_SESSION["userid"])) {
	$template->assign("loginerror", 0);
	$template->display("login.tpl");
	die();
}

$id = (int)getArrayVal($_GET, "id");

$project = new project();
if($id>0) {
	$project_data = $project->getProject($id);
	$project_title = ' - Project "'.$project_data["name"].'"';
	$template->assign("project", $project_data);
	
	$cloud = new tags();
	$cloud->cloudlimit = 1;
	$thecloud = $cloud->getTagcloud($id);
	if (strlen($thecloud) > 0)
		$template->assign("cloud", $thecloud);
}

$title = $langfile['mygantt'];
$template->assign("title", $title.$project_title);
$template->assign("gantt_data", $project->ganttInit($id));
$template->display("mygantt.tpl");

?>