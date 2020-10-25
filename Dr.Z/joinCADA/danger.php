<?php
$dbname='members.db';
$mytable ="member";

if(!class_exists('SQLite3'))
   die("SQLite 3 NOT supported.");

$base=new SQLite3($dbname, 0666); 

$query = "SELECT ID, NAME, GRADE, SCHOOL, GENDER, PHONE, MAIL, FAVOR, WORK FROM $mytable";
$results = $base->query($query);


?>

<!DOCTYPE html>
<html lang="zh_cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CADA Registering">
    <meta name="author" content="Z4Tech">

    <title>CADA报名名单</title>

    <link href="./css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index">CADA报名</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="index">报名</a></li>
            <li class="active"><a href="show">列表</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="main">
          <h2 class="sub-header">报名列表</h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>姓名</th>
                  <th>院系</th>
                  <th>年级</th>
                  <th>电话</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
<?php

while($arr = $results->fetchArray())
{
  if(count($arr) == 0) break;

  echo "<tr>\n";  

  $id = $arr['ID'];
  $name = $arr['NAME'];
  $school = $arr['SCHOOL']; 
  $grade = $arr['GRADE'];
  $phone = $arr['PHONE'];

  echo "<tr>";
  echo "<td>$name</td>";
  echo "<td>$school</td>";
  echo "<td>$grade</td>";
  echo "<td>$phone</td>";
  echo "<td><a type='button' href='delete.php?id=$id' class='btn btn-default btn-sm'><span class='glyphicon glyphicon-remove'></span></a></td>";
  echo "</tr>";

} 

?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="./js/jquery.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/docs.min.js"></script>
  </body>
</html>
