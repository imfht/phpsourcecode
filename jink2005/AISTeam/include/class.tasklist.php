<?php
/*
 * This class provides methods to realize tasklists
 *
 * @author original code from Open Dynamics.
 * @package 2-plan
 * @name tasklist
 * @version 0.4.5
 * @link http://2-plan.com
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License v3 or later
 */
class tasklist extends TableBase
{
    public $mylog;

    /*
    * Constructor
    * Initialize the event log
    */
    function __construct()
    {
        $this->mylog = new mylog;
        $this->table_name = 'workpackage'; // was tasklist earlier
    }

    /*
     * Edit a tasklist
     *
     * @param int $project ID of the associated project
     * @param string $name Name of the tasklist
     * @param string $desc Description of the tasklist
     * @param int $access Access level (0 = public)
     * @param int $milestone ID of the associated milestone (0 = no association)
     * @return bool
     */
    function add_workpackage($project, $name, $desc, $access = 0, $milestone = 0, $planeffort = 0, $startdate = 0, $finishdate = 0, $responsible = '', $uuid = '')
    {
        $name = mysql_real_escape_string($name);
        $desc = mysql_real_escape_string($desc);
        $project = (int) $project;
        $access = (int) $access;
        $milestone = (int) $milestone;
        
        $planeffort = floatval($planeffort);
        $responsible = mysql_real_escape_string($responsible);
        $startdate = $startdate > 0 ? strtotime($startdate) : time();
        $finishdate = $finishdate > 0 ? strtotime($finishdate) : '0';

        $start = time();
        
        $ins = mysql_query("INSERT INTO ".$this->getTableName()." (`project`,`name`,`desc`,`start`,`status`,`access`,`milestone`,`planeffort`,`startdate`,`finishdate`,`responsible`,`uuid`) VALUES ($project,'$name','$desc','$start',1,$access,$milestone,$planeffort,'$startdate','$finishdate','$responsible','$uuid')");
		if ($ins)
        {
            $insid = mysql_insert_id();
            $this->mylog->add($name, 'tasklist', 1, $project);
            return $insid;
        }
        else
        {
            return false;
        }
    }

