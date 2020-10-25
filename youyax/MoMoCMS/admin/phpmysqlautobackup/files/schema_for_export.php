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
1.5.1      Feb 2009   improved export data - added quotes around field names
1.5.2      April 2009 improved "create table" export and added backup time start & end
1.5.3      Nov 2009   replaced PHP function "ereg_replace" with "str_replace" - all occurances
1.5.4      Nov 2009   replaced PHP function "str_replace" with "substr" line 114
1.5.5      Feb 2011  more options for config added - email reports only and/or backup, save backup file to local and/or remote server.
                                Reporter added: email report of last 6 (or more) backup stats (date, total bytes exported, total lines exported) plus any errors
                                MySQL error reporting added  and Automated version checker added
1.6.0      Dec 2011  PDO version
1.6.1      April 2012 - CURLOPT_TRANSFERTEXT turned off (to stop garbaging zip file on transfer) and bug removed from write_back
1.6.2      Setp 2012 - corrected issue with CONSTRAINT and FOREIGN KEYS, related to InnoDB functionality/restores
1.6.3      Oct 2012 - corrected bug with CONSTRAINT and added CHARSET - bug fix code gratefully received from: vit.bares@gmail.com
********************************************************************************************/
$phpMySQLAutoBackup_version="1.6.3";
// ---------------------------------------------------------
$dbc = dbc::instance();

if (!isset($table_select))
{
  $result = $dbc->prepare("show tables");
  $i=0;
  $table="";
  $tables = $dbc->executeGetRows($result);
  foreach ($tables as $table_array)
  {
   list(,$table) = each($table_array);
   $exclude_this_table = isset($table_exclude)? in_array($table, $table_exclude) : false;
   if(!$exclude_this_table) $table_select[$i]=$table;
   $i++;
   //echo "$table<br>";
  }
}

$thedomain = $_SERVER['HTTP_HOST'];
if (substr($thedomain,0,4)=="www.") $thedomain=substr($thedomain,4,strlen($thedomain));

$buffer = '# MySQL backup created by phpMySQLAutoBackup - Version: '.$phpMySQLAutoBackup_version . NEWLINE .
          '# ' . NEWLINE .
          '# http://www.dwalker.co.uk/phpmysqlautobackup/' . NEWLINE .
          '#' . NEWLINE .
          '# Database: '. $db . NEWLINE .
          '# Domain name: ' . $thedomain . NEWLINE .
          '# (c)' . date('Y') . ' ' . $thedomain . NEWLINE .
          '#' . NEWLINE .
          '# Backup START time: ' . strftime("%H:%M:%S",time()) . NEWLINE.
          '# Backup END time: #phpmysqlautobackup-endtime#' . NEWLINE.
          '# Backup Date: ' . strftime("%d %b %Y",time()) . NEWLINE;

$i=0;
$lines_exported=0;
$alter_tables="";
foreach ($table_select as $table)
        {
          $i++;
          $export = ' '.NEWLINE.'drop table if exists `' . $table . '`; ' . NEWLINE;

          //export the structure
          $query='SHOW CREATE TABLE `' . $table . '`';
          $result = $dbc->prepare($query);
          $tables = $dbc->executeGetRows($result);
          $this_table=$tables[0]['Create Table'];
          //$export.= print_r($tables) ."; \n";
          $alter_table="";          
          if (preg_match('@^[\s]*CONSTRAINT|FOREIGN[\s]+KEY@',$this_table))
          {
           // change line end char to NEWLINE
           if (strpos($this_table, "(\r\n ")) $this_table = str_replace("\r\n", NEWLINE, $this_table);
           elseif (strpos($this_table, "(\n ")) $this_table = str_replace("\n", NEWLINE, $this_table);
           elseif (strpos($this_table, "(\r ")) $this_table = str_replace("\r", NEWLINE, $this_table);

           $sql_lines = explode(NEWLINE, $this_table);
           $sql_count = count($sql_lines);
           // find constraints
           for ($j = 0; $j < $sql_count; $j++)
           {
            if (preg_match('@^[\s]*(CONSTRAINT|FOREIGN[\s]+KEY)@', $sql_lines[$j]) === 1)
               {
               //the following was gratefully received from: vit.bares@gmail.com
               // if more than one constraint in table, we would have ADD CONSTRAINT command ending with ","
               // which is SQL syntax error
               $sql_lines[$j] = str_replace(',', '', $sql_lines[$j]);              
               $alter_table.= 'ALTER TABLE `' . $table . '` ADD ' . $sql_lines[$j] . ';' . NEWLINE;
               
               // if more than one constraint in table, replace rule with comma does not work for at least one constraint
               $needles = array(
                   "," . NEWLINE . $sql_lines[$j],
                   NEWLINE . $sql_lines[$j]
               );
               //the above was gratefully received from: vit.bares@gmail.com
               $this_table = str_replace($needles, "", $this_table);                        
            }
           }
           $alter_tables.=NEWLINE.$alter_table;
          }
          $export.= $this_table.';'.NEWLINE;
          
          $table_list = array();
          $result = $dbc->prepare('show fields from  `' . $table . '`');
          $fields = $dbc->executeGetRows($result);
          foreach ($fields as $field_array) $table_list[] = $field_array['Field'];           

          $buffer.=$export;
          // dump the data
          $query='select * from `' . $table . '` LIMIT '. $limit_from .', '. $limit_to.' ';
          $result = $dbc->prepare($query);
          $rows = $dbc->executeGetRows($result);
          foreach ($rows as $row_array)
          {
            $export = 'insert into `' . $table . '` (`' . implode('`, `', $table_list) . '`) values (';
            $lines_exported++;
            reset($table_list);
            while (list(,$i) = each($table_list))
            {
              if (!isset($row_array[$i])) $export .= 'NULL, ';
              elseif (has_data($row_array[$i]))
              {
                $row = addslashes($row_array[$i]);
                $row = str_replace("\n#", "\n".'\#', $row);
                $export .= '\'' . $row . '\', ';
              }
              else $export .= '\'\', ';
            }
            $export = substr($export,0,-2) . ");".NEWLINE;
            $buffer.= $export;
          }
        }
//uncomment line below to show table dumps, inc insert and alter table statements:
//exit('<textarea rows="30" name="themessage" cols="100">'.$buffer.$alter_tables.'</textarea>');

$buffer.=$alter_tables;        
$buffer = str_replace('#phpmysqlautobackup-endtime#', strftime("%H:%M:%S",time()), $buffer);