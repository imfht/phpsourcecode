<?php
/*
 * The class 'settings' provides methods to deal with the global system settings
 *
 * @author original code from Open Dynamics.
 * @name settings
 * @package 2-plan
 * @version 0.4.9
 * @link http://2-plan.com
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License v3 or later
 */
class settings extends TableBase
{
    public $mylog;

    /*
     * Constructor
     */
    function __construct()
    {
    	$this->table_name = 'settings';
    }

    /*
     * Returns all global settings
     *
     * @return array $settings Global system settings
     */
    function getSettings()
    {
        global $userid;
        $user = new user();
        $client = $user->getProfileField($userid, "client_id");
        
        $settings = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$this->getTablePrefix()."settings_global"));
        if(!($settings_client = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$this->getTableName()." WHERE client='$client'")))) {
           $settings_client = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$this->getTableName()." WHERE client='0'"));
        }
        $settings = array_merge($settings, $settings_client);

        if (!empty($settings))
        {
            return $settings;
        }
        else
        {
            return false;
        }
    }

    /*
     * Edits the global system settings
     *
     * @param string $name System name
     * @param string $subtitle Subtitle is displayed under the system name
     * @param string $locale Standard locale
     * @param string $timezone Standard timezone
     * @param string $templ Template
     * @param string $rssuser Username for RSS Feed access
     * @param string $rsspass Password for RSS Feed access
     * @return bool
     */
    function editSettings($name, $subtitle, $locale, $timezone, $dateformat, $templ, $rssuser, $rsspass, $client_id = 0)
    {
        global $userid;
        $user = new user();
        $client = $client_id>0 ? $client_id : $user->getProfileField($userid, "client_id");

        $name = mysql_real_escape_string($name);
        $subtitle = mysql_real_escape_string($subtitle);
        $locale = mysql_real_escape_string($locale);
        $timezone = mysql_real_escape_string($timezone);
        $dateformat = mysql_real_escape_string($dateformat);
        $templ = mysql_real_escape_string($templ);
        $sounds = mysql_real_escape_string($sounds);
        $rssuser = mysql_real_escape_string($rssuser);
        $rsspass = mysql_real_escape_string($rsspass);

        if(mysql_fetch_assoc(mysql_query("SELECT ID FROM ".$this->getTableName()." WHERE client='$client'")))
            $upd = mysql_query("UPDATE ".$this->getTableName()." SET name='$name', subtitle='$subtitle', `locale`='$locale', `timezone`='$timezone', `dateformat`='$dateformat', `template`='$templ', rssuser='$rssuser',rsspass='$rsspass' WHERE client='$client'");
        else
            $upd = mysql_query("INSERT INTO ".$this->getTableName()." (client,name,subtitle,`locale`,`timezone`,`dateformat`,`template`,rssuser,rsspass) VALUES ('$client','$name','$subtitle','$locale','$timezone','$dateformat','$templ','$rssuser','$rsspass')");

        if ($upd)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     * Edits the global mail notification settings
     *
     * @param int $onoff 1 = nofitications on, 0 = notifications off
     * @param string $mailfrom Sender
     * @param string $mailfromname Name of the sender
     * @param string $method Method (e.g. SMTP)
     * @param string $mailhost Host
     * @param string $mailuser User
	 * @param string $mailpass Password
     * @return bool
     */
	function editMailsettings($onoff,$mailfrom,$mailfromname,$method,$mailhost,$mailuser,$mailpass)
	{
		global $userpermissions;
		if(!$userpermissions["admin"]["root"])
			return false;
		
		$onoff = (int) $onoff;
		$mailfrom = mysql_real_escape_string($mailfrom);
		$mailfromname = mysql_real_escape_string($mailfromname);
		$method =  mysql_real_escape_string($method);
		$mailhost = mysql_real_escape_string($mailhost);
		$mailuser = mysql_real_escape_string($mailuser);
		$mailpass = mysql_real_escape_string($mailpass);

		$upd = mysql_query("UPDATE ".$this->getTablePrefix()."settings_global SET mailnotify=$onoff,mailfrom='$mailfrom',mailfromname='$mailfromname',mailmethod='$method',mailhost='$mailhost',mailuser='$mailuser',mailpass='$mailpass'");
		if($upd)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

    /*
     * Returns all available templates
     *
     * @return array $templates
     */
    function getTemplates()
    {
        $handle = opendir(CL_ROOT . "/templates");
        $templates = array();
        while (false !== ($file = readdir($handle)))
        {
            $type = filetype(CL_ROOT . "/templates/" . $file);
            if ($type == "dir" && !in_array($file, array(".","..",".svn","iphone","bb")))
            {
                $template = $file;
                array_push($templates, $template);
            }
        }
        if (!empty($templates))
        {
            return $templates;
        }
        else
        {
            return false;
        }
    }
}
