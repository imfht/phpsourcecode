<?php
/*******************************************************************************************
    phpMySQLAutoBackup  -  Author:  http://www.DWalker.co.uk - released under GPL License
           For support and help please try the forum at: http://www.dwalker.co.uk/forum/
********************************************************************************************
Version    Date              Comment
0.2.0      7th July 2005     GPL release
0.3.0      June 2006  Upgrade - added ability to backup separate tables
0.4.0      Dec 2006   removed bugs/improved code
1.4.0      Dec 2007   improved faster version
1.5.0      Dec 2008   improved and added FTP backup to remote site
1.5.4      Nov 2009   version added to xmail
1.5.5      Feb 2011  more options for config added - email reports only and/or backup, save backup file to local and/or remote server.
                                Reporter added: email report of last 6 (or more) backup stats (date, total bytes exported, total lines exported) plus any errors
                                MySQL error reporting added  and Automated version checker added
1.6.0      Dec 2011  PDO version
1.6.2      Sept 2012 - updated newline to constant:  NEWLINE
********************************************************************************************/
$phpMySQLAutoBackup_version="1.6.2";
// ---------------------------------------------------------
if(($db=="")OR($mysql_username=="")OR($mysql_password==""))
{
 echo "Configure your installation BEFORE running, add your details to the file /phpmysqlautobackup/run.php";
 exit;
}

$errors="";
include(LOCATION."phpmysqlautobackup_extras.php");
include(LOCATION."schema_for_export.php");

// zip the backup and email it
$backup_file_name = 'mysql'.strftime("%Y%B%dTIME%H%M%S.sql",time()).'.gz';
$dump_buffer = gzencode($buffer);

if ($save_backup_zip_file_to_server) write_backup($dump_buffer, $backup_file_name);

if (DEBUG) echo '备份完成';
?>