<?php
/**
 * Provides methods to interact with users
 *
 * @author original code from Open Dynamics.
 * @name user
 * @version 0.4.7
 * @package 2-plan
 * @link http://2-plan.com
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License v3 or laterg
 */
class user extends TableBase
{
    public $mylog;

    /**
     * Konstruktor
     * Initialisiert den Eventlog
     */
    function __construct()
    {
        $this->mylog = new mylog;
        $this->table_name = 'user';
    }

    /**
     * Creates a user
     *
     * @param string $name Name of the member
     * @param string $email Email Address of the member
     * @param string $company Company Nmae of the member
     * @param string $pass Password
     * @param string $locale Localisation
     * @return int $insid ID of the newly created member
     */
    function add($name, $email, $company, $pass, $locale = "", $tags = "", $rate = 0.0, $tel1="", $tel2="")
    {
        $name = mysql_real_escape_string($name);
        $email = mysql_real_escape_string($email);
        $company = mysql_real_escape_string($company);
        $pass = mysql_real_escape_string($pass);
        $locale = mysql_real_escape_string($locale);
        $tags = mysql_real_escape_string($tags);
        $rate = (float) $rate;
        $tel1 = mysql_real_escape_string($tel1);
        $tel2 = mysql_real_escape_string($tel2);

        $pass = sha1($pass);
        $hash = md5($name.time().$pass.rand(128,512).$email);

        $ins1 = mysql_query("INSERT INTO ".$this->getTableName()." (name,email,company,pass,locale,tags,rate,tel1,tel2,hash) VALUES ('$name','$email','$company','$pass','$locale','$tags','$rate','$tel1','$tel2','$hash')");
        if ($ins1)
        {
            $insid = mysql_insert_id();
            $this->mylog->add($name, 'user', 1, 0);
            return $insid;
        }
        else
        {
            return false;
        }
    }

