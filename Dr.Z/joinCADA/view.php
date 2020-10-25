<?php

$myid = $_GET['id'];

$dbname='members.db';
$mytable ="member";

if(!class_exists('SQLite3'))
   die("SQLite 3 NOT supported.");

$base=new SQLite3($dbname, 0666); 


$query = "SELECT ID, NAME, GRADE, SCHOOL, GENDER, PHONE, MAIL, FAVOR, WORK 
          FROM $mytable WHERE (id=$myid)";
$results = $base->query($query);
$row = $results->fetchArray();

// $query = "CREATE TABLE $mytable(
//             ID bigint(20) NOT NULL PRIMARY KEY,
//             NAME text NOT NULL,
//             GRADE text NOT NULL,         
//             SCHOOL text NOT NULL,
//             GENDER integer NOT NULL,
//             PHONE text NOT NULL,
//             MAIL text,
//             FAVOR text,
//             WORK integer NOT NULL      
//             )";

$id = $row['ID'];
$name = $row['NAME'];
$grade = $row['GRADE'];
$school = $row['SCHOOL'];
$gender = $row['GENDER'];
$phone = $row['PHONE'];
$mail = $row['MAIL'];
$favor = $row['FAVOR'];
$work = $row['WORK'];

$text_gender = ($gender==0) ? "男":"女";
$text_work = ($work==0)? "否":"是";

?>

<!DOCTYPE html>
<html lang="zh_cn">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CADA Registering">
    <meta name="author" content="Z4Tech">

    <title><?="CADA会员 - ".$name ?></title>

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

              <tbody>
              	<tr>
					<td>学号</td>
					<td><?= $id ?></td>
				</tr>
				<tr>
					<td>姓名</td>
					<td><?= $name ?></td>
				</tr>
				<tr>
					<td>年级</td>
					<td><?= $grade ?></td>
				</tr>
				<tr>
					<td>学院</td>
					<td><?= $school ?></td>
				</tr>
				<tr>
					<td>性别</td>
					<td><?= $text_gender ?></td>
				</tr>
				<tr>
					<td>手机</td>
					<td><?= $phone ?></td>
				</tr>
				<tr>
					<td>邮箱</td>
					<td><?= $mail ?></td>
				</tr>
				<tr>
					<td>兴趣</td>
					<td><?= $favor ?></td>
				</tr>
				<tr>
					<td>是否骨干</td>
					<td><?= $text_work ?></td>
				</tr>
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
