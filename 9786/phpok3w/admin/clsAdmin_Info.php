<?php
class Admin_Info
{
    var $userid;
    var $username;
    var $db;
    var $table_member;
    var $errmsg = errmsg;

    function Admin_Info()
    {
        global $db;
        $this->table_member = $db->pre . 'member';
        $this->db = & $db;
    }

    public  function AdminLogin($username,$password)
    {
        $db=GetConn();
        $password=md5($password);
        $SQL = "select * from Ok3w_Admin where adminname='$username' and adminpwd='$password'";
echo $SQL;
        $result = $db->query($SQL);
        $total=$result->num_rows ;
        if ($total == 0)
        {
           CloseConn($db);
        } else
        {
            $row =$result->fetch_array(MYSQLI_ASSOC);
            $_SESSION["username"] = $username;
            //echo $_SESSION["username"];
            return -1;
        }
    }


    function login($login_username, $login_password, $login_cookietime = 0, $admin = false)
    {
        global $DT_TIME, $DT_IP, $MOD, $MODULE, $L;
        if (!check_name($login_username)) return $this->_( '用户名格式错误' );
        $user = userinfo($login_username, 0);
        if (!$admin)
        {

            if ($user['adminpwd'] !=    md5($login_password) )
            {
                echo "password is increct";
                exit();
                return $this->_('密码错误,请重试');
            }
        }
        $adminid = $user['adminid'];



        $cookietime = $DT_TIME + ($login_cookietime ? intval($login_cookietime) : 86400 * 7);
        $auth = encrypt($user['adminid'] . "\t" . $user['adminname']."\t". $user['groupid'] ."\t". $user['adminpwd'] , md5(DT_KEY . $DT_IP));
        set_cookie('auth', $auth, $cookietime);
        set_cookie('adminid', $user['adminid'], $cookietime);
        set_cookie('adminname', $user['adminname'], $DT_TIME + 86400 * 365);
        $this->db->query("UPDATE {$this->table_member} SET loginip='$DT_IP',logintime=$DT_TIME,logintimes=logintimes+1 WHERE adminid=$adminid");
        return $user;
    }


    function login_log($username, $password, $admin = 0, $message = '')
    {
        global $DT_PRE, $DT_TIME, $DT_IP, $L;
        $password = is_md5($password) ? md5($password) : md5(md5($password));
        $agent = addslashes(htmlspecialchars(strip_sql($_SERVER['HTTP_USER_AGENT'])));
        $message or $message = $L['member_login_ok'];
        if ($message == $L['member_login_ok'])
            cache_delete($DT_IP . '.php', 'ban');

        $this->db->query("INSERT INTO {$DT_PRE}login_log (username,password,admin,loginip,logintime,message,agent) VALUES ('$username','$password','$admin','$DT_IP','$DT_TIME','$message','$agent')");
    }


    function _($e)
    {
        $this->errmsg = $e;
        return false;
    }


}

?>