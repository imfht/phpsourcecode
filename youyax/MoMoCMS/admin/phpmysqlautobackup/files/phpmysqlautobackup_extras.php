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
1.5.4      Nov 2009   Version printed in email
1.5.5      Feb 2011  more options for config added - email reports only and/or backup, save backup file to local and/or remote server.
                                Reporter added: email report of last 6 (or more) backup stats (date, total bytes exported, total lines exported) plus any errors
                                MySQL error reporting added  and Automated version checker added
1.6.0      Dec 2011  PDO version
1.6.1      April 2012 - CURLOPT_TRANSFERTEXT turned off (to stop garbaging zip file on transfer) and bug removed from write_back
1.6.2      Sept 2012 - updated newline to constant:  NEWLINE
1.6.3      Oct 2012 - corrected bug with CONSTRAINT and added CHARSET - bug fix code gratefully received from: vit.bares@gmail.com
********************************************************************************************/
$phpMySQLAutoBackup_version="1.6.3";
// ---------------------------------------------------------
function has_data($value)
{
 if (is_array($value)) return (sizeof($value) > 0)? true : false;
 else return (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) ? true : false;
}

function write_backup($gzdata, $backup_file_name)
{
 $fp = fopen(LOCATION."../backups/".$backup_file_name, "w");
 fwrite($fp, $gzdata);
 fclose($fp);
}

class dbc extends PDO
{
 protected static $instance;

 public function __construct()
 {   
   $options = array(PDO::ATTR_PERSISTENT => true,
   PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
   PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES ".CHARSET // this command will be executed during every connection to server - suggested by: vit.bares@gmail.com
   );
   try {
        $this->dbconn = new PDO(DBDRIVER.":host=".DBHOST.";port=".DBPORT.";dbname=".DBNAME,DBUSER,DBPASS,$options);
        return $this->dbconn;
       }
    catch (PDOException $e){ $this->reportDBError($e->getMessage()); }   
 }
 
 public function reportDBError($msg)
 {
  if (DEBUG) print_r('<div style="padding:10%;"><h3>'.nl2br($msg).'</h3></div>');
  else
  {
   if(!session_id()) session_start();
   $_SESSION['pmab_mysql_errors'] = NEWLINE.NEWLINE."MySQL error: ".$msg."\n";
  }

 }
 
 public static function instance()
 {
  if (!isset(self::$instance)) self::$instance = new self();
  return self::$instance;
 }

 public function prepare($query) {
  try { return $this->dbconn->prepare($query); }
   catch (PDOException $e){ $this->reportDBError($e->getMessage()); }   
 }      

 public function bindParam($query) {
  try { return $this->dbconn->bindParam($query); }
   catch (PDOException $e){ $this->reportDBError($e->getMessage()); }     
 }

 public function query($query) {
  try {
       if ($this->query($query)) return $this->fetchAll();
       else return 0;
      } 
   catch (PDOException $e){ $this->reportDBError($e->getMessage()."<hr>".$e->getTraceAsString()); } }

 public function execute($result) {//use for insert/update/delete
  try { if ($result->execute()) return $result; } 
   catch (PDOException $e){ $this->reportDBError($e->getMessage()."<hr>".$e->getTraceAsString()); }     
 }
 public function executeGetRows($result) {//use to retrieve rows of data
  try { 
       if ($result->execute()) return $result->fetchAll(PDO::FETCH_ASSOC);
       else return 0;
      }
    catch (PDOException $e){ $this->reportDBError($e->getMessage()."<hr>".$e->getTraceAsString()); }     
 }

 public function __clone()
 {  //not allowed
 }
 public function __destruct()
 {
  $this->dbconn = null;
 }
}