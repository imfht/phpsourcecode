<?php
include("init.php");
if (!isset($_SESSION["userid"]))
{
    $template->assign("loginerror", 0);
    $template->display("login.tpl");
    die();
}
$objworkpackage = (object) new tasklist();
$objmilestone = (object) new milestone();

$action = getArrayVal($_GET, "action");
$id = getArrayVal($_GET, "id");
$tlid = getArrayVal($_GET, "tlid");
$mode = getArrayVal($_GET, "mode");

$name = getArrayVal($_POST, "name");
$desc = getArrayVal($_POST, "desc");
$access = getArrayVal($_POST, "email");
$milestone = getArrayVal($_POST, "milestone");

$planeffort = getArrayVal($_POST, "planeffort");
$startdate = getArrayVal($_POST, "startdate");
$finishdate = getArrayVal($_POST, "finishdate");
$responsible = getArrayVal($_POST, "responsible");

$cloud = new tags();
$cloud->cloudlimit = 1;
$thecloud = $cloud->getTagcloud($id);
if (strlen($thecloud) > 0)
	$template->assign("cloud", $thecloud);

$project = array();
$project['ID'] = $id;
$classes = array("overview" => "overview",
    "msgs" => "msgs",
    "tasks" => "tasks_active",
    "miles" => "miles",
    "files" => "files",
    "users" => "users",
    "tracker" => "tracking"
    );
$template->assign("classes", $classes);
if (!chkproject($userid, $id))
{
    $errtxt = $langfile["notyourproject"];
    $noperm = $langfile["accessdenied"];
    $template->assign("errortext", "$errtxt<br>$noperm");
    $template->assign("mode", "error");
    $template->display("error.tpl");
    die();
}
$template->assign("mode", $mode);

