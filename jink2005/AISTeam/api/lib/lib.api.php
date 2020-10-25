<?php
require_once 'inc.init.php';

class Request {
	private $get;
	private $post;
	private $secret;
	private $postdata;

	public function __construct() {
		$this->get = $this->array_copy($_GET);
		$this->post = $this->array_copy($_POST);
		$this->secret = $GLOBALS['api_secret'];
		
		// fetches the raw post parameteres
		$this->postdata = file_get_contents("php://input");
	}

	public function process() {
		$this->authenticate();
		
		//Response::write(array("msg" => $this->getParam('method')));
		if ( $this->getParam('method') != null &&
			 in_array($this->getParam('method'), get_class_methods("ApiMethods")) ) {
			return call_user_func_array('ApiMethods::' . $this->getParam('method'), array($this));
		}
		return Response::error("Method missing");
	}

	private function array_copy(&$array) {
		$barr = array();
		$ex_pat1 = '/[^a-zA-Z_]/';
		$ex_pat2 = '/[^a-zA-Z0-9\.@_-]/';
		
		if (count($array) > 0) {
			foreach ($array as $key => $value) {
				$key = preg_replace($ex_pat1, '', $key); // remove non allowed chars
				if($key != 'jsonstring')
				{
					$value = preg_replace($ex_pat2, '', $value); // remove non allowed chars
					//$value = str_replace('.', '_', $value);
				}
				$barr[$key] = stripcslashes($value);
			}
		}
		return $barr;
	}
	
	/**
	 * 
	 * Get the hash of the entire query string upto "&hash.." and compare with the hash
	 */
	private function authenticate() {
		$qs = $this->postdata;
		//if($_SERVER["SERVER_NAME"]=="teamserver.re") return true;
		// strip query string from "&hash.."
		$qs = preg_replace("/&hash=.+/", '', $qs);
		$qs = $qs . $this->secret; // post-fix secret to the qs
		
		$hash = $this->getParam('hash');
		if ($hash != md5($qs)) {
			Response::error("Failed authentication");
		}
		
		// token is authorized, but the user is still not authenticated
		$user = new user();
		$name = $this->getParam('name');
		$pass = $this->getParam('pass');
		$time = $this->getParam('time');
		
		if (!$user->loginApi($name, $pass, $time)) {
			Response::error("Wrong credentials");
		}
	}

	public function get($key) {
		return isset( $this->get[$key] ) ? $this->get[$key] : null;
	}

	public function post($key) {
		return isset( $this->post[$key] ) ? $this->post[$key] : null;
	}

	public function getParam($key) {
		if ( $this->get($key) != null ) {
			return $this->get($key);
		}
		// return post value, post will return null if it doesn't exist
		return $this->post($key);
	}
}

class Response {
	public static function write($result) {
		$out = array(
			'data' => $result,
			'error' => '',
			'success' => true
		);
		echo json_encode($out);
		exit;
	}

	public static function error($result) {
		$out = array(
			'data' => '',
			'error' => $result,
			'success' => false
		);
		echo json_encode($out);
		exit;
	}
}

// Only these methods can be directly called from the REST API
class ApiMethods {
	public static function test($req) {
		Response::write(array("response" => "it just works!"));
	}
	
	/**
	 * State: working
	 *  
	 * Input:
	 * 	'name'
	 * 
	 * Output:
	 * 	"api":{"read":<<read access>>,"write":<<write access>>}
	 */
	public static function user_getAccessRights($req) {
		$api = new RemoteApi($req, false);
		$roles = array("api" => $api->getUserPermissions("api"));

		Response::write($roles); // only need to send the api access rights
	}
	
