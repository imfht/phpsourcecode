<?php
/*
* This class provides Remote Api methods
*
* @author Yuriy Bakhtin
* @name RemoteApi
* @version 0.1
* @package 2-plan
* @link http://2-plan.com
* @license http://opensource.org/licenses/gpl-license.php GNU General Public License v3 or later
*/

class RemoteApi extends TableBase {
	private $accesskey = "";
	private $project = null;
	private $user_permissions = array();
	
	/*
	* Constructor
	* Initialize the event log
	*/
	public function __construct($request, $set_project = true) {
		$this->table_name = 'projekte';
		$this->mylog = new mylog;
		$this->setUserPermissions($request->getParam('name'));
		if($set_project) {
			$this->setAccessKey($request->getParam('accesskey'));
			$this->setProject();
		}
	}

	/*
	* remove all unsupported symbols from access key
	*
	* @param string $accesskey access key
	* @set string $accesskey clear access key
	*/
	private function setAccessKey($accesskey) {
		$this->accesskey = preg_replace("/[^a-z0-9]/", "", $accesskey);
	}

	/*
	* get project info from DB by accesskey
	*
	* @param 
	* @return bool $result true if project exists
	*/
	private function setProject() {
		if($project = mysql_fetch_object(mysql_query("SELECT * FROM ".$this->getTableName()." WHERE accesskey='{$this->accesskey}'"))) {
			$this->project = $project;
			return true;
		} else {
			Response::error("Project doesn't exist");
			return false;
		}
	}

	/*
	* set user permissions
	*
	* @param string $login user name(for root) or email
	* @set array $user_permissions
	*/
	public function setUserPermissions($login) {
		$role = mysql_fetch_assoc(mysql_query("SELECT ra.role FROM ".$this->getTablePrefix()."user u LEFT JOIN ".$this->getTablePrefix()."roles_assigned ra ON u.ID=ra.user WHERE (u.name='$login' AND u.ID=1) OR u.email='$login'"));
		if($roles = mysql_fetch_assoc(mysql_query("SELECT projects, tasks, milestones, messages, files, user, timetracker, admin, api FROM ".$this->getTablePrefix()."roles WHERE ID = '{$role["role"]}'"))) {
			foreach($roles as $name => $permissions) {
				$roles[$name] = unserialize($permissions);
			}
		} else {
			$roles = array();
		}
		$this->user_permissions = $roles;
	}

	/*
	* get user permissions
	*
	* @set array $user_permissions
	*/
	public function getUserPermissions($name = "") {
		return $name!="" ? $this->user_permissions[$name] : $this->user_permissions;
	}
	
	/*
	* check access for the role and actions
	*
	* @param string $role role name
	* @param strings $action many action names
	* @set array $user_permissions
	*/
	public function access($role/*, $action1, $action2, ..., $actionN */) {
		$actions = func_get_args();
		unset($actions[0]);
		$permission = true;
		foreach($actions as $action) {
			$permission &= $this->user_permissions[$role][$action];
		}
		if(!$permission)
			Response::error("No Permissions");
	}

	/*
	* verify existing of the workpackage
	*
	* @param int $uuid uuid
	* @param int $workpackage_id Workpackage ID
	* @return int workpackage ID
	*/
	public function existWorkpackage($uuid, $workpackage_id) {
		$uuid = (int) $uuid;
		$workpackage_id = (int) $workpackage_id;
		
		$workpackage = mysql_fetch_assoc(mysql_query("SELECT ID FROM ".$this->getTablePrefix()."workpackage WHERE (uuid='$uuid' OR ID='$workpackage_id') AND project='{$this->project->ID}' LIMIT 1"));
		
		if(!$workpackage)
			Response::error("Workpackage doesn't exist");
		else
			return (int) $workpackage["ID"];
	}

	/*
	* verify existing of the task
	*
	* @param int $task_id Task ID
	*/
	public function existTask($task_id) {
		$task_id = (int) $task_id;
		
		$task = mysql_fetch_assoc(mysql_query("SELECT ID FROM ".$this->getTablePrefix()."tasks WHERE ID='$task_id' AND project='{$this->projectID()}'"));
		if(!$task)
			Response::error("Task doesn't exist");
	}

