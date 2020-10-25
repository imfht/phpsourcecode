<?php
/*
* This class provides methods to realize a client
*
* @author original code from Open Dynamics.
* @name client
* @version 0.4
* @package 2-plan
* @link http://2-plan.com
* @license http://opensource.org/licenses/gpl-license.php GNU General Public License v3 or later
*/

class client extends TableBase
{
    /*
    * Constructor
    * Initialize the event log
    */
    function __construct()
    {
    	$this->table_name = 'client';
    	$this->mylog = new mylog;
    }

    /*
    * Add a new client
    *
    * @param string $name Name of the client
    * @param string $email Email-address
    * @param string $phone Phonenumber
    * @param string $address1 Main address
    * @param string $address2 Second address
    * @param string $state State
    * @param string $country Country
    * @param string $logo Client's logo
    * @param int $status Client's status
    * @param int $numberusers Max Number of users
    * @param int $numberprojects Max Number of projects
    * @param int $validuntildate last date when client is active
    * @return bool
    */
    function add($name, $email="", $phone="", $address1="", $address2="", $state="", $country="", $logo="", $status=1, $numberusers=0, $numberprojects=0, $validuntildate=0)
    {
        $name = mysql_real_escape_string($name);
        $email = mysql_real_escape_string($email);
        $phone = mysql_real_escape_string($phone);
        $address1 = mysql_real_escape_string($address1);
        $address2 = mysql_real_escape_string($address2);
        $state = mysql_real_escape_string($state);
        $country = mysql_real_escape_string($country);
        $logo = mysql_real_escape_string($logo);
        $status = (int)$status;
        $numberusers = (int)$numberusers;
        $numberprojects = (int)$numberprojects;
        $validuntildate = (int)$validuntildate;
        if($validuntildate>0)
            $validuntildate = date("Y-m-d H:i:s", time()+$validuntildate*86400);
        
        $ins1 = mysql_query("INSERT INTO ".$this->getTableName()." (name, email, phone, address1, address2, state, country, logo, status, numberusers, numberprojects, validuntildate) VALUES ('$name', '$email', '$phone', '$address1', '$address2', '$state', '$country', '$logo', '$status', '$numberusers', '$numberprojects', '$validuntildate')");
        if ($ins1)
        {
            $path_name = $this->clientPathFormat($name);
            if(!file_exists(CL_ROOT . "/files/" . CL_CONFIG . "$path_name"))
                mkdir(CL_ROOT . "/files/" . CL_CONFIG . "$path_name", 0777);
            return mysql_insert_id();
        }
        else
        {
            return false;
        }
    }