if ($action == "addform")
{
    $milestones = $objmilestone->getAllProjectMilestones($id, 10000);
    $myproject = (object) new project();
    $pro = $myproject->getProject($project);
    
    $title = $langfile['addtasklist'];
    $template->assign("title", $title);

    $template->assign("milestones", $milestones);
    $template->assign("projectid", $project);
    $template->assign("pro", $pro);
    $template->display("addtasklist.tpl");
} elseif ($action == "add")
{
    if ($objworkpackage->add_workpackage($id, $name, $desc, 0, $milestone, $planeffort, $startdate, $finishdate, $responsible))
    {
        $loc = $url . "managetask.php?action=showproject&id=$id&mode=listadded";
        header("Location: $loc");
    }
    else
    {
        $template->assign("addworkpackage", 0);
    }
}
if ($action == "editform")
{
    if (!$userpermissions["tasks"]["edit"])
    {
        $errtxt = $langfile["nopermission"];
        $noperm = $langfile["accessdenied"];
        $template->assign("errortext", "<h2>$errtxt</h2><br>$noperm");
        $template->display("error.tpl");
        die();
    }
    $tasklist = $objworkpackage->getTasklist($tlid);
    $mile_id = $tasklist["milestone"];
    $m = $objmilestone->getMilestone($mile_id);
    $tasklist["milestonename"] = $m["name"];
    $milestones = $objmilestone->getAllProjectMilestones($id, 10000);
    $project = array();
    $project['ID'] = $id;

    $myproject = (object) new project();

    $pro = $myproject->getProject($id);
    $projectname = $pro["name"];
	
    $title = $langfile["edittasklist"];

    $template->assign("title", $title);
    $template->assign("projectname", $projectname);
    $template->assign("showhead", 1);
    $template->assign("milestones", $milestones);
    $template->assign("tasklist", $tasklist);
    $template->assign("project", $project);
    $template->assign("pro", $pro);
    $template->display("edittasklist.tpl");
} elseif ($action == "edit")
{
    if (!$userpermissions["tasks"]["edit"])
    {
        $errtxt = $langfile["nopermission"];
        $noperm = $langfile["accessdenied"];
        $template->assign("errortext", "<h2>$errtxt</h2><br>$noperm");
        $template->display("error.tpl");
        die();
    }

    if ($objworkpackage->edit_workpackage($tlid, $name, $desc, $milestone, $planeffort, $startdate, $finishdate, $responsible))
    {
        $loc = $url . "managetasklist.php?action=showtasklist&id=$id&tlid=$tlid&mode=edited";
        header("Location: $loc");
    }
    else
    {
        $template->assign("editworkpackage", 0);
    }
} elseif ($action == "del")
{
    if (!$userpermissions["tasks"]["del"])
    {
        $errtxt = $langfile["nopermission"];
        $noperm = $langfile["accessdenied"];
        $template->assign("errortext", "<h2>$errtxt</h2><br>$noperm");
        $template->display("error.tpl");
        die();
    }

    if ($objworkpackage->del_workpackage($tlid))
    {
        $loc = $url . "managetask.php?action=showproject&id=$id&mode=listdeleted";
        header("Location: $loc");
    }
    else
    {
        $template->assign("delworkpackage", 0);
    }
} elseif ($action == "close")
{
    if (!$userpermissions["tasks"]["close"])
    {
        $errtxt = $langfile["nopermission"];
        $noperm = $langfile["accessdenied"];
        $template->assign("errortext", "<h2>$errtxt</h2><br>$noperm");
        $template->display("error.tpl");
        die();
    }
    if ($objworkpackage->close_workpackage($tlid))
    {
        $loc = $url . "managetask.php?action=showproject&id=$id&mode=listclosed";
        header("Location: $loc");
    }
    else
    {
        $template->assign("closeworkpackage", 0);
    }
} elseif ($action == "open")
{
    if (!$userpermissions["tasks"]["edit"])
    {
        $errtxt = $langfile["nopermission"];
        $noperm = $langfile["accessdenied"];
        $template->assign("errortext", "<h2>$errtxt</h2><br>$noperm");
        $template->display("error.tpl");
        die();
    }
	 if ($objworkpackage->open_workpackage($tlid))
    {
        $loc = $url . "managetask.php?action=showproject&id=$id&mode=listopened";
        header("Location: $loc");
        // echo "ok";
    }
    else
    {
        $template->assign("openworkpackage", 0);
    }
} elseif ($action == "showtasklist")
{
    $myproject = (object) new project();
    $project_members = $myproject->getProjectMembers($id);

    $pro = $myproject->getProject($id);
    $projectname = $pro["name"];
    $template->assign("projectname", $projectname);

    $tasklist = $objworkpackage->getTasklist($tlid);
    $tasks = $objworkpackage->getTasksFromList($tlid);
    $tasklist["tasknum"] = count($tasks);
    $donetasks = $objworkpackage->getTasksFromList($tlid, 0);
    $tasklist["donetasknum"] = count($donetasks);

    $workpackage = $tasklist;
    $workpackage["finishdate"] = strtotime($workpackage["finishdate"]);
    $milestones = $objmilestone->getAllProjectMilestones($id, 10000);
    $template->assign("milestones", $milestones);
    
    $task = new task();
    $chart_data = $task->estimateChartData($task->estimateListWorkpackage($tlid), $objworkpackage->estimateListWorkpackage($tlid));
    $chart_data2 = $task->estimateChartData($task->estimateListWorkpackage2($tlid), $objworkpackage->estimateListWorkpackage($tlid,"planned"));
    
    $tasklist["editingenabled"] = strlen($tasklist['uuid']) == 0;
    $title = $langfile['tasklist'];
    $template->assign("title", $title);
    $template->assign("classes", $classes);
    $template->assign("tasklist", $tasklist);
    $template->assign("assignable_users", $project_members);
    $template->assign("tasks", $tasks);
    $template->assign("donetasks", $donetasks);
    $template->assign("project", $project);
    $template->assign("pro", $pro);
    $template->assign("workpackage", $workpackage);
    $template->assign("chart_data", $chart_data);
    $template->assign("chart_data2", $chart_data2);
    if (getArrayVal($_COOKIE, "chartwork"))
        $template->assign("chartworkstyle", "display:".$_COOKIE['chartwork']);
    if (getArrayVal($_COOKIE, "newtasks"))
        $template->assign("newtasksstyle", "display:".$_COOKIE['newtasks']);
    $template->display("tasklist.tpl");
}

?>