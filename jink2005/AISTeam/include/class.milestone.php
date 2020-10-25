<?php
/**
 * This class provides methods to realize milestones
 *
 * @author original code from Open Dynamics.
 * @name milestone
 * @package 2-plan
 * @version 0.4.5
 * @link http://2-plan.com
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License v3 or later
 * @global $mylog
 */
class milestone extends TableBase
{
    private $mylog;

    /**
     * Constructor
     * Initialize the event log
     */
    function __construct()
    {
        $this->mylog = new mylog;
        $this->table_name = 'milestones';
    }

    /**
     * Add a milestone
     *
     * @param int $project ID of the associated project
     * @param string $name Name of the milestone
     * @param string $desc Description
     * @param string $end Day the milestone is due
     * @param int $status Status (0 = finished, 1 = open)
     * @return bool
     */
    function add($project, $name, $desc, $end, $status, $external = 0, $uuid = "", $start = "")
    {
        $project = (int) $project;
        $name = mysql_real_escape_string($name);
        $desc = mysql_real_escape_string($desc);
        $end = strtotime($end);
        $status = (int) $status;
        $start = $start!="" ? strtotime($start) : time();

        $ins = mysql_query("INSERT INTO ".$this->getTableName()." (`project`,`name`,`desc`,`start`,`end`,`status`,`external`, `uuid`) VALUES ($project,'$name','$desc','$start','$end','$status','$external','$uuid')");

        if ($ins)
        {
            $insid = mysql_insert_id();
            $this->mylog->add($name, 'milestone' , 1, $project);
            return $insid;
        }
        else
        {
            return false;
        }
    }