	/*
	* get project
	*
	* @return array $project
	*/
	public function project() {
		$project = (array) $this->project;
		
		unset($project["ID"]);
		unset($project["accesskey"]);
		unset($project["uuid"]);
		
		$probj = new project();
		$project["actual"] = $this->projectActual();
		$project["completed"] = $probj->getProgress($this->projectID())."%";
		
		return $project;
	}

	/*
	* get project ID
	*
	* @return int $project
	*/
	public function projectID() {
		return (int) $this->project->ID;
	}

	/*
	* update uuid of the project
	*
	* @param int $uuid
	* @return bool result
	*/
	public function projectUpdateUuid($uuid) {
		$uuid = (int) $uuid;
		
		$result = mysql_query("UPDATE ".$this->getTableName()." SET uuid='$uuid' WHERE ID='{$this->projectID()}'");
		return (bool) $result;
	}

	/*
	* get all users of the project
	*
	* @param int $limit max limit of the return users
	* @return array $users
	*/
	public function projectUsers($limit = 0) {
		$limit = (int) $limit;
		$users = array();
		
		if($limit>0)
			$sql_limit = " LIMIT $limit";
		$users_q = mysql_query("SELECT user FROM ".$this->getTablePrefix()."projekte_assigned WHERE projekt='{$this->projectID()}'$sql_limit");
		while($user = mysql_fetch_assoc($users_q)) {
			$user = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$this->getTablePrefix()."user WHERE ID='{$user["user"]}'"));
			unset($user["pass"]);
			$users[] = $user;
		}
		return $users;
	}

	/*
	* get all workpackages of the project
	*
	* @param int $status workpackage status (0 = Finished, 1 = Active, 2 = All)
	* @return array $workpackages
	*/
	public function projectWorkpackages($status) {
		$status = (int) $status;
		$workpackages = array();
		
		if($status==0 || $status==1)
			$sql_where = " AND status='$status'";
		$workpackages_q = mysql_query("SELECT * FROM ".$this->getTablePrefix()."workpackage WHERE project='{$this->project->ID}'$sql_where");
		while($workpackage = mysql_fetch_assoc($workpackages_q)) {
			$workpackage["actual"] = $this->workpackageActual($workpackage["ID"]);
			$workpackage["efforttocomplete"] = $this->workpackageEffort($workpackage["ID"]);
			$workpackages[] = $workpackage;
		}
		
		return $workpackages;
	}

	/*
	* get all tasks of the project
	*
	* @param int $project_id Project ID
	* @return array $tasks
	*/
	public function projectTasks() {
		$tasks = array();
		$tasks_q = mysql_query("SELECT * FROM ".$this->getTablePrefix()."tasks WHERE project='{$this->projectID()}'");
		while($task = mysql_fetch_assoc($tasks_q)) {
			$task["actual"] = $this->taskActual(array($task["ID"]));
			$tasks[] = $task;
		}
		
		return $tasks;
	}

	/*
	* Time booked on all tasks for the project
	*
	* @return float $time
	*/
	private function projectActual() {
		$tasks = $this->projectTasksIDs();
		return $this->taskActual($tasks);
	}

	/*
	* all tasks IDs of the project
	*
	* @param int $project_id Project ID
	* @return array $tasks_ids
	*/
	private function projectTasksIDs() {
		$tasks_q = mysql_query("SELECT ID FROM ".$this->getTablePrefix()."tasks WHERE project='{$this->projectID()}'");
		$tasks = array();
		while($task = mysql_fetch_assoc($tasks_q))
			$tasks[] = $task["ID"];
		return $tasks;
	}

	/*
	* get milestone ID by uuid
	*
	* @param int $uuid uuid
	* @return int $id ID of the milestone
	*/
	public function milestoneIDByUuid($uuid) {
		$uuid = mysql_real_escape_string($uuid);
		$milestone = mysql_fetch_assoc(mysql_query("SELECT ID FROM ".$this->getTablePrefix()."milestones WHERE uuid='$uuid'"));
		return (int) $milestone["ID"];
	}

	/*
	* get workpackage ID by uuid
	*
	* @param int $uuid uuid
	* @return int $id ID of the workpackage
	*/
	public function workpackageIDByUuid($uuid) {
		$uuid = mysql_real_escape_string($uuid);
		$workpackage = mysql_fetch_assoc(mysql_query("SELECT ID FROM ".$this->getTablePrefix()."workpackage WHERE uuid='$uuid'"));
		return (int) $workpackage["ID"];
	}

	/*
	* get all tasks of the workpackage
	*
	* @param int $workpackage_id Workpackage ID
	* @return array $tasks
	*/
	public function workpackageTasks($workpackage_id) {
		$workpackage_id = (int) $workpackage_id;
		
		$tasks = array();
		$tasks_q = mysql_query("SELECT * FROM ".$this->getTablePrefix()."tasks WHERE workpackage='$workpackage_id'");
		while($task = mysql_fetch_assoc($tasks_q)) {
			$task["actual"] = $this->taskActual(array($task["ID"]));
			$tasks[] = $task;
		}
		return $tasks;
	}

	/*
	* Time booked on all tasks for the workpackage
	*
	* @param int $workpackage_id Workpackage ID
	* @return float $time
	*/
	private function workpackageActual($workpackage_id) {
		$tasks = $this->workpackageTasksIDs($workpackage_id);
		return $this->taskActual($tasks);
	}

	/*
	* get efforttocomplete on all tasks for the workpackage
	*
	* @param int $workpackage_id Workpackage ID
	* @return float $time
	*/
	private function workpackageEffort($workpackage_id) {
		$tasks = $this->workpackageTasksIDs($workpackage_id);
		return $this->taskEffort($tasks);
	}

	/*
	* all tasks IDs of the workpackage
	*
	* @param int $workpackage_id Workpackage ID
	* @return array $tasks_ids
	*/
	private function workpackageTasksIDs($workpackage_id) {
		$tasks_q = mysql_query("SELECT ID FROM ".$this->getTablePrefix()."tasks WHERE workpackage='$workpackage_id'");
		$tasks = array();
		while($task = mysql_fetch_assoc($tasks_q))
			$tasks[] = $task["ID"];
		return $tasks;
	}

	/*
	* assign milestone to the workpackage
	*
	* @param int $workpackage_id Workpackage ID
	* @param int $workpackage_id Workpackage ID
	* @return bool $result
	*/
	public function workpackageAssignMilestone($workpackage_id, $milestone_id) {
		$result = mysql_query("UPDATE ".$this->getTablePrefix()."workpackage SET milestone='$milestone_id' WHERE ID='$workpackage_id'");
		return (bool) $result;
	}

	/*
	* Time booked on all the tasks
	*
	* @param int $task_ids Tasks IDs
	* @return float $time
	*/
	private function taskActual($task_ids) {
		if(count($task_ids)>0)
			$time = mysql_fetch_assoc(mysql_query("SELECT SUM(hours) as actual FROM ".$this->getTablePrefix()."timetracker WHERE task IN ('".join("','",$task_ids)."')"));
		return (string) number_format($time["actual"],2,".","");
	}

	/*
	* get efforttocomplete on all the tasks
	*
	* @param int $task_ids Tasks IDs
	* @return float $time
	*/
	private function taskEffort($task_ids) {
		if(count($task_ids)>0)
			$time = mysql_fetch_assoc(mysql_query("SELECT SUM(efforttocomplete) as effort FROM ".$this->getTablePrefix()."tasks WHERE ID IN ('".join("','",$task_ids)."')"));
		return (string) number_format($time["effort"],2,".","");
	}

	/*
	* get timetrackers for the task
	*
	* @param int $task_ids Tasks IDs
	* @return array $timetrackers
	*/
	public function timetrackerTask($task_id) {
		$task_id = (int) $task_id;
		
		$timetrackers = array();
		$timetrackers_q = mysql_query("SELECT t.*, u.name as user_name FROM ".$this->getTablePrefix()."timetracker t LEFT JOIN ".$this->getTablePrefix()."user u ON t.user=u.ID WHERE task='".$task_id."'");
		while($timetracker = mysql_fetch_assoc($timetrackers_q)){
			$timetrackers[] = $timetracker;
		}
		return $timetrackers;
	}
}
?>