	/* Project API Methods */


	   
	/**
	 * State: working (Api Rights not handled)
	 * API Right: Read/Write
	 * Input: 
	 * 	'accesskey'
	 * Output:
	 * 	none
	 */
	public static function test_reset($req) {
		$accesskey = $req->getParam('accesskey');
	
		$project = new project();
		$user = new user();
		$client = new client();
		if($p = $project->getProjectFromAK($accesskey)) {
			$users = $project->getProjectMembers($p["ID"]);
			if(is_array($users) && count($users)>0) {
				foreach($users as $usr) {
					$user->del($usr["ID"]);
				}
			}
			$project->del($p["ID"]);
		}
	
		$client->delByName("API");
		$client_id = $client->add("API");
	
		$role = new roles();
		$role->delByName("API No Access");
		$role_no = $role->add("API No Access", array(), array(), array(), array(), array(), array(), array(), array(), array("read" => 0, "write" => 0), $client_id);
		$role->delByName("API Read");
		$role_read = $role->add("API Read", array(), array(), array(), array(), array(), array(), array(), array(), array("read" => 1, "write" => 0), $client_id);
		$role->delByName("API Read/Write");
		$role_write = $role->add("API Read/Write", array(), array(), array(), array(), array(), array(), array(), array(), array("read" => 1, "write" => 1), $client_id);
		
		$project_id = $project->add("TestProject", "TestProjectDesc", "2010-10-30", 100, 0, "2010-10-01", $accesskey);
		
		$user_rw_id = $user->add("TestProjectManager", "TestProjectManager@2-plan.com", "abc inc.", "%TestProjectManager%", "en", "", 0, "1233456789", "123456789");
		$project->assign($user_rw_id, $project_id);
		$role->assign($role_write, $user_rw_id);
		$client->assign($client_id, $user_rw_id);
		
		$user_read_id = $user->add("TestManager", "TestManager@2-plan.com", "", "%TestManager%", "en", "", 0, "1233456789", "123456789");
		$project->assign($user_read_id, $project_id);
		$role->assign($role_read, $user_read_id);
		$client->assign($client_id, $user_read_id);
		
		$user_no_id = $user->add("User", "User@2-plan.com", "", "%User%", "en", "", 0, "", "");
		$project->assign($user_no_id, $project_id);
		$role->assign($role_no, $user_no_id);
		$client->assign($client_id, $user_no_id);
		
		$user->delByEmail("NonMember@2-plan.com");
		$user_id = $user->add("NonMember", "NonMember@2-plan.com", "", "%NonMember%", "en", "", 0, "", "");
		$client->assign($client_id, $user_id);
		
		$user_ext_id = $user->add("ExternalProjectManager", "ExternalProjectManager@abc.com", "", "%ExternalProjectManager%", "en", "", 0, "", "");
		$role->assign($role_write, $user_ext_id);
		
		$milestone = new milestone();
		$milestone_id = $milestone->add($project_id, "Test Milestone", "Test Milestone Desc", "2010-10-28", 1, 0, "");
		$milestone_closed_id = $milestone->add($project_id, "Test Milestone Closed", "Test Milestone Closed Desc", "2010-10-19", 1, 1, "");
		
		$workpackage = new tasklist();
		$workpackage_id = $workpackage->add_workpackage($project_id, "TestWorkpackage", "TestWorkpackageDesc", 0, 0, 90, "2010-10-01", "2010-10-26", 10);
		$workpackage_closed_id = $workpackage->add_workpackage($project_id, "TestWorkpackageClosed", "TestWorkpackageClosedDesc", 0, 0, 134, "2010-10-05", "2010-10-29", 16);
		
		$task = new task();
		$task_id1 = $task->add("2010-10-22", "TestTaskOne", "TestTaskOneText", $workpackage_id, $project_id, 50, 1, 1);
		$task_id2 = $task->add("2010-10-26", "TestTaskTwo", "TestTaskTwoText", $workpackage_id, $project_id, 35, 1, 2);
		$task_id3 = $task->add("2010-10-07", "TestTask3", "TestTask3Text", $workpackage_closed_id, $project_id, 45, 1, 1);
		
		$timetracker = new timetracker();
		$timetracker->add($user_rw_id, $project_id, $task_id1, "ok 1", "10:20", "12:20", "2010-10-21");
		$timetracker->add($user_read_id, $project_id, $task_id1, "ok 2", "12:56", "17:23", "2010-10-22");
		$timetracker->add($user_rw_id, $project_id, $task_id2, "ok 3", "14:11", "15:12", "2010-10-23");
		$timetracker->add($user_rw_id, $project_id, $task_id3, "ok 3", "04:16", "05:16", "2010-10-10");
		
		$milestone->close($milestone_closed_id);
		$workpackage->close_workpackage($workpackage_closed_id);
		
		Response::write(array("response" => "test project reseted"));
	}

	
	/**
	 * State: 'actual' and "completed' not implemented
	 * Api Rights not handled
	 * 
	 * Returns project information. This method also returns some calculated values regarding the project progress.
	 * 
	 * API Right: Read
	 * 
	 * Input:
	 * 	'accesskey'
	 * Output:
	 * 'name'
	 * 'desc'
	 * 'planeffort'
	 * 'start'
	 * 'end'
	 * 'status'
	 * 'actual'   	#number of hours booked on all workpackages
	 * 'completed'  #calculated complete value [0..100]  (50 -> 50%)
	 *  
	 */
	public static function project_get($req) {
		$api = new RemoteApi($req);
		$api->access("api", "read");
		
		Response::write($api->project());
	}

	public static function project_edit($req) {}
	public static function project_create($req) {}
	public static function project_delete($req) {}
	