    /**
     * Edit a milestone
     *
     * @param int $id Milestone ID
     * @param string $name Name
     * @param string $desc Description
     * @param string $end Day it is due
     * @param int $external External
     * @return bool
     */
    function edit($id, $name, $desc, $end, $external)
    {
        $id = (int) $id;
        $name = mysql_real_escape_string($name);
        $desc = mysql_real_escape_string($desc);
        $end = strtotime($end);

        $upd = mysql_query("UPDATE ".$this->getTableName()." SET `name`='$name', `desc`='$desc', `end`='$end', `external`='$external' WHERE ID=$id");
        if ($upd)
        {
            $nam = mysql_query("SELECT project,name FROM ".$this->getTableName()." WHERE ID = $id");
            $nam = mysql_fetch_row($nam);
            $project = $nam[0];
            $name = $nam[1];

            $this->mylog->add($name, 'milestone' , 2, $project);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Delete a milestone
     *
     * @param int $id Milestone ID
     * @return bool
     */
    function del($id)
    {
        $id = (int) $id;

        $nam = mysql_query("SELECT project,name FROM ".$this->getTableName()." WHERE ID = $id");
        $del = mysql_query("DELETE FROM ".$this->getTableName()." WHERE ID = $id");
        $del1 = mysql_query("DELETE FROM ".$this->getTablePrefix()."milestones_assigned WHERE milestone = $id");
        if ($del)
        {
            $nam = mysql_fetch_row($nam);
            $project = $nam[0];
            $name = $nam[1];

            $this->mylog->add($name, 'milestone', 3, $project);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Mark a milestone as open / active
     *
     * @param int $id Milestone ID
     * @return bool
     */
    function open($id)
    {
        $id = (int) $id;

        $upd = mysql_query("UPDATE ".$this->getTableName()." SET status = 1 WHERE ID = $id");

        if ($upd)
        {
            $nam = mysql_query("SELECT project,name FROM ".$this->getTableName()." WHERE ID = $id");
            $nam = mysql_fetch_row($nam);
            $project = $nam[0];
            $name = $nam[1];

            $this->mylog->add($name, 'milestone', 4, $project);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Marka milestone as finished
     *
     * @param int $id Milestone ID
     * @return bool
     */
    function close($id)
    {
        $id = (int) $id;

        $upd = mysql_query("UPDATE ".$this->getTableName()." SET status = 0 WHERE ID = $id");
        $tasklists = $this->getMilestoneTasklists($id);
        if (!empty($tasklists))
        {
            foreach ($tasklists as $tasklist)
            {
                $tl = new tasklist();
                $tasks = $tl->getTasksFromList($tasklist[ID]);
                foreach ($tasks as $task)
                {
                    $close_task = mysql_query("UPDATE ".$this->getTablePrefix()."tasks SET status = 0 WHERE ID = $task[ID]");
                }
                $close_tasklist = mysql_query("UPDATE ".$this->getTablePrefix()."workpackage SET status = 0 WHERE ID = $tasklist[ID]");
            }
        }

        if ($upd)
        {
            $nam = mysql_query("SELECT project,name FROM ".$this->getTableName()." WHERE ID = $id");
            $nam = mysql_fetch_row($nam);
            $project = $nam[0];
            $name = $nam[1];

            $this->mylog->add($name, 'milestone', 5, $project);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Assign a milestone to a user
     *
     * @param int $milestone Milestone ID
     * @param int $user User ID
     * @return bool
     */
    function assign($milestone, $user)
    {
        $milestone = (int) $milestone;
        $user = (int) $user;

        $upd = mysql_query("INSERT INTO ".$this->getTablePrefix()."milestones_assigned (NULL,$user,$milestone)");
        if ($upd)
        {
            $nam = mysql_query("SELECT project,name FROM ".$this->getTableName()." WHERE ID = $id");
            $nam = mysql_fetch_row($nam);
            $project = $nam[0];
            $name = $nam[1];

            $this->mylog->add($name, 'milestone', 6, $project);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Delete the assignment of a milestone to a given user
     *
     * @param int $milestone Milestone ID
     * @param int $user User ID
     * @return bool
     */
    function deassign($milestone, $user)
    {
        $milestone = (int) $milestone;
        $user = (int) $user;

        $upd = mysql_query("DELETE FROM ".$this->getTablePrefix()."milestones_assigned WHERE user = $user AND milestone = $milestone");
        if ($upd)
        {
            $nam = mysql_query("SELECT project,name FROM ".$this->getTableName()." WHERE ID = $id");
            $nam = mysql_fetch_row($nam);
            $project = $nam[0];
            $name = $nam[1];

            $this->mylog->add($name, 'milestone', 7, $project);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Return a milestone with its tasklists
     *
     * @param int $id Milestone ID
     * @return array $milestone Milestone details
     */
    function getMilestone($id)
    {
        $id = (int) $id;

        $sel = mysql_query("SELECT * FROM ".$this->getTableName()." WHERE ID = $id");
        $milestone = mysql_fetch_array($sel);

        if (!empty($milestone))
        {
            $endstring = date(CL_DATEFORMAT, $milestone["end"]);
            $milestone["endstring"] = $endstring;
            $milestone["fend"] = $endstring;

            $startstring = date(CL_DATEFORMAT, $milestone["start"]);
            $milestone["startstring"] = $startstring;

            $milestone["name"] = stripslashes($milestone["name"]);
            $milestone["desc"] = stripslashes($milestone["desc"]);

            $psel = mysql_query("SELECT name FROM ".$this->getTablePrefix()."projekte WHERE ID = $milestone[project]");
            $pname = mysql_fetch_row($psel);
            $pname = $pname[0];
            $milestone["pname"] = $pname;
            $milestone["pname"] = stripslashes($milestone["pname"]);

            $dayslate = $this->getDaysLeft($milestone["end"]);
            $dayslate = str_replace("-", "" , $dayslate);
            $milestone["dayslate"] = $dayslate;
            $milestone["daysleft"] = $dayslate;

            $tasks = $this->getMilestoneTasklists($milestone["ID"]);
            $milestone["tasks"] = $tasks;
            $messages = $this->getMilestoneMessages($milestone["ID"]);
            $milestone["messages"] = $messages;
            
            $date_limits = mysql_fetch_assoc(mysql_query("SELECT MIN(startdate) AS start, MAX(finishdate) AS end FROM ".$this->getTablePrefix()."workpackage WHERE milestone='$id'"));
            $milestone['gantt_start'] = $date_limits["start"];
            $milestone['duration'] = ceil((strtotime(date("Y-m-d", $date_limits["end"]))-strtotime(date("Y-m-d", $date_limits["start"])))/(3600*3))+8;
            $milestone["done"] = $this->getProgress($milestone["ID"]);
            
            return $milestone;
        }
        else
        {
            return false;
        }
    }

    /**
     * Return the latest milestones
     *
     * @param int $status Status (0 = finished, 1 = open)
     * @param int $num Number of milestones to return
     * @return array $milestones Details of the milestones
     */
    function getMilestones($status = 1, $num = 10)
    {
        $status = (int) $status;
        $num = (int) $num;

        $milestones = array();

        $sel = mysql_query("SELECT ID FROM ".$this->getTableName()." WHERE `status`=$status LIMIT $num");

        while ($milestone = mysql_fetch_array($sel))
        {
            $themilestone = $this->getMilestone($milestone["ID"]);
            array_push($milestones, $themilestone);
        }

        if (!empty($milestones))
        {
            return $milestones;
        }
        else
        {
            return false;
        }
    }

    /**
     * Return all finished milestones of a given project
     *
     * @param int $project Project ID
     * @return array $stones Details of the milestones
     */
    function getDoneProjectMilestones($project)
    {
        $project = (int) $project;

        $sel = mysql_query("SELECT ID FROM ".$this->getTableName()." WHERE project = $project AND status = 0 ORDER BY ID ASC");
        $stones = array();

        while ($milestone = mysql_fetch_array($sel))
        {
            $themilestone = $this->getMilestone($milestone["ID"]);
            array_push($stones, $themilestone);
        }

        if (!empty($stones))
        {
            return $stones;
        }
        else
        {
            return false;
        }
    }

    /**
     * Return all late milestones of a given project
     *
     * @param int $project Project ID
     * @param int $lim Number of milestones to return
     * @return array $milestones Dateils of the late milestones
     */
    function getLateProjectMilestones($project, $lim = 10)
    {
        $project = (int) $project;
        $lim = (int) $lim;

        $tod = date("d.m.Y");
        $now = strtotime($tod);
        $milestones = array();

        $sql = "SELECT ID FROM ".$this->getTableName()." WHERE project = $project AND end < $now AND status = 1 ORDER BY end ASC LIMIT $lim";

        $sel1 = mysql_query($sql);
        while ($milestone = mysql_fetch_array($sel1))
        {
            if (!empty($milestone))
            {
                $themilestone = $this->getMilestone($milestone["ID"]);
                array_push($milestones, $themilestone);
            }
        }

        if (!empty($milestones))
        {
            return $milestones;
        }
        else
        {
            return false;
        }
    }

    /**
     * Return all open milestones of a given project
     *
     * @param int $project Project ID
     * @param int $lim Number of milestones to return
     * @return array $milestones Details of the open milestones
     */
    function getAllProjectMilestones($project, $lim = 10)
    {
        $project = (int) $project;
        $lim = (int) $lim;

        $tod = date("d.m.Y");
        $now = strtotime($tod);
        $milestones = array();
        $sql = "SELECT ID FROM ".$this->getTableName()." WHERE project = $project AND status = 1 ORDER BY end ASC LIMIT $lim";

        $sel1 = mysql_query($sql);
        while ($milestone = mysql_fetch_array($sel1))
        {
            if (!empty($milestone))
            {
                $themilestone = $this->getMilestone($milestone["ID"]);
                array_push($milestones, $themilestone);
            }
        }

        if (!empty($milestones))
        {
            return $milestones;
        }
        else
        {
            return false;
        }
    }

    /**
     * Return all milestone of a given project, that are not late
     *
     * @param int $project Project ID
     * @param int $lim Number of milestones to return
     * @return array $milestones Details of the milestones
     */
    function getProjectMilestones($project, $lim = -1)
    {
        $project = (int) $project;
        $lim = (int) $lim;

        $now = time();
        $milestones = array();
        $sql = "SELECT * FROM ".$this->getTableName()." WHERE project = $project AND end > $now AND status = 1 ORDER BY end ASC";
        
        if ($lim > 0) {
        	$sql .= " LIMIT $lim";
        }

        $sel1 = mysql_query($sql);
        while ($milestone = mysql_fetch_array($sel1))
        {
            $themilestone = $this->getMilestone($milestone["ID"]);
            array_push($milestones, $themilestone);
        }

        if (!empty($milestones))
        {
            return $milestones;
        }
        else
        {
            return false;
        }
    }

    /**
     * Return all milestones of a projects, that are due today
     *
     * @param int $project Project ID
     * @param int $lim Number of milestones to return
     * @return array $milestones Details of the milestones
     */

    function getTodayProjectMilestones($project, $lim = 10)
    {
        $project = (int) $project;
        $lim = (int) $lim;

        $tod = date("d.m.Y");
        $now = strtotime($tod);
        $milestones = array();

        $sel1 = mysql_query("SELECT * FROM ".$this->getTableName()." WHERE project = $project AND end = '$now' AND status = 1 ORDER BY end ASC LIMIT $lim");
        while ($milestone = mysql_fetch_array($sel1))
        {
            $themilestone = $this->getMilestone($milestone["ID"]);
            array_push($milestones, $themilestone);
        }

        if (!empty($milestones))
        {
            return $milestones;
        }
        else
        {
            return false;
        }
    }

    /**
     * Return all milestones of that belong to the loggedin user, due on a given day.
     * This method is needed for populating the calendar widget with data.
     *
     * @param int $m Month Month, without leading zero (e.g. 5 for march)
     * @param int $y Year Year in format yyyy
     * @param int $d Day Without leading zero (e.g. 1 for the 1st of the month $m in year $y)
     * @return array $milestones Details of the milestones
     */
    function getTodayMilestones($m, $y, $d, $project = 0)
    {
        $m = (int) $m;
        $y = (int) $y;

        if ($m > 9)
        {
            $startdate = date($d . "." . $m . "." . $y);
        }
        else
        {
            $startdate = date($d . ".0" . $m . "." . $y);
        }
        $starttime = strtotime($startdate);
        $user = (int) $_SESSION["userid"];

        $timeline = array();

        if ($project > 0)
        {
            $sel1 = mysql_query("SELECT * FROM ".$this->getTableName()." WHERE project =  $project AND status=1 AND end = '$starttime'");
        }
        else
        {
            $sel1 = mysql_query("SELECT m.*,pa.user,p.name AS pname FROM ".$this->getTableName()." m,".$this->getTablePrefix()."projekte_assigned pa,".$this->getTablePrefix()."projekte p WHERE m.project = pa.projekt AND m.project = p.ID HAVING pa.user = $user AND status=1 AND end = '$starttime'");
        }

        while ($stone = mysql_fetch_array($sel1))
        {
            $stone["daysleft"] = $this->getDaysLeft($stone["end"]);
            array_push($timeline, $stone);
        }

        if (!empty($timeline))
        {
            return $timeline;
        }
        else
        {
            return array();
        }
    }

    /**
     * Return all open tasklists associated to a given milestones
     *
     * @param int $milestone Milestone ID
     * @return array $lists Details of the tasklists
     */
    private function getMilestoneTasklists($milestone)
    {
        $milestone = (int) $milestone;

        $sel = mysql_query("SELECT * FROM ".$this->getTablePrefix()."workpackage WHERE milestone = $milestone AND status = 1");
        $lists = array();
        if ($milestone)
        {
            while ($list = mysql_fetch_array($sel))
            {
                $list["name"] = stripslashes($list["name"]);
                $list["desc"] = stripslashes($list["desc"]);
                array_push($lists, $list);
            }
        }
        if (!empty($lists))
        {
            return $lists;
        }
        else
        {
            return false;
        }
    }

    private function getMilestoneMessages($milestone)
    {
        $milestone = (int) $milestone;
        $sel = mysql_query("SELECT ID,title FROM ".$this->getTablePrefix()."messages WHERE milestone = $milestone");
        $msgs = array();
        while ($msg = mysql_fetch_array($sel))
        {
            array_push($msgs, $msg);
        }
        if (!empty($msgs))
        {
            return $msgs;
        }
    }

    /**
     * Return the days left from today until a given point in time
     *
     * @param int $end Point in time
     * @return int $days Days left
     */
    private function getDaysLeft($end)
    {
        $tod = date("d.m.Y");
        $now = strtotime($tod);
        $diff = $end - $now;
        $days = floor($diff / 86400);
        return $days;
    }

    /**
     * Format a milestone's timestamp
     *
     * @param int $milestones Milestone ID
     * @param int $format Wanted time format
     * @return array $milestones Milestone with the formatted timestamp
     */
    function formatdate(array $milestones)
    {
        $cou = 0;

        if ($milestones)
        {
            foreach($milestones as $stone)
            {
                $datetime = date(CL_DATEFORMAT, $stone[5]);
                $milestones[$cou]["due"] = $datetime;
                $cou = $cou + 1;
            }
        }

        if (!empty($milestones))
        {
            return $milestones;
        }
        else
        {
            return false;
        }
    }

    /**
     * Return all milestone of a given workpackage, that are not late
     *
     * @param int $workpackage Workpackage ID
     * @param int $lim Number of milestones to return
     * @return array $milestones Details of the milestones
     */
    function getWorkpackageMilestones($workpackage, $lim = -1)
    {
        $workpackage = (int) $workpackage;
        $lim = (int) $lim;

        $now = time();
        $milestones = array();
        $sql = "SELECT m.ID FROM ".$this->getTablePrefix()."workpackage w LEFT JOIN ".$this->getTableName()." m ON w.milestone=m.ID WHERE w.ID = $workpackage ORDER BY m.end ASC";
        
        if ($lim > 0) {
        	$sql .= " LIMIT $lim";
        }

        $sel1 = mysql_query($sql);
        while ($milestone = mysql_fetch_array($sel1))
        {
            $themilestone = $this->getMilestone($milestone["ID"]);
            array_push($milestones, $themilestone);
        }

        if (!empty($milestones))
        {
            return $milestones;
        }
        else
        {
            return false;
        }
    }

    /**
     * Progressmeter
     *
     * @param int $milestone Milestone ID
     * @return array $done Percent of finished tasks
     */
    function getProgress($milestone)
    {
        $milestone = (int) mysql_real_escape_string($milestone);
        $workpackage_q = mysql_query("SELECT ID FROM ".$this->getTablePrefix()."workpackage WHERE milestone = '$milestone'");
        while($workpackage = mysql_fetch_assoc($workpackage_q)){
            $wp = new tasklist();
            $percent += $wp->getProgress($workpackage["ID"]);
            $workpackage_count++;
        }
        return $workpackage_count>0 ? round($percent/$workpackage_count) : 0;
    }
}

?>