    /*
    * Edit a client
    *
    * @param int $id Client ID
    * @param string $name Client's name
    * @param string $email Emal address
    * @param string $address1 Main address
    * @param string $address2 Second address
    * @param string $state State
    * @param string $country Country
    * @param string $logo Client's logo
    * @return bool
    */
    function edit($id, $name, $email, $phone, $address1, $address2, $state, $country, $logo)
    {
        $id = (int) $id;
        $name = mysql_real_escape_string($name);
        $email = mysql_real_escape_string($email);
        $phone = mysql_real_escape_string($phone);
        $address1 = mysql_real_escape_string($address1);
        $address2 = mysql_real_escape_string($address2);
        $state = mysql_real_escape_string($state);
        $country = mysql_real_escape_string($country);
        $logo = mysql_real_escape_string($logo);

        $upd = mysql_query("UPDATE ".$this->getTableName()." SET name='$name', email='$email', phone='$phone', address1='$address1', address2='$address2', state='$state', country='$country', logo='$logo' WHERE ID = $id");
        if ($upd)
        {
            $this->mylog->add('client', 2);
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
    * Delete a client
    *
    * @param int $id Client ID
    * @return bool
    */
    function del($id)
    {
        $id = (int) $id;
        $del = mysql_query("DELETE FROM ".$this->getTableName()." WHERE ID = $id");
        if ($del)
        {
            $this->mylog->add('client', 3);
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
    * Delete a client by name
    *
    * @param int $name Client name
    * @return
    */
    function delByName($name)
    {
        $name = mysql_real_escape_string($name);
        $clients_q = mysql_query("SELECT ID FROM ".$this->getTableName()." WHERE name='$name'");
        while($client = mysql_fetch_assoc($clients_q)) {
            $this->del($client["ID"]);
        }
    }

	/*
    * Assign a user to a client
    *
    * @param int client Client ID
    * @param int user User ID
    * @param int $id Client ID
    * @return bool
    */
	function assign($client,$user)
	{
		$client = (int) $client;
		$user = (int) $user;
		
		if($ca = mysql_fetch_object(mysql_query("SELECT * FROM ".$this->getTablePrefix()."client_assigned WHERE user='$user'"))) {
			$ins = mysql_query("UPDATE ".$this->getTablePrefix()."client_assigned SET client='$client' WHERE ID='$ca->ID'");
		} else {
			$ins = mysql_query("INSERT INTO ".$this->getTablePrefix()."client_assigned (user,client) VALUES ($user,$client)");
		}
		if($ins)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/*
    * Remove a user from a client
    *
    * @param int client Client ID
    * @param int user User ID
    * @param int $id Client ID
    * @return bool
    */
	function deassign($client,$user)
	{
		$client = (int) $client;
		$user = (int) $user;
		
		$ins = mysql_query("UPDATE ".$this->getTablePrefix()."user SET client=0 WHERE ID = $id");
		if($ins)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
    /*
    * Return the profile of a client
    *
    * @param int $id Client ID
    * @return bool
    */
    function getProfile($id)
    {
        $id = (int) $id;

        $sel = mysql_query("SELECT * FROM ".$this->getTableName()." WHERE ID = $id");
        $profile = mysql_fetch_array($sel);


        if (!empty($profile))
        {
            return $profile;
        }
        else
        {
            return false;
        }
    }

	function getAllClients()
	{
		$sel = mysql_query("SELECT * FROM ".$this->getTableName());
		$clients = array();
		
		while($client = mysql_fetch_array($sel))
		{
			array_push($clients,$client);
		}
		
		if(!empty($clients))
		{
			return $clients;
		}
		else
		{
			return false;
		}
	}
	
	function getUserClient($id)
	{
		$id = (int) $id;
		$cu = mysql_fetch_assoc(mysql_query("SELECT client FROM ".$this->getTablePrefix()."client_assigned WHERE user='$id'"));
		return (int)$cu["client"];
	}
	
	function getClientPath($user_id)
	{
		$client_id = $this->getUserClient($user_id);
		$client = mysql_fetch_assoc(mysql_query("SELECT name FROM ".$this->getTableName()." WHERE ID='$client_id'"));
		return $this->clientPathFormat($client["name"]);
	}
	
	function clientPathFormat($name)
	{
		$path = $name!=""?"/".$name:"";
		$path = strtolower($path);
		$path = str_replace("@", "_", $path);
		return $path;
	}
	
	/*
	* Return all members of a client
	*
	* @param int $id Client ID
	* @return array $staff Members of the client
	*/
	function getClientMembers($id)
	{
		$id = (int) $id;

		$sel = mysql_query("SELECT user,client FROM ".$this->getTablePrefix()."client_assigned WHERE client = $id");
		$staff = array();
		$userobj = (object) new user();
		$client = $this->getProfile($member[1]);
		while($member = mysql_fetch_row($sel))
		{
			$user = $userobj->getProfile($member[0]);
			array_push($staff,$user); 
		}
		$client["staff"] = $staff;

		if (!empty($client))
		{
			return $client;
		}
		else
		{
			return false;
		}
	}
	
	/*
	* Return activity of user in the client
	*
	* @param int $id User ID
	* @return bool $is_active user activity
	*/
	function isActive($id)
	{
		global $userpermissions;
		
		if(isset($userpermissions)) {
			if($userpermissions["admin"]["root"])
				return true;
		} else {
			$rolesobj = new roles();
			$permissions = $rolesobj->getUserRole($id);
			if($permissions["admin"]["root"])
				return true;
		}
		
		$id = (int) $id;
		$client = mysql_fetch_assoc(mysql_query("SELECT c.ID FROM ".$this->getTableName()." c LEFT JOIN ".$this->getTablePrefix()."client_assigned ca ON c.ID=ca.client WHERE ca.user='$id' AND (c.validuntildate=0 OR c.validuntildate>'".date("Y-m-d H:i:s")."')"));
		return (bool)$client;
	}
	
	/*
	* Return enable to add of new users
	*
	* @param int $id User ID
	* @return bool $enabled
	*/
	function enabledAddUsers($id) {
		global $userpermissions;
		if($userpermissions["admin"]["root"])
			return true;
		
		$id = (int) $id;
		$client = mysql_fetch_assoc(mysql_query("SELECT c.ID, c.numberusers FROM ".$this->getTableName()." c LEFT JOIN ".$this->getTablePrefix()."client_assigned ca ON c.ID=ca.client WHERE ca.user='$id'"));
		if($client["numberusers"]==0)// unlimit users
			return true;
		
		$users = mysql_fetch_assoc(mysql_query("SELECT COUNT(*) AS number FROM ".$this->getTablePrefix()."client_assigned WHERE client='{$client["ID"]}'"));
		return $client["numberusers"]>$users["number"] ? true : false;
	}
	
	/*
	* Return enable to add of new projects
	*
	* @param int $id User ID
	* @return bool $enabled
	*/
	function enabledAddProjects($id) {
		global $userpermissions;
		if($userpermissions["admin"]["root"])
			return true;
		
		$id = (int) $id;
		$client = mysql_fetch_assoc(mysql_query("SELECT c.ID, c.numberprojects FROM ".$this->getTableName()." c LEFT JOIN ".$this->getTablePrefix()."client_assigned ca ON c.ID=ca.client WHERE ca.user='$id'"));
		if($client["numberprojects"]==0)// unlimit projects
			return true;
		
		$users_q = mysql_query("SELECT user FROM ".$this->getTablePrefix()."client_assigned WHERE client='{$client["ID"]}'");
		$users = array();
		while($user = mysql_fetch_assoc($users_q))
			$users[] = $user["user"];
		$projects = mysql_num_rows(mysql_query("SELECT DISTINCT projekt FROM ".$this->getTablePrefix()."projekte_assigned WHERE user IN('".join("', '",$users)."')"));
		return $client["numberprojects"]>$projects ? true : false;
	}
}
?>