	/**
	 * State: working (missing user rights)
	 * 
	 * Set the UUID of a project. This feature is currently not used!
	 * 
	 * API Right: Read/Write
	 * 
	 * Input: 
	 * 	'accesskey'
	 * 	'uuid'  UUID which should be stored in the project table
	 * 
	 * Output:
	 * 	none
	 */
	public static function project_connect($req) {
		$api = new RemoteApi($req);
		$api->access("api", "read", "write");
		
		$result = $api->projectUpdateUuid($req->getParam('uuid'));
		
		Response::write($result);
	}
	
	/* Tasks API Methods */
	
	/**
	 * Status: working but...
	 * 	+ liste should be renamed to workpackage and should contain the UUID of the workpackage 
	 * 	- project should be the UUID and not the ID
	 * 	- User rights not handled
	 * 
	 * Returns all Tasks of a project
	 * 
	 * API Rights: Read
	 * 
	 * Input:
	 * 	'accesskey'
	 * Output for each task:
	 * 'ID'
	 * 'start'
	 * 'end'
	 * 'title'
	 * 'text'
	 * 'workpackage'
	 * 'status'
	 * 'project'
	 * 'priority'
	 * 'efforttocomplete'
	 * 'optionaletc'
	 * 
	 */
	public static function task_getAll($req) {
		$api = new RemoteApi($req);
		$api->access("api", "read");
		
		$tasks = $api->projectTasks();
		
		Response::write($tasks);
	}
	
	/**
	 *	Status: Not Working. uuid and ID not handled correctly.
	 *
	 * 	API Rights: Read
	 *
	 *	Input:
	 *	'accesskey'
	 *	'uuid'  the UUID of the workpackage (each workpackage which was created by the API has a UUID)
	 *   OR (!!)
	 *   'ID'   the ID of the workpackage
	 *
	 *	Output:
	 *	@see task_getAll	 
	 */
	public static function task_getAllInWorkpackage($req) {
		$api = new RemoteApi($req);
		$api->access("api", "read");

		/* uuid or ID will be passed! */
		$uuid = $req->getParam('uuid');
		$wid = $req->getParam('ID');
		/* here we have to check if getParam('ID) return != null */
		
		$id = $api->existWorkpackage($uuid, $wid);
		$tasks = $api->workpackageTasks($id);
		
		Response::write($tasks);
	}
	
	public static function task_add($req) {}
	public static function task_edit($req) {}
	public static function task_delete($req) {}
	
	/* Timetracker API Methods */
	public static function timetracker_get($req) {}
	public static function timetracker_add($req) {}
	public static function timetracker_edit($req) {}
	public static function timetracker_delete($req) {}
	
	/**
	 *	Status: still not tested.
	 *
	 * 	API Rights: Read
	 *
	 *	Input:
	 *	'accesskey'
	 *	'uuid'  the UUID of the workpackage (each workpackage which was created by the API has a UUID)
	 *   OR
	 *   'ID'   the ID of the workpackage
	 *   
	 *   Output:
	 *   all timetracker fields
	 */
	public static function timetracker_getAllInWorkpackage($req)
	{
		$accesskey = $req->getParam('accesskey');
		$uuid = $req->getParam('uuid');

		$project = new project();
		$project_id = $project->getProjectID($accesskey);

		$tasklist = new tasklist();
		$id = $tasklist->getID($uuid, $project_id); // send project_id as a security check

		$tasks = array();
		$timetracker = new timetracker();
		
		if (! empty($id)) {
			$tasks = $tasklist->getTasksFromList($id, $status = 1);
			$timetrackers = array();
			foreach($tasks as $task)
			{
				$temp = $timetracker->getProjectTrack($project_id, $task['id']);
				$timetrackers = array_merge($timetrackers, $temp);
			}
			Response::write($timetrackers);
		}
		else {
			Response::error(array("message" => "Incorrect UUID"));
		}
		
	}
	/**
	 *	Status: still not tested.
	 *
	 * 	API Rights: Read
	 *
	 *	Input:
	 *   'ID'   the ID of the task
	 *   
	 *   Output:
	 *   all timetracker fields
	 */
	public static function timetracker_getAllInTask($req) {
		$api = new RemoteApi($req);
		$api->access("api", "read");
		$api->access("timetracker", "read");
		$api->existTask($req->getParam('ID'));
		
		$timetrackers = $api->timetrackerTask($req->getParam('ID'));
		
		Response::write($timetrackers);
	}
	
	/* User API Methods */
	 /**
	 *	Status: Returns too much information (also the Roles)
	 *
	 * 	API Rights: Read
	 *
	 *	Input:
	 *	'accesskey'
	 *
	 *  Output:
	 *  All user fields but without any assignments or roles
	 */
	public static function user_getAllInProject($req) {
		$api = new RemoteApi($req);
		$api->access("api", "read");
		
		$HARD_LIMIT = 1000; // max members/users to return
		
		$users = $api->projectUsers($HARD_LIMIT);
		
		if(empty($users))
			Response::error("Missing Access");
		Response::write($users);
	}
	
