
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <link rel="stylesheet" type="text/css" href="css/int.css" />
    <script type="text/javascript" src="js/func.js"></script>
    <style type="text/css">
        td{
            height:30px;
            vertical-align:middle;
            align:center;
        }
        #myText{
            width:600px;
        }
    </style>
    <title>注册页面</title>
</head>
<body >
<?php
/*
error_reporting(0);
//不让PHP报告有错语发生。如果不关闭好有类似这的错语 Warning: preg_match() 关闭就不出现了 
session_start();
header("Cache-control: private");
$conn = @ mysql_connect("localhost","root","mmeizhen")or die("数据库连接错误");
mysql_select_db("bbs",$conn);
mysql_query("set names utf8");
if($_POST['submit'])
{
    $username = $_POST["username"];
    $sql="select userName from user_info where userName='$username'";
// echo $sql; 
    $query=mysql_query($sql);
    $rows = mysql_num_rows($query);
    if($rows > 0){
        echo "<script type='text/javascript'>alert('用户名已存在');location='javascript:history.back()';</script>";
    }else{
        $user_in = "insert into user_info (username,pass,sex,qq,email,img) values ('$_POST[username]',md5('$_POST[pass]'),'$_POST[sex]','$_POST[qq]','$_POST[email]','$_POST[img_select]')";
//echo $user_in; 
        mysql_query($user_in);
        echo "<script type='text/javascript'>alert('写入成功！！');location.href='login.php';</script>";
    }
//javascript:history.go(-1) 
}*/
?>
<form action="reg.php" name="reg_form" method="post" onsubmit="return check_reg()">
    <table name="reg_table" align="left">
        <tr>
            <td>用户：</td><td><input id="username" name="username" class="myText" type="text" maxlength="12" /></td>
        </tr>
        <tr> <!--性别：0 保密 1 女 2 男-->
            <td > 性别：</td>
            <td>女<input type="radio" value="1" name="sex"/>
                男<input type="radio" value="2" name="sex" />
                保密<input type="radio" value="0" name="sex" checked/></td>
        </tr>
        <tr>
            <td>密码：</td><td><input name="pass" class="myText" type="password" onblur="check_len(this)"/><span id="show_pass" style="color:red;"></span></td>
        </tr>
        <tr>
            <td>重复密码：</td><td><input name="repass" class="myText" type="password" onblur="check_pass(this)" /><span id="show_repass" style="color:red;"></span></td>
        </tr>
        <tr>
            <td>QQ：</td><td><input type="text" class="myText" name="qq" onblur="check_qq(this)"/><span style="color:red;" id="show_qq"></span></td>
        </tr>
        <tr>
            <td>邮箱：</td><td><input type="text" class="myText" name="email" onblur="check_email(this)"/><span id="show_e" style="color:red;"></span></td>
        </tr>
        <tr>
            <td height="60">头像：</td>
            <td>
                <select name="img_select" onchange="img_change(this)">
                    <option value="101" >女 001</option>
                    <option value="102" >女 002</option>
                    <option value="103" >女 003</option>
                    <option value="104" >女 004</option>
                    <option value="105" >男 001</option>
                    <option value="106" >男 002</option>
                    <option value="107" >男 003</option>
                    <option value="108" >男 004</option>
                </select>
                <img src="/bbs/img/101.gif" id="tx_change" style="width:50px; height:65px;" alt=""/>
            </td>
        </tr>
        <tr height="20" align="justify">
            <td align="right" ><input type="submit" value="注册" name="submit" style="margin-right:5px;"/></td>
            <td><input type="reset" value="重置" name="reset" style="margin-left:5px;"/></td>
        </tr>
        <tr>
            <td colspan="2">我已有账号现在<a href="login.php">登录</a></td>
        </tr>
    </table>
</form>
</body>
</html> 
