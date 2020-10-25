<?php
error_reporting(0);
header("Content-Type: text/html;charset=utf-8");
$jianche=1;
$erro="";

$go = !empty($_GET['go']) ? $_GET['go'] : null; 

$str_s=include('../config.php');
$showprefix=$str_s['DB_PREFIX'];
if($go==1) {$str_s['M_INSTALL']=0;file_put_contents('../config.php', '<?php return '.var_export($str_s, true).';');}
if($str_s['M_INSTALL']=='1'){
    echo "<p style='font-size:12px;'>本系统已经安装！请<a href='../'>浏览网站</a>或'<a href='index.php?go=1'>重新安装</a>'！</p>";
    die;
}
function file_info($file){
	if (DIRECTORY_SEPARATOR == '/' and @ini_get("safe_mode") == FALSE){
		return is_writable($file);
	}
	if (is_dir($file)){
		$file = rtrim($file, '/').'/is_writable.html';
		if (($fp = @fopen($file,'w+')) === FALSE){
			return FALSE;
		}
  		fclose($fp);
  		@chmod($file,0755);
  		@unlink($file);
		return TRUE;
	}else if ( ! is_file($file) or ($fp = @fopen($file, 'r+')) === FALSE){
		return FALSE;
	}
	fclose($fp);
	return TRUE;
}

//$go = !empty($_GET['go']) ? $_GET['go'] : null; 
if($go==2){
    credatadb(); 
}
function credatadb(){
     global $erro;
     $con = mysql_connect($_POST['DB_HOST'],$_POST['DB_USER'],$_POST['DB_PWD']);
        if(!$con)
        {
          $erro='数据库连接失败，请检查数据库信息输入是否正确: ' . mysql_error();
          return;
        }
     mysql_query('CREATE DATABASE IF NOT EXISTS '.$_POST['DB_NAME'].' default charset utf8',$con);
    	if(!mysql_select_db($_POST['DB_NAME'], $con)){
    		$erro='数据库连接失败，请检查数据库信息输入是否正确：'.mysql_error();
            return;
    	}
        
     $mysqlv=mysql_get_server_info();
    	if(substr($mysqlv,0,1)<5){
    		$erro='您的数据库版本过低，Mysql版本要求大于等于5)';
            return;
    	};
        
        mysql_query('SET NAMES UTF8',$con);
	    mysql_query('set sql_mode=""',$con);
        
        //配置文件修改
        $ddata=include('../config.php');
        $yprefix=$ddata['DB_PREFIX'];
        $ddata['DB_HOST']=$_POST['DB_HOST'];
        $ddata['DB_USER']=$_POST['DB_USER'];
        $ddata['DB_PWD']=$_POST['DB_PWD'];
        $ddata['DB_PWD']=$_POST['DB_PWD'];
        $ddata['DB_NAME']=$_POST['DB_NAME'];
        $ddata['DB_PREFIX']=$_POST['DB_PREFIX'];
        $ddata['M_INSTALL']=1;
        file_put_contents('../config.php', '<?php return '.var_export($ddata, true).';');
        //删除所有表
        $dbname=$_POST['DB_NAME'];
        $result = mysql_query("show table status from $dbname");
        while($data=mysql_fetch_array($result)) {
            mysql_query("drop table $data[Name]");
        }
        
        include('./sql.php');
        if($_POST['backup'])
            traverse($_POST['backup'],$yprefix,$ddata['DB_PREFIX']);
        else
            traverse('sql','wb_',$ddata['DB_PREFIX']);
        
        mysql_close($con);
        echo "<p style='font-size:12px;'>安装完成！<a href='../'>浏览网站<a></p>";
        die;
}
?>
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' /> 
    <title>安装</title>
    <link href="install.css" rel="stylesheet" type="text/css"/>
    <script src="../Lib/Public/jquery-1.7.2.min.js"></script>
</head>
<body>
<div class="head">
    <img style="float:left;margin-top:10px;" src="login.png"/>
    <span style="float: left;margin-top:20px;font-size:14px;font-weight:bold;color:#fff;">安装</span>
    <div style="clear: both;"></div>
    <script>
        function jianche(){
            if($('#DB_HOST').val()=='')
            {
                alert('输入数据库地址！');
                return false;
            }
            if($('#DB_USER').val()=='')
            {
                alert('输入用户名！');
                return false;
            }
            if($('#DB_NAME').val()=='')
            {
                alert('输入数据库！');
                return false;
            }
            if($('#DB_PREFIX').val()=='')
            {
                alert('输入表前缀！');
                return false;
            }
            return true;
        }
    </script>