	public static function user_add($req) {}
	public static function user_edit($req) {}
	public static function user_delete($req) {}
	
	/* Milestone API Methods */
	/**
	 * Returns all milestones assigned to the project.
	 * Status: not tested - but parameter uuid not needed, missing parameter 'status'
	 * 
	 * API Rights: Read
	 * 
	 * Input:
	 * 'accesskey'
	 * 'status'  (0) return all Milestones in State 0   
	 * 			 (1) return all Milestones in State 1
	 * 			 (2) return all Milestones
	 * 
	 * Output:
	 * 'ID'
	 * 'project'
	 * 'name'
	 * 'desc'
	 * 'start' / 'end'  <---- is the field 'start' used anywhere??? In the UI a milestone only have one date.
	 * 'status'
	 * 'external'
	 * 'uuid'
	 */
	public static function milestone_getAllInProject($req) {
		$HARD_LIMIT = 1000; // max members/users to return
		
		$accesskey = $req->getParam('accesskey');
		$uuid = $req->getParam('uuid');

		$project = new project();
		$project_id = $project->getProjectID($accesskey);
		
		$milestone = new milestone();
		$milestones = $milestone->getAllProjectMilestones($project_id, $HARD_LIMIT);
		
		Response::write($milestones);
	}
	
	
	 
	/**
	 * Status: not tested
	 * Add a milestone to the project
	 * API Rights: Read/Write
	 * 
	 */
	public static function milestone_add($req) {
		$api = new RemoteApi($req);
		$api->access("api", "read", "write");

		$text = $req->getParam('jsonstring');
		$ms = json_decode($text, true);
		if($api->milestoneIDByUuid($ms['uuid'])) {
			return Response::error("Milestone already exists");
		} else {
			$mileobj = new milestone();
			$mile_id = $mileobj->add($api->projectID(),$ms['name'], $ms['desc'], $ms['date'], $ms['status'], $ms['external'], $ms['uuid']);
		}

		Response::write($mile_id);
	}
	public static function milestone_edit($req) {}
	public static function milestone_delete($req) {}
	
	/* Workpackage API Methods */
	
	/**
	* Status: returns only open workpackages. Should handle "state" information
	* 
	* API Rights: Read
	* 
	* Input:
	*  'accesskey'
	* 	'state'  (0 = Finished, 1 = Active, 2 = All)
	* 
	* Return:
	* All fields from table workpackage
	* 
	*/
	public static function workpackage_getAllInProject($req) {
		$api = new RemoteApi($req);
		$api->access("api", "read");
		$api->access("api", "read");
		
		$workpackages = $api->projectWorkpackages($req->getParam('state'));
		
		Response::write($workpackages);
	}
	
	
	/**
	 * @deprecated
	 */
	public static function workpackage_get($req) {
		$uuid = $req->getParam('uuid');
		$workpackage = new tasklist();
		$result = $workpackage->getProjectTasklists($uuid);
		Response::write($result);
	}
	/**
	 * Status: Should check if user has write access
	 * 
	 * Add a workpackage to the project
	 * API Rights: Read/Write
	 * Input:
	 * 'accesskey'
	 * json decoded workpackage
	 * 
	 */
	public static function workpackage_add($req) {
		$api = new RemoteApi($req);
		$api->access("api", "read", "write");

		$text = $req->getParam('jsonstring');
		$wp = json_decode($text, true);
		
		if($api->workpackageIDByUuid($wp['uuid'])) {
			return Response::error("Workpackage already exists");
		} else {
			$workpackage = new tasklist();
			$workpackage_id = $workpackage->add_workpackage($api->projectID(), $wp['name'], $wp['desc'], $wp['access'], $wp['milestone'], $wp['planeffort'], $wp['startdate'], $wp['finishdate'], $wp['responsible'], $wp['uuid']);
		}
		if($workpackage_id) {
			if($wp["milestone"]!="") {
				$mile = json_decode($wp["milestone"], true);
				if(!($mile_id = $api->milestoneIDByUuid($mile["uuid"]))) {
					$mileobj = new milestone();
					$mile_id = $mileobj->add($api->projectID(),$mile["name"],$mile["desc"],$mile["date"],$mile["status"],$mile["external"],$mile["uuid"]);
				}
				$api->workpackageAssignMilestone($workpackage_id, $mile_id);
			}
		}
		
		Response::write($workpackage_id);
	}
	public static function workpackage_edit($req) {}
	public static function workpackage_delete($req) {}
	
	public static function workpackage_assignMilestone($req){
		
	}
}