    /*
     * Edit a tasklist
     *
     * @param int $id Tasklist ID
     * @param string $name Tasklist name
     * @param string $desc Tasklist description
     * @param int $milestone ID of the associated milestone
     * @return bool
     */
    function edit_workpackage($id, $name, $desc, $milestone, $planeffort, $startdate, $finishdate, $responsible)
    {
        $name = mysql_real_escape_string($name);
        $desc = mysql_real_escape_string($desc);
        $startdate = strtotime(mysql_real_escape_string($startdate));
        $finishdate = strtotime(mysql_real_escape_string($finishdate));
        $responsible = mysql_real_escape_string($responsible);
        
        $id = (int) $id;
        $milestone = (int) $milestone;
        $planeffort = floatval($planeffort);
        
        $upd = mysql_query("UPDATE ".$this->getTableName()." SET `name`='$name', `desc`='$desc', `milestone`=$milestone, `planeffort`=$planeffort, `startdate`='$startdate', `finishdate`='$finishdate', `responsible`='$responsible' WHERE ID = $id");
        if ($upd)
        {
            $sel = mysql_query("SELECT project FROM ".$this->getTableName()." WHERE ID = $id");
            $proj = mysql_fetch_array($sel);
            $proj = $proj[0];

            $this->mylog->add($name, 'tasklist', 2, $proj);
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     * Delete a tasklist
     *
     * @param int $id Tasklist ID
     * @return bool
     */
    function del_workpackage($id)
    {
        $id = (int) $id;

        $sel = mysql_query("SELECT project, name FROM ".$this->getTableName()." WHERE ID = $id");
        $del = mysql_query("DELETE FROM ".$this->getTableName()." WHERE ID = $id LIMIT 1");
        if ($del)
        {
            $tasks1 = $this->getTasksFromList($id);
            $taskobj = new task();
            if (!empty($tasks1))
            {
                foreach($tasks1 as $task)
                {
                    $taskobj->del($task["ID"]);
                }
            }
            $tasks2 = $this->getTasksFromList($id, 0);
            if (!empty($tasks2))
            {
                foreach($tasks2 as $task)
                {
                    $taskobj->del($task["ID"]);
                }
            }
            $sel1 = mysql_fetch_array($sel);
            $proj = $sel1[0];
            $name = $sel1[1];
            $this->mylog->add($name, 'tasklist', 3, $proj);
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     * Reactivate / open a tasklist
     *
     * @param int $id Tasklist ID
     * @return bool
     */
    function open_workpackage($id)
    {
        $id = (int) $id;

        $upd = mysql_query("UPDATE ".$this->getTableName()." SET status = 1 WHERE ID = $id");

        if ($upd)
        {
            $nam = mysql_query("SELECT project, name FROM ".$this->getTableName()." WHERE ID = $id");
            $nam = mysql_fetch_row($nam);
            $project = $nam[0];
            $name = $nam[1];

            $this->mylog->add($name, 'tasklist', 4, $project);
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     * Finish / close a tasklist
     *
     * @param int $id Tasklist ID
     * @return bool
     */
    function close_workpackage($id)
    {
        $id = (int) $id;

        $upd = mysql_query("UPDATE ".$this->getTableName()." SET status = 0 WHERE ID = $id");
        // Close assigned milestone too, if no other open tasklists are assigned to it
        $sql = mysql_query("SELECT milestone FROM ".$this->getTableName()." WHERE ID = $id");
        $milestone = mysql_fetch_row($sql);
        if ($milestone[0] > 0)
        {
            $sql2 = mysql_query("SELECT count(*) FROM ".$this->getTableName()." WHERE milestone = $milestone[0] AND status = 1");
            $cou = mysql_fetch_row($sql2);

            if ($cou[0] == 0)
            {
                $miles = new milestone();
                $miles->close($milestone[0]);
            }
        }
        $tasks = $this->getTasksFromList($id);
        if (!empty($tasks))
        {
            $taskobj = new task();
            foreach($tasks as $task)
            {
                $taskobj->close($task["ID"]);
            }
        }
        // Log entry
        if ($upd)
        {
            $nam = mysql_query("SELECT project, name FROM ".$this->getTableName()." WHERE ID = $id");
            $nam = mysql_fetch_row($nam);
            $project = $nam[0];
            $name = $nam[1];

            $this->mylog->add($name, 'tasklist', 5, $project);
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     * Return all tasklists (including its open tasks) associated with a given project
     *
     * @param int $project Project ID
     * @param int $status Tasklist status (0 = Finished, 1 = Active)
     * @return array $tasklists Details of the tasklists
     */
    function getProjectTasklists($project, $status = 1)
    {
        $project = (int) $project;
        $status = (int) $status;

        if($status<2)
          $sql_status = " AND status=$status";
        $sel = mysql_query("SELECT * FROM ".$this->getTableName()." WHERE project = $project".$sql_status);
        $tasklists = array();

        $taskobj = new task();
        while ($list = mysql_fetch_array($sel))
        {
            $sel2 = mysql_query("SELECT ID FROM ".$this->getTablePrefix()."tasks WHERE workpackage = $list[ID] AND status=1 ORDER BY `end` ASC");
            $list['tasks'] = array();
            while ($tasks = mysql_fetch_array($sel2))
            {
                array_push($list['tasks'], $taskobj->getTask($tasks["ID"]));
            }

            $sel3 = mysql_query("SELECT ID FROM ".$this->getTablePrefix()."tasks WHERE workpackage = $list[ID] AND status=0 ORDER BY `end` ASC");
            $list['oldtasks'] = array();
            while ($oldtasks = mysql_fetch_array($sel3))
            {
                array_push($list['oldtasks'], $taskobj->getTask($oldtasks["ID"]));
            }

            // add the days left to start and days left to finish date
            $list['daystostart'] = $this->getDaysLeft($list['startdate'], 0);
            $list['daystofinish'] = $this->getDaysLeft($list['finishdate'], 1);
            $list['duration'] = ceil(($list['finishdate']-$list['startdate'])/(3600*3))+8;
            $list["done"] = $this->getProgress($list["ID"]);
            $list["actual"] = $this->getActual($list["ID"]);
            $list["forecast"] = $list["actual"]+$this->getEffort($list["ID"]);
            $list["is_started"] = $list["actual"]>0 && $list['startdate']<time();
            $list["timeok"] = $this->getTimeOk($list["ID"]);
            
            array_push($tasklists, $list);
        }

        if (!empty($tasklists))
        {
            return $tasklists;
        }
        else
        {
            return false;
        }
    }

    /*
     * Return a tasklist
     *
     * @param int $id Taskist ID
     * @return array $tasklist Tasklist details
     */
    function getTasklist($id)
    {
        $id = (int) $id;

        $sel = mysql_query("SELECT * FROM ".$this->getTableName()." WHERE ID = $id");
        $tasklist = mysql_fetch_array($sel);

        if (!empty($tasklist))
        {
            $startstring = date("d.m.y", $tasklist["start"]);
            $startdate = date("d.m.Y", $tasklist["startdate"]);
            $finishdate = date("d.m.Y", $tasklist["finishdate"]);
            
            $tasklist["startdate"] = $startdate;
            $tasklist["finishdate"] = $finishdate;
            $tasklist["name"] = stripslashes($tasklist["name"]);
            $tasklist["desc"] = stripslashes($tasklist["desc"]);
            $tasklist["done"] = $this->getProgress($tasklist["ID"]);
            $milestone = (object) new milestone();
            $milestone = $milestone->getMilestone($tasklist["milestone"]);
            $tasklist["milestone_name"] = $milestone["name"];

            return $tasklist;
        }
        else
        {
            return false;
        }
    }

    /*
     * Return all open or all finished tasks of a given tasklist
     *
     * @param int $id Tasklist ID
     * @param int $status Status of the tasks (0 = finished, 1 = open)
     * @return array $tasks Details of the tasks
     */
    function getTasksFromList($id, $status = 1)
    {
        $id = (int) $id;
        $status = (int) $status;

        $taskobj = new task();

        $sel = mysql_query("SELECT ID FROM ".$this->getTablePrefix()."tasks WHERE `workpackage` = $id AND `status` = $status ORDER BY ID DESC");
        $tasks = array();
        while ($task = mysql_fetch_array($sel))
        {
            array_push($tasks, $taskobj->getTask($task["ID"]));
        }

        if (!empty($tasks))
        {
            return $tasks;
        }
        else
        {
            return false;
        }
    }
    
    function getID($uuid, $project_id) {
    	$uuid = mysql_escape_string($uuid);
    	$project_id = (int) $project_id;
    	
        $tasklistobj = new tasklist();

        $sel = mysql_query("SELECT ID FROM ".$this->getTableName()." WHERE (`uuid` = '$uuid' OR `project` = '$project_id')");
        
        $id = null;
        $tasklist = array();
        while ($tasklist = mysql_fetch_array($sel))
        {
            $id = (int) $tasklist['ID'];
        }

        if (!empty($id))
        {
            return $id;
        }
        return false;
    }
    
    /**
     * Return the number of left days until workpackage starts (0) or is due (1)
     *
     * @param string $whents Timestamp of the date for which the days should be calculated
     * @param string $what Calculate from start (0) or from due (0)
     * @return int $days Days left
     */
    private function getDaysLeft($whents, $what)
    {
        $tod = date("d.m.Y");
        $now = strtotime($tod);
        $diff = $whents - $now;
        $days = floor($diff / 86400);
        return $days;
    }

    /**
     * Progressmeter
     *
     * @param int $workpackage Workpackage ID
     * @return array $done Percent of finished tasks
     */
    function getProgress($workpackage)
    {
        $workpackage = mysql_real_escape_string($workpackage);
        $workpackage = (int) $workpackage;
        $wp = mysql_fetch_assoc(mysql_query("SELECT status FROM ".$this->getTableName()." WHERE ID = $workpackage"));
        if($wp["status"]=="0")
          return 100;

        $otasks = mysql_query("SELECT COUNT(*) FROM ".$this->getTablePrefix()."tasks WHERE workpackage = $workpackage AND status = 1");
        $otasks = mysql_fetch_row($otasks);
        $otasks = $otasks[0];

        $clotasks = mysql_query("SELECT COUNT(*) FROM ".$this->getTablePrefix()."tasks WHERE workpackage = $workpackage AND status = 0");
        $clotasks = mysql_fetch_row($clotasks);
        $clotasks = $clotasks[0];

        $totaltasks = $otasks + $clotasks;
        if ($totaltasks > 0 and $clotasks > 0) {
            $done = $clotasks / $totaltasks * 100;
            $done = round($done);
        } else {
            $done = 0;
        }
        return $done;
    }
    
    /**
     * all tasks IDs of the workpackage
     *
     * @param int $workpackage Workpackage ID
     * @return array $tasks_ids
     */
    private function getTasksIDs($workpackage)
    {
        $workpackage = (int) $workpackage;
        
        $tasks_q = mysql_query("SELECT ID FROM ".$this->getTablePrefix()."tasks WHERE workpackage = '$workpackage'");
        $tasks = array();
        while($task = mysql_fetch_assoc($tasks_q))
            $tasks[] = $task["ID"];
        return $tasks;
    }
    
    /**
     * Time booked on all tasks for the workpackage
     *
     * @param int $workpackage Workpackage ID
     * @return float $time
     */
    private function getActual($workpackage)
    {
        $tasks = $this->getTasksIDs($workpackage);
        if(count($tasks)>0)
            $time = mysql_fetch_assoc(mysql_query("SELECT SUM(hours) as actual FROM ".$this->getTablePrefix()."timetracker WHERE task IN ('".join("','",$tasks)."')"));

        return (float)number_format($time["actual"],1,".","");
    }
    
    /**
     * "Effort to complete" of all tasks for the workpackage
     *
     * @param int $workpackage Workpackage ID
     * @return float $time
     */
    private function getEffort($workpackage)
    {
        $workpackage = (int) $workpackage;
        $tasks = mysql_fetch_assoc(mysql_query("SELECT SUM(efforttocomplete) AS effort FROM ".$this->getTablePrefix()."tasks WHERE workpackage = '$workpackage'"));
        return (float)number_format($tasks["effort"],1,",","");
    }
    
    /**
     * Time Ok
     *
     * @param int $workpackage Workpackage ID
     * @return bool
     */
    private function getTimeOk($workpackage)
    {
        $workpackage = (int) $workpackage;
        $date = time();
        
        $workpackage = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$this->getTableName()." WHERE ID = '$workpackage'"));
        $k = $workpackage["planeffort"]/($workpackage["finishdate"]-$workpackage["startdate"]);
        $x = $workpackage["finishdate"]-$date;
        $y1 = $k*$x;
        
        $tasks = $this->getTasksIDs($workpackage["ID"]);
        if(count($tasks)>0)
            $estimate = mysql_fetch_assoc(mysql_query("SELECT SUM(efforttocomplete) AS effort FROM ".$this->getTablePrefix()."tasks_estimate WHERE task IN (".join(",",$tasks).") AND date='".date("Y-m-d", $date)."'"));
        $y2 = $estimate["effort"];
        
        return $y1>=$y2;
    }

    /**
     * get ideal/planned list of the task estimates for selected workpackage
     *
     * @param int $workpackage workpackage ID
     * @param string $type data type
     * @return array $estimates list of the task estimates
     */
    function estimateListWorkpackage($workpackage, $type = "ideal")
    {
        $workpackage = (int) $workpackage;
        $workpackage = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$this->getTableName()." WHERE ID='$workpackage'"));
        
        $day = 86400;
        $estimates = array();
        $estimates[date("Y-m-d", $workpackage["startdate"])] = $type=="ideal" ? $workpackage["planeffort"] : 0;
        for($date = $workpackage["startdate"]+$day; $date<$workpackage["finishdate"]; $date=$date+$day){
            if(date("Y-m-d", $date)==date("Y-m-d", $workpackage["startdate"]))
                continue;
            $estimates[date("Y-m-d", $date)] = '"x"';
        }
        $estimates[date("Y-m-d", $workpackage["finishdate"])] = $type=="ideal" ? 0 : $workpackage["planeffort"];
        
        return $estimates;
    }
}

?>
