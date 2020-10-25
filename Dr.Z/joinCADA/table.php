<?php
$dbname='members.db';
$mytable ="member";

if(!class_exists('SQLite3'))
   die("SQLite 3 NOT supported.");

$base=new SQLite3($dbname, 0666); 

$query = "SELECT ID, NAME, GRADE, SCHOOL, GENDER, PHONE, MAIL, FAVOR, WORK FROM $mytable";
$results = $base->query($query);

while($arr = $results->fetchArray())
{
  if(count($arr) == 0) break;

  echo "<tr>\n";  

  $id = $arr['ID'];
  $name = $arr['NAME'];
  $grade = $arr['GRADE'];
  $school = $arr['SCHOOL'];
  $gender = $arr['GENDER'];
  $phone = $arr['PHONE'];
  $mail = $arr['MAIL'];
  $favor = $arr['FAVOR'];
  $work = $arr['WORK'];

  $text_gender = ($gender==0) ? "男":"女";
  $text_work = ($work==0)? "否":"是";

  echo $id.",".$name.",".$grade.",".$school.",".$text_gender.",".$phone.",".$mail.", \"".$favor."\",".$text_work.",";
  echo "<br/>";
} 



?>