</div>
<div class="install">
<form action="index.php?go=2" method="post" onsubmit="return jianche()">
    <table border="0" cellpadding="0" cellspacing="0">
        <tr>
            <th colspan="3">填写数据库信息</th>
        </tr>
        <tr>
            <td width="15%" style="text-align:right;">数据库地址：</td>
            <td width="20%"><input type="text" id="DB_HOST" name="DB_HOST" value="<?php echo $_POST['DB_HOST'];?>"/></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td width="15%" style="text-align:right;">用户名：</td>
            <td width="20%"><input type="text" id="DB_USER" name="DB_USER" value="<?php echo $_POST['DB_USER'];?>"/></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td width="15%" style="text-align:right;">密码：</td>
            <td width="20%"><input type="text" name="DB_PWD"/></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td width="15%" style="text-align:right;">数据库：</td>
            <td width="20%"><input type="text" id="DB_NAME" name="DB_NAME" value="<?php echo $_POST['DB_NAME'];?>"/></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td width="15%" style="text-align:right;">表前缀：</td>
            <td width="20%"><input type="text" value="<?php echo $showprefix;?>" id="DB_PREFIX" name="DB_PREFIX" value="<?php echo $_POST['DB_PREFIX'];?>"/></td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <th colspan="3" style="border-top: 0px;">还原安装</th>
        </tr>
        <tr>
            <td width="15%" style="text-align:right;">选择安装文件：</td>
            <td width="20%">
                <select name="backup">
                    <option value="0">全新安装</option>
                    <?php
                        $dh=opendir('../Uploads/Backup');
                          while ($file=readdir($dh)) {
                            if($file!="." && $file!="..") {
                              $fullpath=$dir."/".$file;
                              if(!is_dir($fullpath)) {
                                  echo "<option value='../Uploads/Backup$fullpath'>$file</option>";
                              } 
                            }
                          }
                          closedir($dh);
                    ?>
                </select>
            </td>
            <td>&nbsp;如果想全新安装，请保持默认设置</td>
        </tr>
        <tr>
            <th colspan="3" style="border-top: 0px;">环境检测</th>
        </tr>
        <tr>
            <td width="15%" style="text-align:right;">php版本：</td>
            <td width="20%">
                 <?php 
                    if(substr(phpversion(),0,1)>=5&&substr(phpversion(),2,1)>=2)
                        echo '<span style="color: green;">'.phpversion().'&emsp;通过检测</span>';
                    else
                    {
                        $jianche=0;
                        echo '<span style="color:red">'.phpversion().'&emsp;不可写</span>';
                    }
                ?>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td width="15%" rowSpan="4" style="text-align:right;">目录权限：</td>
            <td width="20%">
                <?php 
                    if(file_info('../Runtime'))
                        echo '<span style="color: green;">/Runtime&emsp;通过</span>';
                    else
                    {
                        $jianche=0;
                        echo '<span style="color:red">/Runtime&emsp;不可写</span>';
                    }
                ?>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td width="20%">
                <?php 
                    if(file_info('../Uploads'))
                        echo '<span style="color: green;">/Uploads&emsp;通过</span>';
                    else{
                        $jianche=0;
                        echo '<span style="color:red">/Uploads&emsp;不可写</span>';
                    }
                ?>
            </td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td width="20%">
                 <?php 
                    if(file_info('../config.php'))
                        echo '<span style="color: green;">/config.php&emsp;通过</span>';
                    else
                    {
                        $jianche=0;
                        echo '<span style="color:red">/config.php&emsp;不可写</span>';
                    }
                ?>
            </td>
            <td>&nbsp;</td>
        </tr>
         <tr>
            <td width="20%">
                 <?php 
                    if(file_info('../Uploads/Backup'))
                        echo '<span style="color: green;">/Uploads/Backup&emsp;通过</span>';
                    else
                    {
                        $jianche=0;
                        echo '<span style="color:red">/Uploads/Backup&emsp;不可写</span>';
                    }
                ?>
            </td>
            <td>&nbsp;</td>
        </tr>
    </table>
    <?php 
        echo "<div style='width:1000px;height:30px;margin:0 auto;font-size:12px;color:red;'>".$erro."</div>";
        if(!$jianche)
            echo "<span style='font-size:12px'>请处理完所有错误！</span>";
        else
            echo "<input type='submit' class='button-b' value='安装'/>";
    ?>
</form>
</div>

</body>
</html>