    /**
     * Edits a member
     *
     * @param int $id Member ID
     * @param string $name Member name
     * @param string $realname realname
     * @param string $role role
     * @param string $email Email
     * @param int $company Company ID of the member (unused)
     * @param string $zip ZIP-Code
     * @param string $gender Gender
     * @param string $url URL
     * @param string $address1 Adressline1
     * @param string $address2 Addressline2
     * @param string $state State
     * @param string $country Country
     * @param string $locale Localisation
     * @param string $avatar Avatar
     * @return bool
     */
    function edit($id, $name, $realname, $email, $tel1, $tel2, $company, $zip, $gender, $url, $address1, $address2, $state, $country, $tags, $locale, $avatar = "", $rate = 0.0)
    {
        $name = mysql_real_escape_string($name);
        $realname = mysql_real_escape_string($realname);
        $job = mysql_real_escape_string($role);
        $email = mysql_real_escape_string($email);
        $zip = mysql_real_escape_string($zip);
        $gender = mysql_real_escape_string($gender);
        $url = mysql_real_escape_string($url);
        $address1 = mysql_real_escape_string($address1);
        $address2 = mysql_real_escape_string($address2);
        $state = mysql_real_escape_string($state);
        $country = mysql_real_escape_string($country);
        $locale = mysql_real_escape_string($locale);
        $avatar = mysql_real_escape_string($avatar);

        $rate = (float) $rate;
        $id = (int) $id;
       // $company = (int) $company;

        if ($avatar != "")
        {
            $upd = mysql_query("UPDATE ".$this->getTableName()." SET name='$name',email='$email',tel1='$tel1', tel2='$tel2',company='$company',zip='$zip',gender='$gender',url='$url',adress='$address1',adress2='$address2',state='$state',country='$country',tags='$tags',locale='$locale',avatar='$avatar',rate='$rate' WHERE ID = $id");
        }
        else
        {
            // realname='$realname',,role='$role'
            $upd = mysql_query("UPDATE ".$this->getTableName()." SET name='$name',email='$email', tel1='$tel1', tel2='$tel2', company='$company',zip='$zip',gender='$gender',url='$url',adress='$address1',adress2='$address2',state='$state',country='$country',tags='$tags',locale='$locale',rate='$rate' WHERE ID = $id");
        }
        if ($upd)
        {
            $this->mylog->add($name, 'user', 2, 0);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Change a password
     *
     * @param int $id Eindeutige Mitgliedsnummer
     * @param string $oldpass Altes Passwort
     * @param string $newpass Neues Passwort
     * @param string $repeatpass Repetition of the new password
     * @return bool
     */
    function editpass($id, $oldpass, $newpass, $repeatpass)
    {
        $oldpass = mysql_real_escape_string($oldpass);
        $newpass = mysql_real_escape_string($newpass);
        $repeatpass = mysql_real_escape_string($repeatpass);
        $id = (int) $id;

        if ($newpass != $repeatpass)
        {
            return false;
        }
        $id = mysql_real_escape_string($id);
        $newpass = sha1($newpass);

        $oldpass = sha1($oldpass);
        $chk = mysql_query("SELECT ID, name FROM ".$this->getTableName()." WHERE ID = $id AND pass = '$oldpass'");
        $chk = mysql_fetch_row($chk);
        $chk = $chk[0];
        $name = $chk[1];
        if (!$chk)
        {
            return false;
        }

        $upd = mysql_query("UPDATE ".$this->getTableName()." SET pass='$newpass' WHERE ID = $id");
        if ($upd)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Change a password as admin
     *
     * @param int $id User ID
     * @param string $newpass New passwort
     * @param string $repeatpass Repetition of the new password
     * @return bool
     */
    function admin_editpass($id, $newpass, $repeatpass)
    {
        $newpass = mysql_real_escape_string($newpass);
        $repeatpass = mysql_real_escape_string($repeatpass);
        $id = (int) $id;

        if ($newpass != $repeatpass)
        {
            return false;
        }
        $id = mysql_real_escape_string($id);
        $newpass = sha1($newpass);

        $upd = mysql_query("UPDATE ".$this->getTableName()." SET pass='$newpass' WHERE ID = $id");
        if ($upd)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Delete a user
     *
     * @param int $id User ID
     * @return bool
     */
    function del($id)
    {
        $id = (int) $id;

        $chk = mysql_query("SELECT name FROM ".$this->getTableName()." WHERE ID = $id");
        $chk = mysql_fetch_row($chk);
        $name = $chk[0];

        $del = mysql_query("DELETE FROM ".$this->getTableName()." WHERE ID = $id");
        $del2 = mysql_query("DELETE FROM ".$this->getTablePrefix()."projekte_assigned WHERE user = $id");
        $del3 = mysql_query("DELETE FROM ".$this->getTablePrefix()."milestones_assigned WHERE user = $id");
        $del4 = mysql_query("DELETE FROM ".$this->getTablePrefix()."tasks_assigned WHERE user = $id");
        $del5 = mysql_query("DELETE FROM ".$this->getTablePrefix()."log WHERE user = $id");
        $del6 = mysql_query("DELETE FROM ".$this->getTablePrefix()."timetracker WHERE user = $id");
        $del7 = mysql_query("DELETE FROM ".$this->getTablePrefix()."roles_assigned WHERE user = $id");
        $del7 = mysql_query("DELETE FROM ".$this->getTablePrefix()."client_assigned WHERE user = $id");
        
        if ($del)
        {
            $this->mylog->add($name, 'user', 3, 0);
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Delete a users by E-Mail
     *
     * @param int $email User E-Mail
     * @return
     */
    function delByEmail($email)
    {
        $email = mysql_real_escape_string($email);
        
        $users_q = mysql_query("SELECT ID FROM ".$this->getTableName()." WHERE email = '$email'");
        while($user = mysql_fetch_assoc($users_q)) {
            $this->del($user["ID"]);
        }
    }

    /**
     * Get a user profile
     *
     * @param int $id User ID
     * @return array $profile Profile
     */
    function getProfile($id)
    {
        $id = (int) $id;

        $sel = mysql_query("SELECT * FROM ".$this->getTableName()." WHERE ID = $id");
        $profile = mysql_fetch_array($sel);
        if (!empty($profile))
        {
            $profile["name"] = stripslashes($profile["name"]);
            if (isset($profile["company"]))
            {
                $profile["company"] = stripslashes($profile["company"]);
            }
            if (isset($profile["adress"]))
            {
                $profile["adress"] = stripslashes($profile["adress"]);
            }
            if (isset($profile["adress2"]))
            {
                $profile["adress2"] = stripslashes($profile["adress2"]);
            }
            if (isset($profile["state"]))
            {
                $profile["state"] = stripslashes($profile["state"]);
            }
            if (isset($profile["country"]))
            {
                $profile["country"] = stripslashes($profile["country"]);
            }
            $tagsobj = new tags();
            $profile["tagsarr"] = $tagsobj->splitTagStr($profile["tags"]);

            $rolesobj = (object) new roles();
            $profile["role"] = $rolesobj->getUserRole($profile["ID"]);

            $clientobj = new client();
            $profile["client_id"] = $clientobj->getUserClient($profile["ID"]);
            $profile["client_path"] = $clientobj->getClientPath($profile["ID"]);

            return $profile;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get a user profile by email
     *
     * @param string $email User E-Mail
     * @return array $profile Profile
     */
    function getProfileByEmail($email)
    {
        $email = mysql_real_escape_string($email);

        if ($profile = mysql_fetch_assoc(mysql_query("SELECT ID FROM ".$this->getTableName()." WHERE email='$email'")))
        {
            return $this->getProfile($profile["ID"]);
        }
        else
        {
            return false;
        }
    }

    /**
     * Get a user profile by hash
     *
     * @param string $hash User Hash
     * @return int $profile_id Profile ID
     */
    function getProfileIDByHash($hash)
    {
        $hash = mysql_real_escape_string($hash);

        $profile = mysql_fetch_assoc(mysql_query("SELECT ID FROM ".$this->getTableName()." WHERE hash='$hash'"));
        return (int) $profile["ID"];
    }

    /**
     * Get a field of the user profile
     *
     * @param int $id User ID
     * @return string $value field value
     */
    function getProfileField($id, $field)
    {
        $id = (int) $id;
        
        $user = $this->getProfile($id);
        return $user[$field];
    }

    /**
     * Get the avatar of a user
     *
     * @param int $id User ID
     * @return array $profile Avatar
     */
    function getAvatar($id)
    {
        $id = (int) $id;

        $sel = mysql_query("SELECT avatar FROM ".$this->getTableName()." WHERE ID = $id");
        $profile = mysql_fetch_row($sel);
        $profile = $profile[0];

        if (!empty($profile))
        {
            return $profile;
        }
        else
        {
            return false;
        }
    }

    /**
     * Log a user in
     *
     * @param string $user User name
     * @param string $pass Password
     * @return bool
     */
    function login($user, $pass)
    {
        if (!$user)
        {
            return false;
        }
        $user = mysql_real_escape_string($user);
        $pass = mysql_real_escape_string($pass);
        $pass = sha1($pass);

        $chk = mysql_fetch_array(mysql_query("SELECT ID,name,locale,lastlogin,gender,hash FROM ".$this->getTableName()." WHERE ((name = '$user' AND ID=1) OR email = '$user') AND pass = '$pass'"));
        if ($chk["ID"] != "")
        {
            // check activity of the user client
            $clientobj = new client();
            if(!$clientobj->isActive($chk["ID"]))
                return false;
            
            $rolesobj = new roles();
            $now = time();
            $_SESSION['userid'] = $chk['ID'];
            $_SESSION['username'] = stripslashes($chk['name']);
            $_SESSION['userhash'] = $chk['hash'];
            $_SESSION['lastlogin'] = $now;
            $_SESSION['userlocale'] = $chk['locale'];
            $_SESSION['usergender'] = $chk['gender'];
            $_SESSION["userpermissions"] = $rolesobj->getUserRole($chk["ID"]);

            $userid = $_SESSION['userid'];
            $seid = session_id();
            $staylogged = getArrayVal($_POST, 'staylogged');

            if ($staylogged == 1)
            {
                setcookie("PHPSESSID", "$seid", time() + 14 * 24 * 3600);
            }
            $upd1 = mysql_query("UPDATE ".$this->getTableName()." SET lastlogin = '$now' WHERE ID = $userid");
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Logout
     *
     * @return bool
     */
    function logout()
    {
        session_start();
        session_destroy();
        session_unset();
        setcookie("PHPSESSID", "");
        return true;
    }

    /**
     * Returns all users
     *
     * @param int $lim Limit
     * @return array $users Registrierte Mitglieder
     */
    function getAllUsers($lim = 10)
    {
        global $userid, $userpermissions, $client_admin;
        $lim = (int) $lim;

        $sel = mysql_query("SELECT COUNT(*) FROM `".$this->getTableName()."`");
        $num = mysql_fetch_row($sel);
        $num = $num[0];
        SmartyPaginate::connect();
        // set items per page
        SmartyPaginate::setLimit($lim);
        SmartyPaginate::setTotal($num);

        $start = SmartyPaginate::getCurrentIndex();
        $lim = SmartyPaginate::getLimit();

        // restriction for non-root users
        if(!$userpermissions["admin"]["root"]) {
            if(!$client_admin)
                $client_admin = $this->getProfileField($userid, "client_id");
            $sel2 = mysql_query("SELECT u.* FROM `".$this->getTableName()."` u LEFT JOIN ".$this->getTablePrefix()."client_assigned ca ON ca.user=u.ID WHERE ca.client='$client_admin' ORDER BY u.ID DESC LIMIT $start,$lim");
        } else {
            $sel2 = mysql_query("SELECT * FROM `".$this->getTableName()."`$sql_where ORDER BY ID DESC LIMIT $start,$lim");
        }
        
        $users = array();
        while ($user = mysql_fetch_array($sel2))
        {
            $user["name"] = stripslashes($user["name"]);
            $user["company"] = stripslashes($user["company"]);
            $user["adress"] = stripslashes($user["adress"]);
            $user["adress2"] = stripslashes($user["adress2"]);
            $user["state"] = stripslashes($user["state"]);
            $user["country"] = stripslashes($user["country"]);
            array_push($users, $user);
        }

        if (!empty($users))
        {
            return $users;
        }
        else
        {
            return false;
        }
    }

    function isOnline($user, $offset = 30)
    {
        $user = (int) $user;
        $offset = (int) $offset;

        $time = time();
        $now = $time - $offset;

        $sel = mysql_query("SELECT ID FROM ".$this->getTableName()." WHERE lastlogin >= $now AND ID = $user");
        $user = mysql_fetch_row($sel);

        if (!empty($user))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function getId($user){
        $user = mysql_real_escape_string($user);

        $user = mysql_fetch_assoc(mysql_query("SELECT ID FROM ".$this->getTableName()." WHERE email = '$user'"));
        return (int)$user["ID"];
    }
    
    /**
     * Authenticate a user for API use
     * <strong>pass</strong> is the SHA1 hash of password which is hashed again 
     * in MD5 with time as salt
     *  pass <- MD5 ( [SHA1 of password] + [salt] )
     *
     * @param string $user User name
     * @param string $pass Password
     * @return bool
     */
    function loginApi($user, $pass, $salt)
    {
        if (!$user)
        {
            return false;
        }
        $user = mysql_real_escape_string($user);
        $pass = mysql_real_escape_string($pass);
        $salt = mysql_real_escape_string($salt);

        $sel1 = mysql_query("SELECT ID,name,pass FROM ".$this->getTableName()." WHERE ((name = '$user' AND ID=1) OR email = '$user')");
        $chk = mysql_fetch_array($sel1);
        if ($chk["ID"] != "")
        {
            // check activity of the user client
            $clientobj = new client();
            if(!$clientobj->isActive($chk["ID"]))
                return false;
            
            $now = time();
            $user = array();
            $user['userid'] = $chk['ID'];
            $user['username'] = stripslashes($chk['name']);
            $user['pass'] = $chk['pass'];
            //$user['lastlogin'] = $now;
            //$_SESSION["userpermissions"] = $rolesobj->getUserRole($chk["ID"]);
            //$upd1 = mysql_query("UPDATE ".$this->getTableName()." SET lastlogin = '$now' WHERE ID = " . $user['userid']);
            
            $local_pass = md5($user['pass'] . $salt);
            //echo $local_pass;
            //echo $pass;
            if ($local_pass != $pass) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Set new password for the user
     *
     * @param int $id User ID
     * @param int $length password length
     * @return string $password new password
     */
    function setNewPassword($id, $length = 8)
    {
        $id = (int) $id;

        if ($profile = mysql_fetch_assoc(mysql_query("SELECT ID FROM ".$this->getTableName()." WHERE id='$id'")))
        {
            for($i=0;$i<$length;$i++) {
                if($i % rand(2,4) == 0)
                    $code = rand(48,57);
                elseif($i % rand(2,4) == 0)
                    $code = rand(65,90);
                else
                    $code = rand(97,122);
                $password .= chr($code);
            }
            $pass_q = mysql_query("UPDATE ".$this->getTableName()." SET pass='".sha1($password)."' WHERE ID='$id'");
            return $password;
        }
        else
        {
            return false;
        }
    }

    /**
     * Activate new registred user
     *
     * @param string $hash checking string
     * @return bool $result
     */
    function activate($hash)
    {
        $hash = mysql_real_escape_string($hash);

        if ($profile = mysql_fetch_assoc(mysql_query("SELECT ID FROM ".$this->getTableName()." WHERE MD5(CONCAT(name,'2p',pass,ID,email))='$hash'")))
        {
            if($client = mysql_fetch_assoc(mysql_query("SELECT c.ID FROM ".$this->getTablePrefix()."client c LEFT JOIN ".$this->getTablePrefix()."client_assigned ca ON ca.client=c.ID WHERE ca.user='".$profile["ID"]."'"))) {
                mysql_query("UPDATE ".$this->getTablePrefix()."client SET status='1' WHERE ID='".$client["ID"]."'");
                return true;
            } else {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
}

?>
